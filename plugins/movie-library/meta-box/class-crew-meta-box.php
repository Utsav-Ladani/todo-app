<?php
/**
 * Crew Meta Box. It contains the meta box for the crew like director, producer, writer, and actor.
 *
 * @package Movie_Library
 */

namespace Movie_Library\Meta_Box;

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
	public static array $crew_data = array(
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
	}

	/**
	 * Add crew meta box.
	 *
	 * @since 1.0.0
	 * @access public
	 * @static
	 */
	public static function add_crew_meta_box() : void
	{
		// Add meta box.
		add_meta_box(
			'rt-movie-meta-crew',
			__( 'Crew', 'movie-library' ),
			array( __CLASS__, 'render_crew_meta_box' ),
			'rt-movie',
			'side',
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
		$selected_crew_member = get_post_meta( $post_id, $crew['id'], true);

		// if the selected crew member is not an array, then make it an array.
		if( ! is_array($selected_crew_member) ) {
			$selected_crew_member = array();
		}

		// Render the meta box section.
		?>
		<label for='<?php echo esc_attr( $crew['id'] ); ?>' >
			<?php echo esc_html( $crew['name'] ); ?>
		</label>
		<select
			name='<?php echo esc_attr( $crew['id'] ); ?>[]'
			id='<?php echo esc_attr( $crew['id'] ); ?>'
			size='3'
			multiple
		>
			<?php

			foreach( $all_crew_member as $crew_member ) {
				?>
				<option
					value='<?php echo esc_attr( $crew_member['id'] ); ?>'
					<?php
						// if the crew member is selected, then mark it as selected.
						echo in_array( $crew_member['id'], $selected_crew_member ) ? 'selected' : '';
					?>
				>
					<?php echo esc_html( $crew_member['name'] ); ?>
				</option>
				<?php
			}
			?>
			?>
		</select>
		<br />
		<?php
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
		$person_query = new \WP_Query( array(
			'post_type' => 'rt-person',
			'orderby' => 'name',
			'order' => 'ASC',
			'tax_query' => array(
				array(
					'taxonomy' => 'rt-person-career',
					'field' => 'term_id',
					'terms' => $career,
				),
			),
		) );

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

	public static function save_crew_meta_data( int $post_id ) : void {
		// check if the request type is POST or not
		if( ! isset($_POST) || count($_POST) === 0  ) {
			return;
		}

		// check if the current user has the permission to edit the post.
		if( !current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// avoid the autosave and revision.
		if( wp_is_post_autosave( $post_id ) || wp_is_post_revision($post_id) ) {
			return;
		}

		// unlink all the existing crew members temp-relationships.
		wp_delete_object_term_relationships( $post_id, '_rt-movie-person' );

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
			FILTER_SANITIZE_STRING,
			FILTER_REQUIRE_ARRAY
		);

		// if the no member is selected then delete the metadata and return.
		if( empty($selected_crew_member) || ! is_array($selected_crew_member) ) {
			delete_post_meta( $post_id, $crew['id'] );
			return;
		}

		$rt_person_arr = array();

		// loop over the selected crew members and create the temp-relationship.
		foreach ( $selected_crew_member as $person ) {
			$rt_person_arr[] = sprintf( 'rt-person-%d', (int) $person );
		}

		// update the metadata.
		update_post_meta( $post_id, $crew['id'], $selected_crew_member );

		// add the temp-relationships.
		wp_add_object_terms( $post_id, $rt_person_arr, '_rt-movie-person');
	}
}