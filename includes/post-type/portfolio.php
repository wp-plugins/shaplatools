<?php
/**
 * Portfolio post type functions.
 * 
 * @package ShaplaTools
 */
$portfolio_labels = apply_filters( 'shapla_portfolio_labels', array(
	'name'               => __( 'Portfolio', 'shapla' ),
	'singular_name'      => __( 'Portfolio', 'shapla' ),
	'add_new'            => __( 'Add New', 'shapla' ),
	'add_new_item'       => __( 'Add New Portfolio', 'shapla' ),
	'edit_item'          => __( 'Edit Portfolio', 'shapla' ),
	'new_item'           => __( 'New Portfolio', 'shapla' ),
	'view_item'          => __( 'View Portfolio', 'shapla' ),
	'search_items'       => __( 'Search Portfolio', 'shapla' ),
	'not_found'          => __( 'No Portfolios found', 'shapla' ),
	'not_found_in_trash' => __( 'No Portfolios found in trash', 'shapla' ),
	'parent_item_colon'  => ''
) );

$shapla_options    = get_option('shapla_options');
@$portfolio_slug = $shapla_options['portfolio_slug'];
@$skills_slug    = $shapla_options['skills_slug'];

$portfolio_slug = ($portfolio_slug != '') ? $portfolio_slug : 'portfolio';
$skills_slug = ($skills_slug != '') ? $skills_slug : 'skill';

$rewrite = array(
	'slug'                => $portfolio_slug,
	'with_front'          => false,
);

$portfolio_args = array(
	'labels'            => $portfolio_labels,
	'public'            => true,
	'show_ui'           => true,
	'show_in_menu'      => true,
	'show_in_nav_menus' => false,
	'menu_position'     => 32,
	'menu_icon'         => 'dashicons-portfolio',
	'rewrite'           => $rewrite,
	'supports'          => apply_filters( 'shapla_portfolio_supports', array( 'title', 'editor', 'thumbnail', 'revisions' ) ),
	'taxonomies'        => array( 'skill' )
);

register_post_type( 'portfolio', apply_filters( 'shapla_portfolio_post_type_args', $portfolio_args ) );

register_taxonomy( 'skill', 'portfolio', array(
	'label'             => __( 'Skills', 'shapla' ),
	'singular_label'    => __( 'Skill', 'shapla' ),
	'public'            => true,
	'hierarchical'      => true,
	'show_ui'           => true,
	'show_in_nav_menus' => true,
	'args'              => array( 'orderby' => 'term_order' ),
	'query_var'         => true,
	'rewrite'           => array( 'slug' => $skills_slug, 'hierarchical' => true)
) );



add_action( 'add_meta_boxes', 'add_shapla_portfolio_meta_box');

function add_shapla_portfolio_meta_box() {
	add_meta_box(
	    'shaplatools_portfolio_section',
	    __( 'Portfolio Settings','shaplatools' ),
	    'meta_box_callback', 
	    'portfolio',
	    'advanced',
		'high'
	);
}


add_action( 'save_post', 'save_shapla_portfolio_meta_box');

function save_shapla_portfolio_meta_box( $post_id ) {

	// Check if our nonce is set.
	if ( ! isset( $_POST['shaplatools_portfolio_custom_box_nonce'] ) )
		return $post_id;

	$nonce = $_POST['shaplatools_portfolio_custom_box_nonce'];

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $nonce, 'shaplatools_portfolio_custom_box' ) )
		return $post_id;

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return $post_id;

	// Check the user's permissions.
	if ( 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) )
		return $post_id;
	
	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) )
		return $post_id;
	}

	/* OK, its safe for us to save the data now. */
	if ( ! empty( $_POST['shaplatools_portfolio'] ) ) {

		$portfolio['subtitle'] = sanitize_text_field( $_POST['shaplatools_portfolio']['subtitle'] );
		$portfolio['client'] = sanitize_text_field( $_POST['shaplatools_portfolio']['client'] );
		$portfolio['date'] = strtotime(sanitize_text_field( $_POST['shaplatools_portfolio']['date'] ));
		$portfolio['url'] = sanitize_text_field( $_POST['shaplatools_portfolio']['url'] );

		update_post_meta( $post_id, '_shaplatools_portfolio', $portfolio );
	} else {
		delete_post_meta( $post_id, '_shaplatools_portfolio' );
	}
}

