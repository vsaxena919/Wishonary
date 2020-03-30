<?php
/**
 * Gwangi Kirki Class
 *
 * @link https://aristath.github.io/kirki/
 *
 * @package  gwangi
 * @author   Themosaurus
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Gwangi_Kirki' ) ) :
	/**
	 * The Gwangi Kirki integration class
	 */
	class Gwangi_Kirki {
		/**
		 * Setup class.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'kirki/config', array( $this, 'change_kirki_config' ), 10, 1 );

			require_once get_template_directory() . '/inc/kirki/class-kirki-modules-webfonts-link.php';
			add_filter( 'kirki_googlefonts_load_method', array( $this, 'change_fonts_load_method' ), 10, 1 );
		}

		/**
		 * Change the config of the logo, description and loader for Kirki
		 *
		 * @since 1.0.0
		 *
		 * @param  mixed $config The config for Kirki.
		 *
		 * @return mixed $config The updated config for Kirki.
		 */
		public function change_kirki_config( $config ) {
			$description = __( 'Gwangi is a Dating & Community theme that integrates seamlessly with BuddyPress and WooCommerce. It makes good use of the Customizer, allowing you to build a tailor-made community plateform using custom color, typography and layout options. Please visit <a href="https://doc.themosaurus.com/gwangi/" target="_blank">the Gwangi online documentation</a> or contact us via <a href="https://support.themosaurus.com" target="_blank">the Themosaurus support forums</a> for help.', 'gwangi' );

			$config['description'] = wp_kses( $description, array(
				'a' => array(
					'href' => array(),
					'rel'  => array(),
				),
			) );
			return $config;
		}

		/**
		 * Change the webfonts load method to avoid FOUT
		 *
		 * @param string $method The current webfonts load method.
		 *
		 * @return string The new webfonts load method.
		 */
		public function change_fonts_load_method( $method ) {
			return 'link';
		}
	}
endif;

return new Gwangi_Kirki();
