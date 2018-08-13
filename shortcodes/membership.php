<?php
if ( ! shortcode_exists( 'fed_membership' ) && ! function_exists( 'fed_membership' ) ) {
	/**
	 * Add Shortcode to the page.
	 *
	 * @return string
	 */
	function fed_membership( ) {

		$templates = new FED_Template_Loader(BC_FED_M_PLUGIN_DIR);
		ob_start();
		$templates->get_template_part( 'fed_membership' );
		return ob_get_clean();
	}

	add_shortcode( 'fed_membership', 'fed_membership' );
}