function meta_box_callback($post){

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'shaplatools_portfolio_custom_box', 'shaplatools_portfolio_custom_box_nonce' );

	// Use get_post_meta to retrieve an existing value from the database.
	$portfolio = get_post_meta( get_the_ID(), '_shaplatools_portfolio', true );

	$subtitle = ( empty( $portfolio['subtitle'] ) ) ? '' : $portfolio['subtitle'];
	$client = ( empty( $portfolio['client'] ) ) ? '' : $portfolio['client'];
	$date = ( empty( $portfolio['date'] ) ) ? '' : $portfolio['date'];
	$url = ( empty( $portfolio['url'] ) ) ? '' : $portfolio['url'];

	//if there is previously saved value then retrieve it, else set it to the current time
	if (! empty( $date )) {
		$date = date_i18n( get_option( 'date_format' ), $portfolio['date']);
	} else {
		$date = '';
	}
		
		
	?>
		<table class="form-table">
			<tr valign="top">
                <th scope="row">
                    <label for="subtitle">
                        <?php _e('Subtitle','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="widefat" id="subtitle" name="shaplatools_portfolio[subtitle]" value="<?php echo esc_attr( $subtitle ); ?>">
                    <p><?php _e('Enter the subtitle for this portfolio item.','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="client">
                        <?php _e('Client Name','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="client" name="shaplatools_portfolio[client]" value="<?php echo esc_attr( $client ); ?>">
                    <p><?php _e('Enter the client name of the project.','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="url">
                        <?php _e('Project URL','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="url" class="regular-text" id="url" name="shaplatools_portfolio[url]" value="<?php echo esc_attr( $url ); ?>">
                    <p><?php _e('Enter the project URL','shaplatools'); ?></p>
				</td>
			</tr>
			<tr valign="top">
                <th scope="row">
                    <label for="portfolio_date">
                        <?php _e('Project Date','shaplatools') ?>
                    </label>
                </th>
				<td>
					<input type="text" class="regular-text" id="portfolio_date" name="shaplatools_portfolio[date]" value="<?php echo $date; ?>">
                    <p><?php _e('Choose the project date.','shaplatools'); ?></p>
				</td>
			</tr>
		</table>
	<?php
}

function shapla_portfolio_columns_head( $defaults ) {
	unset( $defaults['date'] );

	$defaults['skill'] = __( 'Skills', 'shaplatools' );
	$defaults['project_date'] = __( 'Project Date', 'shaplatools' );
	$defaults['project_client'] = __( 'Client', 'shaplatools' );
	$defaults['project_url'] = __( 'Project URL', 'shaplatools' );

	return $defaults;
}
add_filter( 'manage_edit-portfolio_columns', 'shapla_portfolio_columns_head');


function shapla_portfolio_columns_content( $column_name ) {

	$portfolio = get_post_meta( get_the_ID(), '_shaplatools_portfolio', true );

	if ( 'project_date' == $column_name ) {

		if (! empty( $portfolio['date'] )) {
			$portfolio_date = date_i18n( get_option( 'date_format' ), $portfolio['date']);
		} else {
			$portfolio_date = '';
		}
		echo $portfolio_date;
	}

	if ( 'skill' == $column_name ) {

		if ( ! $terms = get_the_terms( get_the_ID(), $column_name ) ) {
			echo '<span class="na">&mdash;</span>';
		} else {
			foreach ( $terms as $term ) {
				$termlist[] = '<a href="' . esc_url( add_query_arg( $column_name, $term->slug, admin_url( 'edit.php?post_type=portfolio' ) ) ) . ' ">' . ucfirst( $term->name ) . '</a>';
			}

			echo implode( ', ', $termlist );
		}
	}

	if ( 'project_client' == $column_name ) {
		if ( ! empty( $portfolio['client'] ) )
		echo $portfolio['client'];
	}

	if ( 'project_url' == $column_name ) {
		if ( ! empty( $portfolio['url'] ) )
		echo $portfolio['url'];
	}
}

add_action( 'manage_portfolio_posts_custom_column', 'shapla_portfolio_columns_content');