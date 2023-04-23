<?php
/**
 * Person REST API
 * It registers different routes and endpoints for the REST API.
 *
 * @package MovieLibrary
 */

namespace Movie_Library\REST_API;

use Movie_Library\Custom_Post_Type\Person;
use Movie_Library\APIs\Movie_Library_Metadata_API;
use Movie_Library\Taxonomy\Hierarchical\Career;
use Movie_Library\Taxonomy\Hierarchical\Genre;
use Movie_Library\Taxonomy\Hierarchical\Label;
use Movie_Library\Taxonomy\Hierarchical\Language;
use Movie_Library\Taxonomy\Hierarchical\Production_Company;

/**
 * Class Person_REST_API
 * It registers different routes and endpoints for the REST API.
 */
class Person_REST_API {
	/**
	 * Initialize the movie REST API.
	 *
	 * @return void
	 */
	public static function init() {
		// Register person routes.
		add_action( 'rest_api_init', array( __CLASS__, 'register_person_routes' ) );
	}

	/**
	 * Register person routes.
	 *
	 * @return void
	 */
	public static function register_person_routes() {
		/**
		 * Route: /wp-json/movie-library/v1/people
		 */
		register_rest_route(
			'movie-library/v1',
			'/people',
			array(
				/**
				 * Route: /wp-json/movie-library/v1/people
				 * Method: GET
				 */
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_people' ),
					'permission_callback' => array( __CLASS__, 'get_people_permissions_check' ),
					'args'                => Movie_REST_API::movies_get_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/people
				 * Method: POST
				 */
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'create_or_update_person' ),
					'permission_callback' => array( __CLASS__, 'create_or_update_person_permissions_check' ),
					'args'                => self::people_editable_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/people
				 * Method: DELETE
				 */
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'delete_person' ),
					'permission_callback' => array( __CLASS__, 'delete_person_permissions_check' ),
					'args'                => Movie_REST_API::movie_delete_route_args(),
				),
			),
		);

		/**
		 * Route: /wp-json/movie-library/v1/people/{id}
		 */
		register_rest_route(
			'movie-library/v1',
			'/people/(?P<id>\d+)',
			array(
				/**
				 * Route: /wp-json/movie-library/v1/people/{id}
				 * Method: GET
				 */
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_person' ),
					'permission_callback' => array( __CLASS__, 'get_people_permissions_check' ),
					'args'                => Movie_REST_API::movie_get_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/people/{id}
				 * Method: POST, PUT, PATCH
				 */
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'create_or_update_person' ),
					'permission_callback' => array( __CLASS__, 'create_or_update_person_permissions_check' ),
					'args'                => self::people_editable_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/person/{id}
				 * Method: DELETE
				 */
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'delete_person' ),
					'permission_callback' => array( __CLASS__, 'delete_person_permissions_check' ),
					'args'                => Movie_REST_API::movie_delete_route_args(),
				),
			),
		);
	}

	/**
	 * Get person.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response | \WP_Error
	 */
	public static function get_people( \WP_REST_Request $request ) {
		// prepare arguments.
		$args = array(
			'post_type'      => Person::SLUG,
			'posts_per_page' => $request->get_param( 'per_page' ) ?? 10,
			'paged'          => $request->get_param( 'page' ) ?? 1,
			'offset'         => $request->get_param( 'offset' ) ?? 0,
			'orderby'        => $request->get_param( 'orderby' ) ?? 'date',
			'order'          => $request->get_param( 'order' ) ?? 'DESC',
			'has_password'   => false,
		);

		// execute query.
		$people = get_posts( $args );

		if ( empty( $people ) ) {
			return new \WP_Error(
				'no_persons_found',
				__( 'No persons found.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// prepare response.
		$response = array();

		foreach ( $people as $person ) {
			$response[] = self::get_person_data( $person );
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Get person data.
	 *
	 * @param \WP_Post $person Person object.
	 *
	 * @return array
	 */
	public static function get_person_data( \WP_Post $person ) : array {
		// structure person data.
		return array(
			'id'             => $person->ID,
			'title'          => $person->post_title,
			'name'           => $person->post_name,
			'author'         => $person->post_author,
			'content'        => $person->post_content,
			'excerpt'        => $person->post_excerpt,
			'status'         => $person->post_status,
			'comment_status' => $person->comment_status,
			'modified'       => $person->post_modified,
			'published'      => $person->post_date,
			'link'           => $person->guid,
			'featured_image' => get_the_post_thumbnail_url( $person->ID, 'full' ),
			'meta'           => self::get_person_metas( $person->ID ),
			'taxonomy'       => self::get_person_taxonomies( $person->ID ),
		);
	}

	/**
	 * Get person post meta.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	public static function get_person_metas( int $post_id ) : array {
		// person meta keys.
		$meta_list = array(
			'rt-person-meta-basic-birth-place',
			'rt-person-meta-basic-birth-place',
			'rt-person-meta-basic-full-name',
			'rt-person-meta-social-twitter',
			'rt-person-meta-social-facebook',
			'rt-person-meta-social-instagram',
			'rt-person-meta-social-web',
			'rt-media-meta-videos',
			'rt-media-meta-images',

		);

		// prepare person meta.
		$meta = array();

		foreach ( $meta_list as $meta_key ) {
			// get meta using custom metadata API.
			$meta[ $meta_key ] = Movie_Library_Metadata_API::get_person_meta( $post_id, $meta_key, true );
		}

		return $meta;
	}

	/**
	 * Get person taxonomies.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	public static function get_person_taxonomies( int $post_id ) : array {
		// person taxonomies.
		$taxonomies = array(
			Career::SLUG,
		);

		// prepare person taxonomies.
		$person_taxonomies = array();

		foreach ( $taxonomies as $taxonomy ) {
			$person_taxonomies[ $taxonomy ] = wp_get_post_terms( $post_id, $taxonomy );
		}

		return $person_taxonomies;
	}

	/**
	 * Get person.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_person( \WP_REST_Request $request ) {
		// get person ID.
		$person_id = $request->get_param( 'id' );

		if ( ! $person_id ) {
			return new \WP_Error(
				'rest_person_id_required',
				__( 'Person ID required.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// get person.
		$person = get_post( $person_id );

		if ( ! $person || Person::SLUG !== $person->post_type ) {
			return new \WP_Error(
				'rest_person_not_found',
				__( 'Person not found.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// prepare response.
		$response = self::get_person_data( $person );

		return rest_ensure_response( $response );
	}

	/**
	 * Check if a given request has access to get person.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public static function get_people_permissions_check( \WP_REST_Request $request ) {
		// check if current user can read.
		if ( ! current_user_can( 'read' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot view the person.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	/**
	 * Create or Update person.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response | \WP_Error
	 */
	public static function create_or_update_person( \WP_REST_Request $request ) {
		// get person id.
		$person_id = $request->get_param( 'id' );

		// get request body.
		$data = $request->get_body();
		$data = json_decode( $data, true );

		// check if necessary fields are present, when creating the post.
		if ( empty( $person_id ) ) {
			$required_fields = array(
				'title',
				'content',
			);

			foreach ( $required_fields as $field ) {
				if ( empty( $data[ $field ] ) ) {
					return new \WP_Error(
						array(
							'status'  => 'necessary_field_missing',
							/* translators: %s: field name */
							'message' => sprintf( __( '%s is required.', 'movie-library' ), ucwords( $field ) ),
						),
						400
					);
				}
			}
		}

		// prepare post arguments.
		$args = array(
			'post_type'      => Person::SLUG,
			'post_title'     => $data['title'],
			'post_author'    => $data['author'] ?? get_current_user_id(),
			'post_content'   => $data['content'],
			'post_excerpt'   => $data['excerpt'] ?? '',
			'post_status'    => $data['status'] ?? 'publish',
			'comment_status' => $data['comment_status'] ?? 'closed',
			'ping_status'    => $data['ping_status'] ?? 'closed',
			'post_password'  => $data['post_password'] ?? '',
		);

		// check edit_other_posts capability and add the author's id if not.
		if ( ! current_user_can( 'edit_others_posts' ) ) {
			$args['post_author'] = get_current_user_id();
		}

		// check if user can publish posts.
		if ( ! current_user_can( 'publish_posts' ) ) {
			$args['post_status'] = 'draft';
		}

		// add post id if updating.
		if ( $person_id ) {
			$args['ID'] = $person_id;
		}

		// execute the query.
		$person_id = wp_insert_post( $args );

		// check for errors.
		if ( is_wp_error( $person_id ) ) {
			return rest_ensure_response( $person_id );
		}

		// add the person featured image.
		if ( isset( $data['featured_image'] ) && $data['featured_image'] ) {
			set_post_thumbnail( $person_id, absint( $data['featured_image'] ) );
		}

		// update person meta.
		$person_meta = $data['meta'] ?? array();

		if ( $person_meta ) {
			foreach ( $person_meta as $meta_key => $meta_value ) {
				Movie_Library_Metadata_API::update_person_meta( $person_id, $meta_key, $meta_value );
			}
		}

		// update person taxonomies.
		$person_taxonomies = $data['taxonomy'];

		if ( $person_taxonomies ) {
			foreach ( $person_taxonomies as $taxonomy => $terms ) {
				wp_set_post_terms( $person_id, $terms, $taxonomy );
			}
		}

		// return updated or created person data.
		return rest_ensure_response( self::get_person_data( get_post( $person_id ) ) );
	}

	/**
	 * Check if a given request has access to create or update a person.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public static function create_or_update_person_permissions_check( \WP_REST_Request $request ) {
		// check for specific person post.
		if ( $request->get_param( 'id' ) && ! current_user_can( 'edit_post', $request->get_param( 'id' ) ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot update or create the person.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// check for create person capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot update or create the person.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	/**
	 * Delete person.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_Error | \WP_REST_Response
	 */
	public static function delete_person( \WP_REST_Request $request ) {
		$person_id = $request->get_param( 'id' );

		// reject if person id is not present.
		if ( ! $person_id ) {
			return new \WP_Error(
				'rest_person_id_required',
				__( 'Person ID required.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// get the person post.
		$person = get_post( $person_id );

		// if person is not found, return error.
		if ( ! $person || Person::SLUG !== $person->post_type ) {
			return new \WP_Error(
				'rest_person_invalid_id',
				__( 'Invalid person ID.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// delete the person.
		$deleted = wp_delete_post( $person_id );

		// if person is not deleted, return error.
		if ( ! $deleted ) {
			return new \WP_Error(
				'rest_person_cannot_delete',
				__( 'Person not found!', 'movie-library' ),
				array( 'status' => 500 )
			);
		}

		// delete all person meta.
		self::delete_all_person_meta( $person_id );

		// return deleted person data.
		return rest_ensure_response( self::get_person_data( $deleted ) );
	}

	/**
	 * Delete all person meta.
	 *
	 * @param int $person_id Perons ID.
	 *
	 * @return void
	 */
	public static function delete_all_person_meta( int $person_id ) {
		// get all meta.
		$person_meta = Movie_Library_Metadata_API::get_person_meta( $person_id );

		// delete all meta.
		if ( $person_meta ) {
			foreach ( $person_meta as $meta_key => $meta_value ) {
				Movie_Library_Metadata_API::delete_person_meta( $person_id, $meta_key );
			}
		}
	}

	/**
	 * Check if a given request has access to delete a person.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public static function delete_person_permissions_check( \WP_REST_Request $request ) {
		// if id is not present, return error.
		if ( ! $request->get_param( 'id' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Please provide the ID to delete the person.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// get the person post.
		$person = get_post( $request->get_param( 'id' ) );

		// if person is not found, return error.
		if ( ! $person || Person::SLUG !== $person->post_type ) {
			return new \WP_Error(
				'rest_person_invalid_id',
				__( 'Invalid person ID.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// if user cannot delete the person, return error.
		if ( ! current_user_can( 'delete_post', $request->get_param( 'id' ) ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot delete the person resource.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	/**
	 * People POST, PUT, PATCH endpoint request args
	 *
	 * @return array
	 */
	public static function people_editable_route_args() : array {
		return array(
			'id'             => array(
				'description'       => __( 'Unique identifier for the post.', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
			),
			'author'         => array(
				'description'       => __( 'Author ID', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
			),
			'status'         => array(
				'description'       => __( 'Post status.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'publish',
				'validate_callback' => function ( $param, $request, $key ) {
					return in_array( $param, array( 'publish', 'draft', 'pending', 'trash' ), true );
				},
				'enum'              => array(
					'publish',
					'draft',
					'pending',
					'trash',
				),
			),
			'comment_status' => array(
				'description'       => __( 'Comment status.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'open',
				'validate_callback' => function ( $param, $request, $key ) {
					return in_array( $param, array( 'open', 'closed' ), true );
				},
				'enum'              => array(
					'open',
					'closed',
				),
			),
			'ping_status'    => array(
				'description'       => __( 'Ping status.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'open',
				'validate_callback' => function ( $param, $request, $key ) {
					return in_array( $param, array( 'open', 'closed' ), true );
				},
				'enum'              => array(
					'open',
					'closed',
				),
			),
			'post_password'  => array(
				'description'       => __( 'Post password.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'sanitize_text_field',
			),
			'title'          => array(
				'description'       => __( 'Title for the post.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'sanitize_text_field',
			),
			'content'        => array(
				'description'       => __( 'Content for the post.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'wp_kses_post',
				'validate_callback' => 'wp_kses_post',
			),
			'excerpt'        => array(
				'description'       => __( 'Excerpt for the post.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'wp_kses_post',
				'validate_callback' => 'wp_kses_post',
			),
			'featured_image' => array(
				'description'       => __( 'Featured image for the post.', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
			),
			'meta'           => array(
				'description' => __( 'Meta data.', 'movie-library' ),
				'type'        => 'object',
				'properties'  => array(
					'rt-person-meta-basic-birth-date'  => array(
						'default'     => '',
						'type'        => 'string',
						'description' => __( 'The birth date for the person.', 'movie-library' ),
						// pattern: YYYY-MM-DD.
						'pattern'     => '^\d{4}-\d{2}-\d{2}$',
					),
					'rt-person-meta-basic-birth-place' => array(
						'default'     => '',
						'type'        => 'string',
						'description' => __( 'The birth place for the person.', 'movie-library' ),
						'pattern'     => '^[a-zA-Z0-9\s,]+$',
					),
					'rt-person-meta-basic-full-name'   => array(
						'default'     => '',
						'type'        => 'string',
						'description' => __( 'The full name for the person.', 'movie-library' ),
						'pattern'     => '^[a-zA-Z0-9\s]+$',
					),
					'rt-person-meta-social-twitter'    => array(
						'default'     => '',
						'type'        => 'string',
						'format'      => 'uri',
						'description' => __( 'The twitter handle for the person.', 'movie-library' ),
					),
					'rt-person-meta-social-facebook'   => array(
						'default'     => '',
						'type'        => 'string',
						'format'      => 'uri',
						'description' => __( 'The facebook handle for the person.', 'movie-library' ),
					),
					'rt-person-meta-social-instagram'  => array(
						'default'     => '',
						'type'        => 'string',
						'format'      => 'uri',
						'description' => __( 'The instagram handle for the person.', 'movie-library' ),
					),
					'rt-person-meta-social-web'        => array(
						'default'     => '',
						'type'        => 'string',
						'format'      => 'uri',
						'description' => __( 'The website for the person.', 'movie-library' ),
					),
					'rt-media-meta-images'             => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The images for the person.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-media-meta-videos'             => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The videos for the person.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
				),
			),
			'taxonomy'       => array(
				'type'        => 'object',
				'description' => __( 'The taxonomy data for the object.', 'movie-library' ),
				'properties'  => array(
					Career::SLUG => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The careers for the person.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
				),
			),
		);
	}

	/**
	 * Validate the integer.
	 *
	 * @param mixed            $value Value to validate.
	 * @param \WP_REST_Request $request Full details about the request.
	 * @param string           $param The parameter name.
	 *
	 * @return bool
	 */
	public static function validate_my_int( $value, $request, $param ): bool {
		return is_int( $value );
	}
}
