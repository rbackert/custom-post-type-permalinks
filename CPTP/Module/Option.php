<?php
/**
 * Options.
 *
 * Save Options.
 *
 * @package Custom_Post_Type_Permalinks
 * */

/**
 * Class CPTP_Module_Option
 *
 * @since 0.9.6
 */
class CPTP_Module_Option extends CPTP_Module {

	/**
	 * Add Actions.
	 */
	public function add_hook() {
		add_action( 'admin_init', array( $this, 'save_options' ), 30 );
	}

	/**
	 * Save Options.
	 */
	public function save_options() {
		$referrer = wp_get_raw_referer();

		// Verify access.
		if ( ! (
			// Submitted.
			filter_input( INPUT_POST, 'submit' ) &&
			// With verified nonce.
			check_admin_referer( 'update-permalink' ) &&
			// From permalink page.
			$referrer &&
			str_contains( $referrer, 'options-permalink.php' )
		) ) {
			return false;
		}

		// Save settings.
		// Sanitization performed in each setting's sanitize_callback.
		$post_types = CPTP_Util::get_post_types();

		foreach ( $post_types as $post_type ) {
			$structure = filter_input( INPUT_POST, $post_type . '_structure', FILTER_SANITIZE_URL ); // get setting.
			update_option( $post_type . '_structure', $structure );
		}

		$no_taxonomy_structure = ! filter_input( INPUT_POST, 'no_taxonomy_structure', FILTER_VALIDATE_BOOL );
		$add_post_type_for_tax = filter_input( INPUT_POST, 'add_post_type_for_tax', FILTER_VALIDATE_BOOL );

		update_option( 'no_taxonomy_structure', $no_taxonomy_structure );
		update_option( 'add_post_type_for_tax', $add_post_type_for_tax );
		
		update_option( 'cptp_permalink_checked', CPTP_VERSION );
	}

	/**
	 * Fire on uninstall. delete options.
	 *
	 * @static
	 */
	public static function uninstall_hook() {
		foreach ( CPTP_Util::get_post_types() as $post_type ) {
			delete_option( $post_type . '_structure' );
		}

		delete_option( 'no_taxonomy_structure' );
		delete_option( 'add_post_type_for_tax' );
	}
}
