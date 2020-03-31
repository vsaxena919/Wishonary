<?php

/**
 * Edit Activity Slug.
 */
function yz_rename_activity_slug() {

    if ( defined( 'BP_ACTIVITY_SLUG' ) || ! bp_is_active( 'activity' ) ) {
        return false;
    }

    define( 'BP_ACTIVITY_SLUG', 'wall' );
}

add_action( 'init', 'yz_rename_activity_slug', 1 );

/**
 * Display Meta.
 */
function yz_display_wall_post_meta() {
	
	$show = true;

	// if ( is_user_logged_in() ) {
	// 	return true;
	// }

	// $show = true;
	
	// Get Post Likes
	$post_likes = (int) yz_get_who_liked_activities( bp_get_activity_id() );

	// // Get Post Comments
	$post_comments = (int) bp_activity_get_comment_count();
	//
	if ( 0 == $post_comments && $post_likes <= 0 && ! is_user_logged_in() ) {
		$show = false;
	}

	return apply_filters( 'yz_display_wall_post_meta', $show );
}

/**
 * Change Default Upload Directory to the Youzer Directory.
 */
function yz_temporary_upload_directory( $dir ) {
	
	global $YZ_upload_folder, $YZ_upload_url, $YZ_upload_dir;

    return array(
        'path'   => $YZ_upload_dir . '/temp',
        'url'    => $YZ_upload_url . '/temp',
        'subdir' => '/' . $YZ_upload_folder . '/temp',
    ) + $dir;

}

/**
 * Add Activity Shortcode.
 **/
function yz_activitiy_shortcode( $atts ) {
    
    global $yz_activity_shortcode_args;

	// Call Mentions Scripts.
    add_filter( 'bp_activity_maybe_load_mentions_scripts', 'yz_enable_activity_shortcode_mentions' );

    bp_activity_mentions_script();

	do_action( 'yz_before_activity_shortcode' );

	// Get Args.
	$yz_activity_shortcode_args = wp_parse_args( $atts, array( 'page' => 1, 'show_sidebar' => 'false', 'show_form' => 'true', 'load_more' => 'true', 'show_filter' => 'true' ) );

	if ( $yz_activity_shortcode_args['show_sidebar'] == 'false' ) {	
	    // Remove Sidebar.
	    add_filter( 'yz_activate_activity_stream_sidebar', '__return_false' );
	}

	$class = $yz_activity_shortcode_args['show_sidebar'] == 'false' ? 'yz-no-sidebar' : 'yz-with-sidebar';

    // Add Filter.
    add_filter( 'bp_after_has_activities_parse_args', 'yz_set_activity_stream_shortcode_atts', 99 );

    if ( isset( $yz_activity_shortcode_args['form_roles'] ) ) {
    	add_filter( 'yz_is_wall_posting_form_active', 'yz_set_wall_posting_form_by_role' );
    }

    if ( $yz_activity_shortcode_args['show_form'] == 'false' ) {
    	add_filter( 'yz_is_wall_posting_form_active', '__return_false' );
    }

    if ( $yz_activity_shortcode_args['show_filter'] == 'false' ) {
    	add_filter( 'yz_enable_activity_directory_filter_bar', '__return_false' );
    }

    if ( $yz_activity_shortcode_args['load_more'] == 'false' ) {
    	add_filter( 'bp_activity_has_more_items', '__return_false' );
    }

    $activity_data = '';
    
    if ( ! empty( $yz_activity_shortcode_args ) ) foreach ( $yz_activity_shortcode_args as $key => $value) { $activity_data .= "data-$key='$value'"; }

	ob_start();
    echo "<div class='yz-activity-shortcode $class' $activity_data>";
    include YZ_TEMPLATE . 'activity/index.php';
    echo "</div>";

	if ( $yz_activity_shortcode_args['show_sidebar'] == 'false' ) {	
	    // Remove Sidebar.
	    remove_filter( 'yz_activate_activity_stream_sidebar', '__return_false' );
	}

    if ( $yz_activity_shortcode_args['show_filter'] == 'false' ) {
    	remove_filter( 'yz_enable_activity_directory_filter_bar', '__return_false' );
    }

    if ( isset( $yz_activity_shortcode_args['form_roles'] ) ) {
    	remove_filter( 'yz_is_wall_posting_form_active', 'yz_set_wall_posting_form_by_role' );
    }

    if ( $yz_activity_shortcode_args['show_form'] == 'false' ) {
    	remove_filter( 'yz_is_wall_posting_form_active', '__return_false' );
    }

    if ( $yz_activity_shortcode_args['load_more'] == 'false' ) {
    	remove_filter( 'bp_activity_has_more_items', '__return_false' );
    }
    
    // Add Filter.
    remove_filter( 'bp_after_has_activities_parse_args', 'yz_set_activity_stream_shortcode_atts', 99 );

	return ob_get_clean();
}

add_shortcode( 'youzer_activity', 'yz_activitiy_shortcode' );


/**
 * Activity Shortcode - Ajax Pagination
 */

add_action( 'wp_ajax_yz_activity_load_activities', 'yz_activity_load_activities' );
add_action( 'wp_ajax_nopriv_yz_activity_load_activities', 'yz_activity_load_activities' );

function yz_activity_load_activities() {

	if ( bp_has_activities( $_POST['data'] ) ) {

		ob_start();

		?>
		
		<?php while ( bp_activities() ) : bp_the_activity(); ?>

			<?php bp_get_template_part( 'activity/entry' ); ?>

		<?php endwhile; ?>

		<?php yz_activity_load_more(); ?>

	<?php
		$content = ob_get_clean();

		wp_send_json_success( $content );
	} else {
		wp_send_json_error( array(
			'message' => __( 'Sorry, there was no activity found.', 'bp-activity-shortcode' ),
		) );
	}

	die();
}

