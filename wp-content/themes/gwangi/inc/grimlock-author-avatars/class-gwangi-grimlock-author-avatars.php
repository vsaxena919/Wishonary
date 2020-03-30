<?php
/**
 * Gwangi_Grimlock_Author_Avatars Class
 *
 * @package  gwangi
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Gwangi Grimlock Author Avatars integration class
 */
class Gwangi_Grimlock_Author_Avatars {
	/**
	 * Setup class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_filter( 'grimlock_author_avatars_section_widget_defaults', array( $this, 'change_author_avatars_section_widget_defaults' ), 10, 1 );
	}

	/**
	 * Change the default args for the author avatars section widget.
	 *
	 * @param  array $defaults The default args for the widget.
	 *
	 * @return array           The updated default args for the widget.
	 */
	public function change_author_avatars_section_widget_defaults( $defaults ) {
		$defaults['background_color'] = GWANGI_SECTION_WIDGET_BACKGROUND_COLOR;
		return $defaults;
	}
}

return new Gwangi_Grimlock_Author_Avatars();
