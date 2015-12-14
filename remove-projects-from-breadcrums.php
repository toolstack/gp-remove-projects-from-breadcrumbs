<?php
/*
Plugin Name: GlotPress Remove Projects from Breadcrumbs
Plugin URI: http://glot-o-matic.com/gp-remove-projects-from-breadcrumbs
Description: Remove the "Projects" link from the breadcrumbs in GlotPress
Version: 0.5
Author: gregross
Author URI: http://toolstack.com
Tags: glotpress, glotpress plugin 
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class GP_Remove_Projects_From_Breadcrumbs {
	public $id = 'gp-remove-projects-from-breadcrumbs';

	public $errors  = array();
	public $notices = array();

	public function __construct() {

		add_action( 'gp_breadcrumb_items', array( $this, 'gp_breadcrumb_items'), 10, 1 );
		add_action( 'gp_nav_menu_items', array( $this, 'gp_nav_menu_items' ), 10, 2 );
		add_action( 'gp_logo_url', array( $this, 'gp_logo_url' ), 10, 1 );
	}

	public function gp_breadcrumb_items( $breadcrums ) {
		
		if( is_array( $breadcrums ) ) { 
			if( is_array( $breadcrums[0] ) ) {
		
				unset( $breadcrums[0][0] ); 
			}
			else {
				unset( $breadcrums[0] ); 
			}
		}

		return $breadcrums;
	}
	
	public function gp_nav_menu_items( $items, $locaiton ) {
		$as = array_search( __('Projects'), $items );
		
		if( $as !== FALSE ) { unset( $items[$as] ); }
		
		return $items; 
	}
	
	public function gp_logo_url( $url ) {
		
		if( gp_const_get('GP_REMOVE_PROJECTS_FROM_BREADCRUMS_LOGO_URL') ) { 
			$url = $url . GP_REMOVE_PROJECTS_FROM_BREADCRUMS_LOGO_URL;
		}
		
		return $url;
	}

}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_remove_projects_from_breadcrumbs_init' );

// This function creates the plugin.
function gp_remove_projects_from_breadcrumbs_init() {
	GLOBAL $gp_remove_projects_from_breadcrumbs;
	
	$gp_remove_projects_from_breadcrumbs = new GP_Remove_Projects_From_Breadcrumbs;
}