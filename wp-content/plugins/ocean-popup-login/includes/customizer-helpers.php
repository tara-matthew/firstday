<?php
/**
 * Active callback functions for the customizer
 */

function opl_popup_login_cac_has_custom_text() {
	if ( 'custom' == get_theme_mod( 'opl_popup_login_logged_in', 'logout' ) ) {
		return true;
	} else {
		return false;
	}
}