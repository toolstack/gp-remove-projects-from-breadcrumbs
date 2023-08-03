<?php
/*
Plugin Name: GP Remove Projects from Breadcrumbs
Plugin URI: http://glot-o-matic.com/gp-remove-projects-from-breadcrumbs
Description: Remove the "Projects" link from the breadcrumbs in GlotPress
Version: 1.0
Author: Greg Ross
Author URI: https://toolstack.com/
Text Domain: gp-remove-projects-from-breadcrumbs
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
		add_filter( 'gp_home_title', array( $this, 'gp_home_title' ), 10, 1 );

		// Add the admin page to the WordPress settings menu.
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10, 1 );
	}
	
	public function load_text_domain() {
		load_plugin_textdomain( gp-remove-projects-from-breadcrumbs, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
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
		$as = array_search( __('Projects', 'gp-remove-projects-from-breadcrumbs'), $items );
		
		if( $as !== FALSE ) { unset( $items[$as] ); }
		
		return $items; 
	}
	
	public function gp_home_title( $title ) {
		return $title . '<script type="text/javascript">jQuery(\'a[rel="home"\').attr("href", "' . gp_url_project( get_option( 'gp_rpfbc_logo_project' ) ) . '")</script>';
	}

	// This function adds the admin settings page to WordPress.
	public function admin_menu() {
		add_options_page( __('GlotPress Remove Projects from Breadcrumbs', 'gp-remove-projects-from-breadcrumbs'), __('GlotPress Remove Projects from Breadcrumbs', 'gp-remove-projects-from-breadcrumbs'), 'manage_options', basename( __FILE__ ), array( $this, 'admin_page' ) );
	}
	
	// This function displays the admin settings page in WordPress.
	public function admin_page() {
		// If the current user can't manage options, display a message and return immediately.
		if( ! current_user_can( 'manage_options' ) ) { _e('You do not have permissions to this page!', 'gp-remove-projects-from-breadcrumbs'); return; }
		
		// If the user has saved the settings, commit them to the database.
		if( array_key_exists( 'save_gp_rpfbc', $_POST ) ) {
			
			// If the API key value is being saved, store it in the global key setting.
			if( array_key_exists( 'gp_rpfbc_logo_project', $_POST ) ) {
				// Make sure to sanitize the data before saving it.
				$this->key = sanitize_text_field( $_POST['gp_rpfbc_logo_project'] );
			}	
			
			// Update the option in the database.
			update_option( 'gp_rpfbc_logo_project', $this->key );
		}

	?>	
<div class="wrap">
	<h2><?php esc_html_e('GlotPress Remove Projects from Breadcrumbs', 'gp-remove-projects-from-breadcrumbs');?></h2>

	<form method="post" action="options-general.php?page=gp-remove-projects-from-breadcrumbs.php" >	
		<table class="form-table">
			<tr>
				<th><label for="gp_rpfbc_logo_project"><?php esc_html_e('Project Slug','gp-remove-projects-from-breadcrumbs');?></label></th>
				<td>
				<input type="text" id="gp_rpfbc_logo_project" name="gp_rpfbc_logo_project" size="40" value="<?php echo htmlentities( get_option( 'gp_rpfbc_logo_project' ) );?>">
				<p class="description"><?php esc_html_e('Enter project slug to use for the logo url (leave blank to use the default).', 'gp-remove-projects-from-breadcrumbs');?></p>
				</td>
			</tr>
		</table>
		
		<?php submit_button( __('Save', 'gp-remove-projects-from-breadcrumbs'), 'primary', 'save_gp_rpfbc' ); ?>
		
	</form>
	
</div>
<?php		
	}

}

// Add an action to WordPress's init hook to setup the plugin.  Don't just setup the plugin here as the GlotPress plugin may not have loaded yet.
add_action( 'gp_init', 'gp_remove_projects_from_breadcrumbs_init' );

// This function creates the plugin.
function gp_remove_projects_from_breadcrumbs_init() {
	GLOBAL $gp_remove_projects_from_breadcrumbs;
	
	$gp_remove_projects_from_breadcrumbs = new GP_Remove_Projects_From_Breadcrumbs;
}