/**
 * Load More Button
 */
function yz_activity_load_more() { ?>

	<?php if ( bp_activity_has_more_items() ) : ?>

		<li class="load-more">
			<a href="<?php bp_activity_load_more_link() ?>"><i class="fas fa-level-down-alt"></i><?php _e( 'Load More Posts', 'youzer' ); ?></a>
		</li>

	<?php endif; ?>

	<?php

}
/**
 * Activity Shortcode.
 */
function yz_set_activity_stream_shortcode_atts( $loop ) {

    global $yz_activity_shortcode_args;
    
    $loop = shortcode_atts( $loop, $yz_activity_shortcode_args, 'yz_activity_stream' );

    return $loop;

}

/**
 * Set Wall Posting Form By Role.
 */
function yz_set_wall_posting_form_by_role( $active ) {
	
	global $yz_activity_shortcode_args;

    $active = false;

    $shortcode_roles = explode( ',' , $yz_activity_shortcode_args['form_roles'] );

    if ( ! empty( $shortcode_roles ) ) {

	    // Get Current User Data.
	    $user = get_userdata( bp_loggedin_user_id() );
	    
	    // Get Roles.
	    $user_roles = (array) $user->roles;

	    foreach ( $shortcode_roles as $role ) {
	        if ( in_array( $role, $user_roles ) ) {        
	            $active = true;
	            continue;
	        }
	    }

    }
    
    return $active;

}

/**
 * Get Post Like Button.
 */
function yz_get_post_like_button() {

	// Get Activity ID.
	$activity_id = bp_get_activity_id();
	
	if ( ! bp_get_activity_is_favorite() ) {

		// Get Like Link.
		$like_link = bp_get_activity_favorite_link();

		// Get Like Button.
		$button = '<a href="'. $like_link .'" class="button fav bp-secondary-action">' . __( 'Like', 'youzer' ) . '</a>';

		// Filter.
		$button = apply_filters( 'yz_filter_post_like_button', $button, $like_link, $activity_id );

	} else {

		// Get Unlike Link.
		$unlike_link = bp_get_activity_unfavorite_link();

		// Get Like Button.
		$button = '<a href="'. $unlike_link .'" class="button unfav bp-secondary-action">' . __( 'Unlike', 'youzer' ) . '</a>';

		// Filter.
		$button = apply_filters( 'yz_filter_post_unlike_button', $button, $unlike_link, $activity_id );

	}
	
	return $button;

}

/**
 * Wall Post - Get Comment Button Title.
 */
function yz_wall_get_comment_button_title() {

	// Get Comments Number.
	$comments_nbr = bp_activity_get_comment_count();

	if ( $comments_nbr == '0' ) {
		$button_title = __( '<span></span> Comment', 'youzer' );
	} else {
		$button_title = sprintf( _n( '<span>%s</span> Comment', '<span>%s</span> Comments', $comments_nbr, 'youzer' ), $comments_nbr );
	}

	echo apply_filters( 'yz_wall_get_comment_button_title', $button_title, $comments_nbr );

}

/**
 * Register Wall New Actions.
 */
function yz_add_new_wall_post_actions() {

	// Init Vars
	$bp = buddypress();

	bp_activity_set_action(
		$bp->activity->id,
		'activity_status',
		__( 'Posted a new status', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'status', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_quote',
		__( 'Posted a new quote', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'quotes', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_photo',
		__( 'Posted a new photo', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'photos', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_video',
		__( 'Posted a new video', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'videos', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_audio',
		__( 'Posted a new audio file', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'audios', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_slideshow',
		__( 'Posted a new slideshow', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'slideshows', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_link',
		__( 'Posted a new link', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'links', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_file',
		__( 'Uploaded a new file', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'files', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'new_cover',
		__( 'Changed their profile cover', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'cover', 'youzer' ),
		array( 'activity', 'member' )
	);

	bp_activity_set_action(
		$bp->activity->id,
		'activity_giphy',
		__( 'Added a new Gif', 'youzer' ),
		'yz_activity_action_wall_posts',
		__( 'giphy', 'youzer' ),
		array( 'activity', 'group', 'member', 'member_groups' )
	);

}

add_action( 'bp_register_activity_actions', 'yz_add_new_wall_post_actions' );

/**
 * Activity Mood
 */
function yz_enable_activity_mood() {

	$active = false;

	if ( 'on' == yz_options( 'yz_activity_mood' ) ) {
		$active = true;
	}

	return apply_filters( 'yz_enable_activity_mood', $active );

}

/**
 * Activity Privacy
 */
function yz_enable_activity_privacy() {

	$active = false;

	if ( 'on' == yz_options( 'yz_activity_privacy' ) ) {
		$active = true;
	}

	return apply_filters( 'yz_enable_activity_privacy', $active );

}

/**
 * Activity Mood
 */
function yz_enable_activity_tag_friends() {

	$active = false;

	if ( 'on' == yz_options( 'yz_activity_tag_friends' ) ) {
		$active = true;
	}

	return apply_filters( 'yz_enable_activity_tag_friends', $active );

}

/**
 * Activity Hashtags
 */
function yz_enable_activity_hastags() {

	$active = false;

	if ( 'on' == yz_options( 'yz_activity_hashtags' ) ) {
		$active = true;
	}

	return apply_filters( 'yz_enable_activity_hastags', $active );

}