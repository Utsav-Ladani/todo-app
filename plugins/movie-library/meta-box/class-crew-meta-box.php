<?php
/**
 * Crew Meta Box. It contains the meta box for the crew like director, producer, writer, and actor.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\Custom_Post_Type\Person;
use Movie_Library\Shadow_Taxonomy\Non_Hierarchical\Shadow_Person;
use Movie_Library\Taxonomy\Hierarchical\Career;

/**
 * Class Crew_Meta_Box
 */
abstract class Crew_Meta_Box {
	/**
	 * Crew data. It used to avoid the code duplication.
	 *
	 * @var array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static $crew_data = array(
		array(
			'name' => 'Director',
			'type' => 'director',
			'id'   => 'rt-movie-meta-crew-director',
		),
		array(
			'name' => 'Producer',
			'type' => 'producer',
			'id'   => 'rt-movie-meta-crew-producer',
		),
		array(
			'name' => 'Writer',
			'type' => 'writer',
			'id'   => 'rt-movie-meta-crew-writer',
		),
		array(
			'name' => 'Actor',
			'type' => 'actor',
			'id'   => 'rt-movie-meta-crew-actor',
		),
	);

	/**
	 * Initialize the class.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function init() : void {
		// Add meta box.
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_crew_meta_box' ) );

		// Save meta box data on update post.
		add_action( 'save_post_rt-movie', array( __CLASS__, 'save_crew_meta_data' ) );

		// Enqueue script.
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_script' ) );
	}

	/**
	 * Add crew meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_crew_meta_box() : void {
		// Add meta box.
		add_meta_box(
			'rt-movie-meta-crew',
			__( 'Crew', 'movie-library' ),
			array( __CLASS__, 'render_crew_meta_box' ),
			Movie::SLUG,
			'side',
		);
	}

	/**
	 * Enqueue character name handler script.
	 *
	 * @return void
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function enqueue_script() : void {
		// only enqueue script on rt-movie post type.
		if ( Movie::SLUG !== get_post_type() || ! is_admin() ) {
			return;
		}

		wp_enqueue_script(
			'movie-library-character-name-handler',
			MOVIE_LIBRARY_PLUGIN_URL . 'admin/js/character-name-handler.js',
			array( 'jquery', 'wp-i18n' ),
			filemtime( MOVIE_LIBRARY_PLUGIN_DIR . 'admin/js/character-name-handler.js' ),
			true
		);
	}

	/**
	 * Render crew meta box.
	 *
	 * @param \WP_Post $post Post object.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_crew_meta_box( \WP_Post $post ) : void {
		// loop over the crew data and render the meta box for each section.
		foreach ( self::$crew_data as $crew ) {
			self::render_crew_meta_box_section( $post->ID, $crew );
		}
	}

	/**
	 * Render crew meta box section for particular type of user.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $crew Crew data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function render_crew_meta_box_section( int $post_id, array $crew ) : void {
		// Get all the persons with the given crew type.
		$all_crew_member = self::list_persons_with_career( $crew['type'] );

		// get the selected crew members for the given crew type.
		$selected_crew_member = get_post_meta( $post_id, $crew['id'], true );

		// if the selected crew member is not an array, then make it an array.
		if ( ! is_array( $selected_crew_member ) ) {
			$selected_crew_member = array();
		}

		// if the crew type is actor, then we need to get the character name also.
		$character_name = array();
		if ( 'actor' === $crew['type'] ) {
			$character_name       = $selected_crew_member;
			$selected_crew_member = array_keys( $selected_crew_member );
		}

		$selected_crew_member = array_map(
			function ( $value ) {
				return (int) $value;
			},
			$selected_crew_member
		);

		// add nonce field.
		wp_nonce_field( 'rt-movie-meta-crew', 'rt-movie-meta-crew-nonce' );

		// Render the meta box section.
		?>
		<label for='<?php echo esc_attr( $crew['id'] ); ?>' >
			<?php echo esc_html( $crew['name'] ); ?>
			<?php echo esc_html__( '(Press CTRL to select multiple)', 'movie-library' ); ?>
		</label>
		<br />
		<select
			name='<?php echo esc_attr( $crew['id'] ); ?>[]'
			id='<?php echo esc_attr( $crew['id'] ); ?>'
			size='3'
			class='widefat'
			autocomplete='off'
			multiple
		>
			<?php

			foreach ( $all_crew_member as $crew_member ) {
				?>
				<option
					value='<?php echo esc_attr( $crew_member['id'] ); ?>'
					<?php
						// if the crew member is selected, then mark it as selected.
						echo in_array( (int) $crew_member['id'], $selected_crew_member, true ) ? 'selected' : '';
					?>
				>
					<?php echo esc_html( $crew_member['name'] ); ?>
				</option>
				<?php
			}
			?>
		</select>
		<br />
		<?php

		// if the crew type is actor, then we need to render the character name input.
		if ( 'actor' === $crew['type'] ) {
			?>
			<input
				type='hidden'
				name='rt-movie-meta-crew-actor-character-name'
				id='rt-movie-meta-crew-actor-character-name'
				value='<?php echo esc_attr( wp_json_encode( $character_name ) ); ?>'
				autocomplete='off'
			/>
			<div id='rt-characters-name'>

			</div>
			<?php
		}
	}

	/**
	 * List all the persons with the given career.
	 *
	 * @param string $career Career name.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function list_persons_with_career( string $career ) : array {
		// get the person from the database.
		$person_query = new \WP_Query(
			array(
				'post_type'      => Person::SLUG,
				'orderby'        => 'name',
				'order'          => 'ASC',
				'posts_per_page' => -1,
				// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				'tax_query'      => array(
					array(
						'taxonomy' => Career::SLUG,
						'field'    => 'slug',
						'terms'    => $career,
					),
				),
			)
		);

		$persons = array();

		// loop over the persons and get the name and id.
		foreach ( $person_query->posts as $person_post ) {
			$persons[] = array(
				'id'   => $person_post->ID,
				'name' => $person_post->post_title,
			);
		}

		// reset the global post pointer.
		wp_reset_postdata();

		return $persons;
	}

	/**
	 * Save the crew metadata.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function save_crew_meta_data( int $post_id ) : void {
		// check if the request type is POST or not.
		if ( ! isset( $_POST ) ) {
			return;
		}

		// check if the current user has the permission to edit the post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// avoid the autosave and revision.
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$nonce = filter_input( INPUT_POST, 'rt-movie-meta-crew-nonce', FILTER_DEFAULT );

		// check if the nonce is set or not and verify it.
		if ( ! wp_verify_nonce( $nonce, 'rt-movie-meta-crew' ) ) {
			return;
		}

		// unlink all the existing crew members temp-relationships.
		wp_delete_object_term_relationships( $post_id, Shadow_Person::SLUG );

		// loop over the crew data and save the metadata for each section.
		foreach ( self::$crew_data as $crew ) {
			self::save_crew_meta_data_section( $post_id, $crew );
		}
	}

	/**
	 * Save metadata for given crew and add the link to the temp-relationship between person and movie.
	 * Important function which use the shadow taxonomy.
	 *
	 * @param int   $post_id Post ID.
	 * @param array $crew Crew data.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function save_crew_meta_data_section( int $post_id, array $crew ) : void {
		// get the selected crew members for the given crew type.
		$selected_crew_member = filter_input(
			INPUT_POST,
			$crew['id'],
			FILTER_DEFAULT,
			FILTER_REQUIRE_ARRAY
		);

		// if the no member is selected then delete the metadata and return.
		if ( empty( $selected_crew_member ) || ! is_array( $selected_crew_member ) ) {
			delete_post_meta( $post_id, $crew['id'] );
			return;
		}

		$rt_person_arr = array();

		// loop over the selected crew members and create the temp-relationship.
		foreach ( $selected_crew_member as $person ) {
			$rt_person_arr[] = sprintf( 'person-%d', (int) $person );
		}

		// if the crew type is actor, then we need to get the character name.
		if ( 'actor' === $crew['type'] ) {
			$selected_crew_member = self::get_character_name( $selected_crew_member );
		}

		// update the metadata.
		update_post_meta( $post_id, $crew['id'], $selected_crew_member );

		// add the temp-relationships.
		wp_add_object_terms( $post_id, $rt_person_arr, Shadow_Person::SLUG );
	}

	/**
	 * Get the character name for the given person.
	 *
	 * @param array $persons Person ID array.
	 *
	 * @return array
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function get_character_name( array $persons ) : array {
		// get the character name from the post request.
		$character_names = filter_input(
			INPUT_POST,
			'rt-movie-meta-crew-actor-character-name',
		);

		// decode the response.
		$character_names = json_decode( $character_names, true );

		$data = array();

		// loop over the persons and add the character name if exists.
		foreach ( $persons as $person ) {
			// validate and sanitize the character name.
			$name = $character_names[ $person ];
			$name = trim( $name );

			if ( '' !== $name &&
				preg_match( '/^[a-zA-Z0-9 ]+$/', $name ) &&
				strlen( $name ) < 24
			) {
				$data[ $person ] = $name;
			} else {
				$data[ $person ] = '';
			}
		}

		return $data;
	}
}
