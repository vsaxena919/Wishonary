<?php
/**
 * Gwangi template hooks for Author Avatars
 *
 * @package grimlock-author-avatars/inc
 */

/**
 * Shortcode Hooks.
 *
 * @see gwangi_author_avatars_user_name_template()
 *
 * @since 1.0.0
 */

add_filter( 'aa_user_name_template', 'gwangi_author_avatars_user_name_template', 10, 4 );
