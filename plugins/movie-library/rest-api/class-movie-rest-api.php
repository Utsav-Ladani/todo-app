<?php
/**
 * Movie REST API
 * It registers different routes and endpoints for the REST API.
 *
 * @package MovieLibrary
 */

namespace Movie_Library\REST_API;

use Movie_Library\Custom_Post_Type\Movie;
use Movie_Library\APIs\Movie_Library_Metadata_API;
use Movie_Library\Shadow_Taxonomy\Non_Hierarchical\Shadow_Person;
use Movie_Library\Taxonomy\Hierarchical\Genre;
use Movie_Library\Taxonomy\Hierarchical\Label;
use Movie_Library\Taxonomy\Hierarchical\Language;
use Movie_Library\Taxonomy\Hierarchical\Production_Company;
use Movie_Library\Taxonomy\Non_Hierarchical\Tag;

/**
 * Class Movie_REST_API
 * It registers different routes and endpoints for the REST API.
 */
class Movie_REST_API {
	/**
	 * Initialize the movie REST API.
	 *
	 * @return void
	 */
	public static function init() {
		// Register movie routes.
		add_action( 'rest_api_init', array( __CLASS__, 'register_movie_routes' ) );
	}

	/**
	 * Register movie routes.
	 *
	 * @return void
	 */
	public static function register_movie_routes() {
		/**
		 * Route: /wp-json/movie-library/v1/movies
		 */
		register_rest_route(
			'movie-library/v1',
			'/movies',
			array(
				/**
				 * Route: /wp-json/movie-library/v1/movies
				 * Method: GET
				 */
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( __CLASS__, 'get_movies' ),
					'permission_callback' => array( __CLASS__, 'get_movies_permissions_check' ),
					'args'                => self::movies_get_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/movies
				 * Method: POST
				 */
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'create_or_update_movie' ),
					'permission_callback' => array( __CLASS__, 'create_or_update_movie_permissions_check' ),
					'args'                => self::movie_editable_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/movies
				 * Method: DELETE
				 */
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'delete_movie' ),
					'permission_callback' => array( __CLASS__, 'delete_movie_permissions_check' ),
					'args'                => self::movie_delete_route_args(),
				),
			),
		);

		/**
		 * Route: /wp-json/movie-library/v1/movies/{id}
		 */
		register_rest_route(
			'movie-library/v1',
			'/movies/(?P<id>\d+)',
			array(
				/**
				 * Route: /wp-json/movie-library/v1/movies/{id}
				 * Method: GET
				 */
				array(
					'methods'             => 'GET',
					'callback'            => array( __CLASS__, 'get_movie' ),
					'permission_callback' => array( __CLASS__, 'get_movies_permissions_check' ),
					'args'                => self::movie_get_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/movies/{id}
				 * Method: POST, PUT, PATCH
				 */
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( __CLASS__, 'create_or_update_movie' ),
					'permission_callback' => array( __CLASS__, 'create_or_update_movie_permissions_check' ),
					'args'                => self::movie_editable_route_args(),
				),

				/**
				 * Route: /wp-json/movie-library/v1/movies/{id}
				 * Method: DELETE
				 */
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( __CLASS__, 'delete_movie' ),
					'permission_callback' => array( __CLASS__, 'delete_movie_permissions_check' ),
					'args'                => self::movie_delete_route_args(),
				),
			),
		);
	}

	/**
	 * Get movies.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_Error | \WP_REST_Response
	 */
	public static function get_movies( \WP_REST_Request $request ) {
		// prepare arguments.
		$args = array(
			'post_type'      => Movie::SLUG,
			'posts_per_page' => $request->get_param( 'per_page' ) ?? 10,
			'paged'          => $request->get_param( 'page' ) ?? 1,
			'offset'         => $request->get_param( 'offset' ) ?? 0,
			'orderby'        => $request->get_param( 'orderby' ) ?? 'date',
			'order'          => $request->get_param( 'order' ) ?? 'DESC',
			'has_password'   => false,
		);

		// execute query.
		$movies = get_posts( $args );

		if ( empty( $movies ) ) {
			return new \WP_Error(
				'no_movies_found',
				__( 'No movies found.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// prepare response.
		$response = array();

		foreach ( $movies as $movie ) {
			$response[] = self::get_movie_data( $movie );
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Get movie data.
	 *
	 * @param \WP_Post $movie Movie object.
	 *
	 * @return array
	 */
	public static function get_movie_data( \WP_Post $movie ) : array {
		// structure movie data.
		return array(
			'id'             => $movie->ID,
			'title'          => $movie->post_title,
			'name'           => $movie->post_name,
			'author'         => $movie->post_author,
			'content'        => $movie->post_content,
			'excerpt'        => $movie->post_excerpt,
			'status'         => $movie->post_status,
			'comment_status' => $movie->comment_status,
			'modified'       => $movie->post_modified,
			'published'      => $movie->post_date,
			'link'           => $movie->guid,
			'featured_image' => get_the_post_thumbnail_url( $movie->ID, 'full' ),
			'meta'           => self::get_movie_metas( $movie->ID ),
			'taxonomy'       => self::get_movie_taxonomies( $movie->ID ),
		);
	}

	/**
	 * Get movie post meta.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	public static function get_movie_metas( int $post_id ) : array {
		// movie meta keys.
		$meta_list = array(
			'rt-movie-meta-basic-rating',
			'rt-movie-meta-basic-release-date',
			'rt-movie-meta-basic-runtime',
			'rt-media-meta-images',
			'rt-media-meta-videos',
			'rt-movie-meta-crew-actor',
			'rt-movie-meta-crew-director',
			'rt-movie-meta-crew-writer',
			'rt-movie-meta-crew-producer',
		);

		// prepare movie meta.
		$meta = array();

		foreach ( $meta_list as $meta_key ) {
			// get meta using custom metadata API.
			$meta[ $meta_key ] = Movie_Library_Metadata_API::get_movie_meta( $post_id, $meta_key, true );
		}

		return $meta;
	}

	/**
	 * Get movie taxonomies.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return array
	 */
	public static function get_movie_taxonomies( int $post_id ) : array {
		// movie taxonomies.
		$taxonomies = array(
			Genre::SLUG,
			Label::SLUG,
			Language::SLUG,
			Production_Company::SLUG,
			Tag::SLUG,
		);

		// prepare movie taxonomies.
		$movie_taxonomies = array();

		foreach ( $taxonomies as $taxonomy ) {
			$movie_taxonomies[ $taxonomy ] = wp_get_post_terms( $post_id, $taxonomy );
		}

		return $movie_taxonomies;
	}

	/**
	 * Get movie.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function get_movie( \WP_REST_Request $request ) {
		// get movie ID.
		$movie_id = $request->get_param( 'id' );

		if ( ! $movie_id ) {
			return new \WP_Error(
				'rest_movie_id_required',
				__( 'Movie ID required.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// get movie.
		$movie = get_post( $movie_id );

		if ( ! $movie || Movie::SLUG !== $movie->post_type ) {
			return new \WP_Error(
				'rest_movie_not_found',
				__( 'Movie not found.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// prepare response.
		$response = self::get_movie_data( $movie );

		return rest_ensure_response( $response );
	}

	/**
	 * Check if a given request has access to get movies.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public static function get_movies_permissions_check( \WP_REST_Request $request ) {
		// check if current user can read.
		if ( ! current_user_can( 'read' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot view the movie(s).', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	/**
	 * Create or Update movie.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_REST_Response | \WP_Error
	 */
	public static function create_or_update_movie( \WP_REST_Request $request ) {
		// get movie id.
		$movie_id = $request->get_param( 'id' );

		// get request body.
		$data = $request->get_body();
		$data = json_decode( $data, true );

		// check if necessary fields are present, when creating the post.
		if ( empty( $movie_id ) ) {
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
			'post_type'      => Movie::SLUG,
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
		if ( $movie_id ) {
			$args['ID'] = $movie_id;
		}

		// execute the query.
		$movie_id = wp_insert_post( $args );

		// check for errors.
		if ( is_wp_error( $movie_id ) ) {
			return rest_ensure_response( $movie_id );
		}

		// add the movie featured image.
		if ( isset( $data['featured_image'] ) && $data['featured_image'] ) {
			set_post_thumbnail( $movie_id, absint( $data['featured_image'] ) );
		}

		// update movie meta.
		$movie_meta = $data['meta'] ?? array();

		self::update_movie_meta_and_shadow_tax( $movie_id, $movie_meta );

		// update movie taxonomies.
		$movie_taxonomies = $data['taxonomy'];

		if ( $movie_taxonomies ) {
			foreach ( $movie_taxonomies as $taxonomy => $terms ) {
				wp_set_post_terms( $movie_id, $terms, $taxonomy );
			}
		}

		// return updated or created movie data.
		return rest_ensure_response( self::get_movie_data( get_post( $movie_id ) ) );
	}

	/**
	 * Update movie meta and shadow taxonomies.
	 *
	 * @param int   $movie_id Movie ID.
	 * @param array $movie_meta Movie meta.
	 */
	public static function update_movie_meta_and_shadow_tax( int $movie_id, array $movie_meta ) {
		// remove term relationships.
		wp_delete_object_term_relationships( $movie_id, Shadow_Person::SLUG );

		$person_list = self::extract_person_ids_from_movie_meta( $movie_meta );

		// change the id to shadow person slug.
		$person_list = array_map(
			function( $person_id ) {
				return 'person-' . $person_id;
			},
			$person_list
		);

		// add term relationships.
		wp_add_object_terms( $movie_id, $person_list, Shadow_Person::SLUG );

		if ( $movie_meta ) {
			foreach ( $movie_meta as $meta_key => $meta_value ) {
				Movie_Library_Metadata_API::update_movie_meta( $movie_id, $meta_key, $meta_value );
			}
		}
	}

	/**
	 * Extract person ids from movie meta.
	 *
	 * @param array $movie_meta Movie meta.
	 *
	 * @return array
	 */
	public static function extract_person_ids_from_movie_meta( array $movie_meta ) : array {
		$crew_keys = array(
			'rt-movie-meta-crew-director',
			'rt-movie-meta-crew-writer',
			'rt-movie-meta-crew-producer',
		);

		$person_list = array();

		// get person list.
		foreach ( $crew_keys as $crew_key ) {
			if ( ! empty( $movie_meta[ $crew_key ] ) ) {
				$person_list = array_merge( $person_list, $movie_meta[ $crew_key ] );
			}
		}

		// add the actor list.
		if ( ! empty( $movie_meta['rt-movie-meta-crew-actor'] ) ) {
			foreach ( $movie_meta['rt-movie-meta-crew-actor'] as $actor_id => $char_name ) {
				$person_list[] = $actor_id;
			}
		}

		// remove the duplicate person ids.
		return array_unique( $person_list );
	}


	/**
	 * Check if a given request has access to create or update a movie.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public static function create_or_update_movie_permissions_check( \WP_REST_Request $request ) {
		// check for specific movie post.
		if ( $request->get_param( 'id' ) ) {

			$post = get_post( $request->get_param( 'id' ) );

			if ( ! $post || Movie::SLUG !== $post->post_type ) {
				return new \WP_Error(
					'rest_forbidden',
					__( 'Movie not found.', 'movie-library' ),
					array( 'status' => 404 )
				);
			}

			if ( ! current_user_can( 'edit_post', $request->get_param( 'id' ) ) ) {
				return new \WP_Error(
					'rest_forbidden',
					__( 'You cannot update or create the movie.', 'movie-library' ),
					array( 'status' => 404 )
				);
			}

			return true;
		}

		// check for create movie capability.
		if ( ! current_user_can( 'edit_posts' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot update or create the movie.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	/**
	 * Delete movie.
	 *
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return \WP_Error|\WP_REST_Response
	 */
	public static function delete_movie( \WP_REST_Request $request ) {
		$movie_id = $request->get_param( 'id' );

		// reject if movie id is not present.
		if ( ! $movie_id ) {
			return new \WP_Error(
				'rest_movie_id_required',
				__( 'Movie ID required.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		$movie = get_post( $movie_id );

		// if movie is not found, return error.
		if ( ! $movie || Movie::SLUG !== $movie->post_type ) {
			return new \WP_Error(
				'rest_movie_not found',
				__( 'Movie not found.', 'movie-library' ),
				array( 'status' => 404 ),
			);
		}

		// delete the movie.
		$deleted = wp_delete_post( $movie_id );

		// if movie is not deleted, return error.
		if ( ! $deleted ) {
			return new \WP_Error(
				'rest_movie_not_deleted',
				__( 'Movie not deleted!', 'movie-library' ),
				array( 'status' => 500 )
			);
		}

		// delete all movie meta.
		self::delete_all_movie_meta( $movie_id );

		// remove term relationships.
		wp_delete_object_term_relationships( $movie_id, Shadow_Person::SLUG );

		// return deleted movie data.
		return rest_ensure_response( self::get_movie_data( $deleted ) );
	}

	/**
	 * Delete all movie meta.
	 *
	 * @param int $movie_id Movie ID.
	 *
	 * @return void
	 */
	public static function delete_all_movie_meta( int $movie_id ) {
		// get all meta.
		$movie_meta = Movie_Library_Metadata_API::get_movie_meta( $movie_id );

		// delete all meta.
		if ( $movie_meta ) {
			foreach ( $movie_meta as $meta_key => $meta_value ) {
				Movie_Library_Metadata_API::delete_movie_meta( $movie_id, $meta_key );
			}
		}
	}

	/**
	 * Check if a given request has access to delete a movie.
	 *
	 * @param \WP_REST_Request $request Full data about the request.
	 *
	 * @return bool|\WP_Error
	 */
	public static function delete_movie_permissions_check( \WP_REST_Request $request ) {
		// if id is not present, return error.
		if ( ! $request->get_param( 'id' ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Please provide the ID to delete the movie.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// get the movie.
		$movie = get_post( $request->get_param( 'id' ) );

		// throw error if movie is not found.
		if ( ! $movie || Movie::SLUG !== $movie->post_type ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'Movie not found.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		// permission check.
		if ( ! current_user_can( 'delete_post', $request->get_param( 'id' ) ) ) {
			return new \WP_Error(
				'rest_forbidden',
				__( 'You cannot delete the movie.', 'movie-library' ),
				array( 'status' => 404 )
			);
		}

		return true;
	}

	/**
	 * Movies GET endpoint request args
	 *
	 * @return array
	 */
	public static function movies_get_route_args() : array {
		return array(
			'per_page' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
				'default'           => 10,
			),
			'page'     => array(
				'description'       => __( 'Offset the result set by a specific number of items.', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
				'default'           => 1,
			),
			'offset'   => array(
				'description'       => __( 'Offset the result set by a specific number of items.', 'movie-library' ),
				'type'              => 'integer',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
				'default'           => 0,
			),
			'order'    => array(
				'description'       => __( 'Order sort attribute ascending or descending.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'DESC',
				'validate_callback' => function ( $param, $request, $key ) {
					return in_array( $param, array( 'ASC', 'DESC' ), true );
				},
				'enum'              => array(
					'ASC',
					'DESC',
				),
			),
			'orderby'  => array(
				'description'       => __( 'Sort collection by object attribute.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'date',
				'validate_callback' => function ( $param, $request, $key ) {
					return in_array( $param, array( 'date', 'title', 'id' ), true );
				},
				'enum'              => array(
					'date',
					'title',
					'id',
				),
			),
		);
	}

	/**
	 * Movie GET endpoint request args
	 *
	 * @return array
	 */
	public static function movie_get_route_args() : array {
		return array(
			'id' => array(
				'description'       => __( 'Unique identifier for the post.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
			),
		);
	}

	/**
	 * Movie POST, PUT, PATCH endpoint request args
	 *
	 * @return array
	 */
	public static function movie_editable_route_args() : array {
		return array(
			'id'             => array(
				'description'       => __( 'Unique identifier for the post.', 'movie-library' ),
				'type'              => 'string',
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
					'rt-movie-meta-basic-rating'       => array(
						'default'     => '0',
						'type'        => 'string',
						'description' => __( 'The rating for the movie.', 'movie-library' ),
						'pattern'     => '^([0-9]|10)(\.[0-9])?$',
					),
					'rt-movie-meta-basic-release-date' => array(
						'default'     => '',
						'type'        => 'string',
						'description' => __( 'The release date for the movie.', 'movie-library' ),
						// pattern: YYYY-MM-DD.
						'pattern'     => '^\d{4}-\d{2}-\d{2}$',
					),
					'rt-movie-meta-basic-runtime'      => array(
						'default'     => 0,
						'type'        => 'integer',
						'description' => __( 'The runtime for the movie.', 'movie-library' ),
						'minimum'     => 0,
						'maximum'     => 1000,
					),
					'rt-media-meta-images'             => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The images for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-media-meta-videos'             => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The videos for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-meta-crew-actor'         => array(
						'default'           => array(),
						'type'              => 'object',
						'description'       => __( 'The actors for the movie.', 'movie-library' ),
						'patternProperties' => array(
							'^\\d+$' => array(
								'type' => 'string',
							),
						),
					),
					'rt-movie-meta-crew-director'      => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The directors for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-meta-crew-writer'        => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The writers for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					'rt-movie-meta-crew-producer'      => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The producers for the movie.', 'movie-library' ),
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
					Genre::SLUG              => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The genres for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Language::SLUG           => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The languages for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Label::SLUG              => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The labels for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
					Production_Company::SLUG => array(
						'default'     => array(),
						'type'        => 'array',
						'description' => __( 'The production companies for the movie.', 'movie-library' ),
						'items'       => array(
							'type' => 'integer',
						),
					),
				),
			),
		);
	}

	/**
	 * Movie DELETE endpoint request args.
	 *
	 * @return array
	 */
	public static function movie_delete_route_args() {
		return array(
			'id' => array(
				'description'       => __( 'Unique identifier for the object.', 'movie-library' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => array( __CLASS__, 'validate_my_int' ),
				'required'          => true,
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
		$value = absint( $value );
		return is_int( $value );
	}
}
