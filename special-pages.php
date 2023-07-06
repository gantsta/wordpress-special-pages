<?php
/**
 * Special Pages functionality
 *
 */


/**
 * Creates a new section for 'Special Pages' within the 'Reading' section of WordPress' 'Settings' screen.
 * Using this section we can select an existing page as a 'Special Page' - in this instance our 'Sectors' page.
 * NOTE: You can register as many Special Pages as you need just give each one a unique option_name
 *
 * @author		gantsta
 * @see			https://developer.wordpress.org/reference/functions/add_settings_field/
 * @see			https://developer.wordpress.org/reference/functions/register_setting/
 */
function THEME_add_settings_field(){
	add_settings_field(
		'theme-special-pages',
		__('Special Pages'),
		'THEME_options_callback',
		'reading',
		'default',
		array()
	);
	register_setting( 'reading', 'sectors_page_id' );
}
add_action( 'admin_init', 'THEME_add_settings_field' );


/**
 * This is the callback function that creates the markup for the 'Special Pages' section of the 'Reading' screen.
 * NOTE: You will need a separate li element with its own call to wp_dropdown_pages for each of the unique option_names that you registered within the THEME_add_settings_field function
 *
 * @author		gantsta
 */
function THEME_options_callback(){
	ob_start();
	?>
	<ul>
		<li>
			<label><?php _e('Sectors Page'); ?>
			<?php
			// Don't allow the user to choose the front page or the posts page as they could already be in use
			$page_for_posts = get_option( 'page_for_posts' );
			$page_on_front = get_option( 'page_on_front' );

			$excludes = array();
			if ( $page_for_posts != 0 ): array_push( $excludes, get_option( 'page_for_posts' ) ); endif;
			if ( $page_on_front != 0 ): array_push( $excludes, get_option( 'page_on_front' ) ); endif;

			$args = array(
				'name'				=> 'sectors_page_id',
				'selected'			=> get_option( 'sectors_page_id' ),
				'show_option_none'	=> __('None')
			);
			if ( count($excludes) >= 1 ):
				$args['exclude_tree'] = implode( ',', $excludes );
			endif;

			wp_dropdown_pages($args);
			?>
			</label>
		</li>
	</ul>
	<?php
	echo ob_get_clean();
}


/**
 * Add post display states for 'Special' posts
 *
 * @param		array					$post_states		An array of post display states.
 * @param		object (WP_Post)		$post				The current WordPress post object
 * @author		gantsta
 * @see			https://developer.wordpress.org/reference/hooks/display_post_states/
 */
function THEME_add_display_post_states( $post_states, $post ) {
	$sectors_page_id = get_option( 'sectors_page_id' );
	if ( $sectors_page_id == $post->ID ):
		$post_states[] = __( 'Sectors Page' );
	endif;
	return $post_states;
}
add_filter( 'display_post_states', 'THEME_add_display_post_states', 10, 2 );


/**
 * Frontend function to return the sectors page as a WordPress post object
 *
 * @author		gantsta
 * @return		mixed				WP_Post/bool			The sectors page as a WP_Post object or bool(false) on failure
 */
function THEME_get_sectors_page(){
	$sectors_page_id = get_option( 'sectors_page_id' );
	if ( false != $sectors_page_id ):
		return get_post($sectors_page_id);
	else:
		return false;
	endif;
}
