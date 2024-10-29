<?php
/*
Plugin Name: Append to Menu Links
Description: Append a string such as a hash to the end of menu links. Useful for creating menus that link to a section of a page without having to create a custom link or to add parameters to a page's URL, this way if your page ever changes you don't need to update the link. If you enjoy this plugin, <a href="https://Ko-fi.com/waldirb">buy me a cup of coffee</a>.
Version:     1.0.4
Author:      Waldir Bolanos
Author URI:  https://waldirb.com
Plugin URI:  https://wordpress.org/plugins/append-to-menu-links/
Text Domain: append-to-menu-links
*/

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) || ( defined( 'WP_INSTALLING' ) && WP_INSTALLING ) ) {
	die;
}

/**
 * Class Append_To_Menu_Links
 */
class Append_To_Menu_Links {

	/**
	 * Add a custom screen option so it can be hidden if desired.
	 *
	 * @since  1.0.0
	 * @param  array $args
	 * @return array
	 */
	public static function add_custom_screen_option( $args ) {
			$args['hash'] = __( 'Hash', 'append-to-menu-links' );
			return $args;
	}

	/**
	 * Add menu item field to the menu item editing screen.
	 *
	 * @since  1.0.0
	 * @param  int  $item_id
	 * @param  array $item
	 */
	public static function add_menu_item_field( $item_id, $item ) {
		$menu_item_hash = get_post_meta( $item_id, '_menu_item_hash', true ); ?>
		<p class="field-hash description description-thin">
			<label>
				<?php
				esc_html_e( 'Hash', 'append-to-menu-links' );
				printf(
					'<br><input type="text" name="menu_item_hash[%s]" id="menu-item-hash-%s" value="%s" placeholder="#hash" />',
					esc_attr( $item_id ),
					esc_attr( $item_id, ),
					esc_attr( $menu_item_hash ),
				)
				?>
			</label>
		</p>
		<?php
	}

	/**
	 * Save menu item submission or delete it if empty.
	 *
	 * @since  1.0.0
	 * @param  int $menu_id
	 * @param  int $menu_item_db_id
	 */
	public static function save_menu_item_hash( $menu_id, $menu_item_db_id ) {
		if ( isset( $_POST['menu_item_hash'][ $menu_item_db_id ] ) ) {
			$sanitized_data = sanitize_text_field( $_POST['menu_item_hash'][ $menu_item_db_id ] );
			update_post_meta( $menu_item_db_id, '_menu_item_hash', $sanitized_data );
		} else {
			delete_post_meta( $menu_item_db_id, '_menu_item_hash' );
		}
	}

	/**
	 * Append the hash to the menu item URL.
	 *
	 * @since  1.0.0
	 * @param  object $item
	 * @return object
	 */
	public static function show_menu_item_hash( $item ) {
		if ( is_object( $item ) && isset( $item->ID ) ) {
			$menu_item_hash = get_post_meta( $item->ID, '_menu_item_hash', true );
			if ( ! empty( $menu_item_hash ) ) {
				$item->url = $item->url . $menu_item_hash;
			}
		}
		return $item;
	}

	/**
	 * Add the link to the plugin actions.
	 *
	 * @since  1.0.1
	 * @param array $actions array of links.
	 * @return array
	 */
	// public static function add_action_links( $actions ) {
	// 	$actions[] = '<a href="https://Ko-fi.com/waldirb" target="_blank" rel="noreferrer nofollow">Support</a>';
	// 	return $actions;
	// }

	/**
	 * Initiate the class by adding the hooks.
	 */
	public static function init() {
			add_filter( 'manage_nav-menus_columns', array( __CLASS__, 'add_custom_screen_option' ), 20 );
			add_action( 'wp_nav_menu_item_custom_fields', array( __CLASS__, 'add_menu_item_field' ), 10, 2 );
			add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'save_menu_item_hash' ), 10, 2 );
			add_filter( 'wp_setup_nav_menu_item', array( __CLASS__, 'show_menu_item_hash' ), 10, 2 );
			// add_filter( 'plugin_action_links_' . plugin_basename( __FILE__) , array( __CLASS__, 'add_action_links' ) );
	}
}

/**
 * Initiate the class.
 */
Append_To_Menu_Links::init();
