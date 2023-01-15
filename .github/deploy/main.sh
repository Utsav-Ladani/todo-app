#!/usr/bin/env bash

set -ex

hosts_file="$GITHUB_WORKSPACE/.github/hosts.yml"
export PATH="$PATH:$COMPOSER_HOME/vendor/bin"
export PROJECT_ROOT="$(pwd)"
export HTDOCS="$HOME/htdocs"
export GITHUB_BRANCH=${GITHUB_REF##*heads/}
export CI_SCRIPT_OPTIONS="ci_script_options"


function init_checks() {

	# Check if branch is available
	if [[ "$GITHUB_REF" = "" ]]; then
		echo "\$GITHUB_REF is not set"
		exit 1
	fi

	if [[ "$GITHUB_BRANCH" == "watchtower/"* ]] || [[ "$GITHUB_BRANCH" == "plugin-update/"* ]] || [[ "$GITHUB_BRANCH" == "dependabot-"* ]]; then
		echo "Skipping deployment of automated branches."
		exit 0
	fi

	# Check for SSH key if jump host is defined
	if [[ ! -z "$JUMPHOST_SERVER" ]]; then
		
		if [[ -z "$SSH_PRIVATE_KEY" ]]; then
			echo "Jump host configuration does not work with vault ssh signing."
			echo "SSH_PRIVATE_KEY secret needs to be added."
			echo "The SSH key should have access to the server as well as jumphost."
			exit 1
		fi
	fi

	# Exit if branch deletion detected
	if [[ "true" == $(jq --raw-output .deleted "$GITHUB_EVENT_PATH") ]]; then
		echo 'Branch deletion trigger found. Skipping deployment.'
		exit 78
	fi

	[[ -z "$MULTI_DEV_ENV" ]] && export MULTI_DEV_ENV='multi_branch_env' || echo ''
}

function setup_hosts_file() {

	# Setup hosts file
	rsync -av "$hosts_file" /hosts.yml
	cat /hosts.yml
}

function check_branch_in_hosts_file() {

	match=0
	for branch in $(cat "$hosts_file" | shyaml keys); do
		[[ "$GITHUB_REF" = "refs/heads/$branch" ]] && \
		echo "$GITHUB_REF matches refs/heads/$branch" && \
		match=1
	done


	if [[ "true" == "$MULTI_BRANCH" ]]; then
		if [[ "$match" -eq 0 ]]; then
			export MAIN_SITE=$(cat "$hosts_file" | shyaml get-value "$MULTI_DEV_ENV.main_site")
			export SANITIZED_BRANCH=$(get_sanitized_name "$GITHUB_BRANCH")
			sed -i s/$MULTI_DEV_ENV/$SANITIZED_BRANCH/g $hosts_file
			export hostname=$(cat "$hosts_file" | shyaml get-value "$SANITIZED_BRANCH.hostname")
			export ssh_user=$(cat "$hosts_file" | shyaml get-value "$SANITIZED_BRANCH.user")
			export deploy_path=$(cat "$hosts_file" | shyaml get-value "$SANITIZED_BRANCH.deploy_path")
		fi
	elif [[ "$match" -eq 0 ]]; then
		echo "$GITHUB_REF does not match with any given branch in 'hosts.yml'"
		exit 78
	fi
}

function setup_private_key() {

	if [[ -n "$SSH_PRIVATE_KEY" ]]; then
	echo "$SSH_PRIVATE_KEY" | tr -d '\r' > "$SSH_DIR/id_rsa"
	chmod 600 "$SSH_DIR/id_rsa"
	eval "$(ssh-agent -s)"
	ssh-add "$SSH_DIR/id_rsa"

	if [[ -n "$JUMPHOST_SERVER" ]]; then
		ssh-keyscan -H "$JUMPHOST_SERVER" >> /etc/ssh/known_hosts 
	fi
	else
		# Generate a key-pair
		ssh-keygen -t rsa -b 4096 -C "GH-actions-ssh-deploy-key" -f "$HOME/.ssh/id_rsa" -N ""
	fi
}

function maybe_get_ssh_cert_from_vault() {

	# Get signed key from vault
	if [[ -n "$VAULT_GITHUB_TOKEN" ]]; then
		unset VAULT_TOKEN
		vault login -method=github token="$VAULT_GITHUB_TOKEN" > /dev/null
	fi

	if [[ -n "$VAULT_ADDR" ]]; then
		vault write -field=signed_key ssh-client-signer/sign/my-role public_key=@$HOME/.ssh/id_rsa.pub > $HOME/.ssh/signed-cert.pub
	fi
}

function configure_ssh_config() {

if [[ -z "$JUMPHOST_SERVER" ]]; then
	# Create ssh config file. `~/.ssh/config` does not work.
	cat > /etc/ssh/ssh_config <<EOL
Host $hostname
HostName $hostname
IdentityFile ${SSH_DIR}/signed-cert.pub
IdentityFile ${SSH_DIR}/id_rsa
User $ssh_user
EOL
else
	# Create ssh config file. `~/.ssh/config` does not work.
	cat > /etc/ssh/ssh_config <<EOL
Host jumphost
	HostName $JUMPHOST_SERVER
	UserKnownHostsFile /etc/ssh/known_hosts
	User $ssh_user

Host $hostname
	HostName $hostname
	ProxyJump jumphost
	UserKnownHostsFile /etc/ssh/known_hosts
	User $ssh_user
EOL
fi

}

function setup_ssh_access() {

	if [[ -z $MAIN_SITE ]]; then
		# get hostname and ssh user
		export hostname=$(cat "$hosts_file" | shyaml get-value "$GITHUB_BRANCH.hostname")
		export ssh_user=$(cat "$hosts_file" | shyaml get-value "$GITHUB_BRANCH.user")
		export deploy_path=$(cat "$hosts_file" | shyaml get-value "$GITHUB_BRANCH.deploy_path")
	fi
	printf "[\e[0;34mNOTICE\e[0m] Setting up SSH access to server.\n"

	SSH_DIR="$HOME/.ssh"
	mkdir -p "$SSH_DIR"
	chmod 700 "$SSH_DIR"

	setup_private_key
	maybe_get_ssh_cert_from_vault
	configure_ssh_config
}

function maybe_install_submodules() {

	# Check and update submodules if any
	if [[ -f "$GITHUB_WORKSPACE/.gitmodules" ]]; then
		# add github's public key
		echo "|1|qPmmP7LVZ7Qbpk7AylmkfR0FApQ=|WUy1WS3F4qcr3R5Sc728778goPw= ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==" >> /etc/ssh/known_hosts

		identity_file=''
		if [[ -n "$SUBMODULE_DEPLOY_KEY" ]]; then
			echo "$SUBMODULE_DEPLOY_KEY" | tr -d '\r' > "$SSH_DIR/submodule_deploy_key"
			chmod 600 "$SSH_DIR/submodule_deploy_key"
			ssh-add "$SSH_DIR/submodule_deploy_key"
			identity_file="IdentityFile ${SSH_DIR}/submodule_deploy_key"
		fi

	# Setup config file for proper git cloning
	cat >> /etc/ssh/ssh_config <<EOL
Host github.com
HostName github.com
User git
UserKnownHostsFile /etc/ssh/known_hosts
${identity_file}
EOL
	git submodule update --init --recursive
fi
}

function setup_wordpress_files_for_multi_branch() {

	mkdir -p "$HTDOCS"
	cd "$HTDOCS"
	export build_root="$(pwd)"

	WP_VERSION=${WP_VERSION:-"latest"}
	wp core download --version="$WP_VERSION" --allow-root

	rm -r wp-content/

	# Include webroot-files in htdocs if they exists
	if [[ -d "$GITHUB_WORKSPACE/webroot-files" ]]; then
		rsync -av  "$GITHUB_WORKSPACE/webroot-files/" "$HTDOCS/"  > /dev/null
		rm -rf "$GITHUB_WORKSPACE/webroot-files/"
	fi

	mkdir -p "$HTDOCS/wp-content/$SANITIZED_BRANCH"
	rsync -av  "$GITHUB_WORKSPACE/" "$HTDOCS/wp-content/$SANITIZED_BRANCH"  > /dev/null

	# Remove uploads directory
	cd "$HTDOCS/wp-content/$SANITIZED_BRANCH"

	# Symlink uploads
	rm -rf uploads
	ln -sn ../../../shared/wp-content/uploads .

	# Setup mu-plugins if VIP
	if [[ -n "$MU_PLUGINS_URL" ]]; then
		if [[ "$MU_PLUGINS_URL" = "vip" ]]; then
			MU_PLUGINS_URL="https://github.com/Automattic/vip-mu-plugins-public"
		fi
		MU_PLUGINS_DIR="$HTDOCS/wp-content/mu-plugins"
		echo "Cloning mu-plugins from: $MU_PLUGINS_URL"
		git clone -q --recursive --depth=1 "$MU_PLUGINS_URL" "$MU_PLUGINS_DIR"
	fi

	# Add host keys to known hosts
	ssh-keyscan -H "$hostname" >> /etc/ssh/known_hosts

	# Fix file permissions
	cd $HTDOCS
	find . -type f -exec chmod 0644 {} \;
	find . -type d -exec chmod 0755 {} \;

}

function setup_wordpress_files() {

	if [[ -n $MAIN_SITE ]]; then
		setup_wordpress_files_for_multi_branch
		return
	fi

	mkdir -p "$HTDOCS"
	cd "$HTDOCS"
	export build_root="$(pwd)"

	WP_VERSION=${WP_VERSION:-"latest"}
	wp core download --version="$WP_VERSION" --allow-root

	rm -r wp-content/

	# Include webroot-files in htdocs if they exists
	if [[ -d "$GITHUB_WORKSPACE/webroot-files" ]]; then
		rsync -av  "$GITHUB_WORKSPACE/webroot-files/" "$HTDOCS/"  > /dev/null
		rm -rf "$GITHUB_WORKSPACE/webroot-files/"
	fi

	rsync -av  "$GITHUB_WORKSPACE/" "$HTDOCS/wp-content/"  > /dev/null

	# Remove uploads directory
	cd "$HTDOCS/wp-content/"
	rm -rf uploads

	# Setup mu-plugins if VIP
	if [[ -n "$MU_PLUGINS_URL" ]]; then
		if [[ "$MU_PLUGINS_URL" = "vip" ]]; then
			MU_PLUGINS_URL="https://github.com/Automattic/vip-mu-plugins-public"
		fi
		MU_PLUGINS_DIR="$HTDOCS/wp-content/mu-plugins"
		echo "Cloning mu-plugins from: $MU_PLUGINS_URL"
		git clone -q --recursive --depth=1 "$MU_PLUGINS_URL" "$MU_PLUGINS_DIR"
	fi
}

#------------------------------------------------------------------------------
# Function to get a sanitized name for deployment subdomain.
#
# args:
# $1 - string to be sanitized
#------------------------------------------------------------------------------
function get_sanitized_name() {

	[[ -z "$1" ]] && echo"" || STRING="$1"
	replace_slash_with_dash=${STRING//\//\-}
	replace_dot_with_dash=${replace_slash_with_dash//./-}
	replace_underscore_with_dash=${replace_dot_with_dash//_/-}
	lower_case=$(echo "$replace_underscore_with_dash" | tr '[:upper:]' '[:lower:]')
	strip_hyphen_at_end=$(echo "$lower_case" | sed -e 's/\(-\)*$//g')
	echo "$strip_hyphen_at_end"
}

function deploy_for_multi_branch() {

	# Sync WordPress files
	rsync -e "ssh -o StrictHostKeyChecking=no" -avzh --exclude='.git' --exclude='wp-content' --delete "$HTDOCS/" "$ssh_user@$hostname:$deploy_path/current/"

	# Sync wp-content files
	ssh $ssh_user@$hostname -o StrictHostKeyChecking=no "mkdir -p $deploy_path/current/wp-content/$SANITIZED_BRANCH"
	rsync -e "ssh -o StrictHostKeyChecking=no" -avzh --exclude='.git' --delete "$HTDOCS/wp-content/$SANITIZED_BRANCH/" "$ssh_user@$hostname:$deploy_path/current/wp-content/$SANITIZED_BRANCH/"

	# Fix file permissions
	ssh $ssh_user@$hostname -o StrictHostKeyChecking=no "chown -R www-data: $deploy_path/current"

	# Update site name for notification
	if [[ "$GITHUB_BRANCH" != "master" ]]; then
		sed -i "s#$SANITIZED_BRANCH#$GITHUB_BRANCH#g" $hosts_file
		sed -i "s/$MAIN_SITE/$SANITIZED_BRANCH.$MAIN_SITE/g" $hosts_file
	fi
}

function deploy() {

	if [[ -n $MAIN_SITE ]]; then
		deploy_for_multi_branch
		return
	fi
	cd "$GITHUB_WORKSPACE"
	dep deploy "$GITHUB_BRANCH"
}

function main() {
	init_checks
	setup_hosts_file
	check_branch_in_hosts_file
	setup_ssh_access
	maybe_install_submodules
	setup_wordpress_files
	deploy
}

main