<?php
/*
	Plugin Name: Better Admin Bar
	Plugin URI: http://kubiq.sk
	Description: Better Admin Bar
	Version: 1.9
	Author: Jakub Novák
	Author URI: http://kubiq.sk
*/

if (!class_exists('better_admin_bar')) {
	class better_admin_bar {
		var $domain = 'better_admin_bar';
		var $plugin_admin_page;
		var $settings;
		
		function admin_bar(){ $this->__construct(); }	
		
		function __construct(){
			$mo = plugin_dir_path(__FILE__) . 'languages/' . $this->domain . '-' . get_locale() . '.mo';
			load_textdomain($this->domain, $mo);
			add_action( 'admin_menu', array( &$this, 'plugin_menu_link' ) );
			add_action( 'init', array(&$this, "plugin_init"));
		}
		
		function plugin_init(){
			$this->settings = get_option( "admin_bar_settings" );
			
			add_action( 'wp_head', array(&$this, 'advanced_admin_bar'), 11 );

			if(isset($this->settings[ 'show_admin' ])){
				if (!current_user_can('administrator') && !is_admin()) {
					add_action('get_header', array(&$this, 'remove_admin_login_header'));
				}
			}else{
				if(isset($this->settings[ 'hide_admin_bar' ])){
					add_action('get_header', array(&$this, 'remove_admin_login_header'));
				}
			}
		}

		function remove_admin_login_header() {
			remove_action('wp_head', '_admin_bar_bump_cb');
		}

		function advanced_admin_bar() {
			$inactive = $this->settings[ 'inactive_opacity' ] == "" ? 30 : $this->settings[ 'inactive_opacity' ];
			$active = $this->settings[ 'active_opacity' ] == "" ? 100 : $this->settings[ 'active_opacity' ];
			$style = '<style type="text/css" media="screen">'.
				'html,html body,* html body{margin-top:0px !important}'.
				'#wpadminbar{'.
					'filter:alpha(opacity='.$inactive.');'.
					'opacity:'.((int)$inactive/100).';'.
					'-webkit-transition:all .3s ease;'.
					'-moz-transition:all .3s ease;'.
					'-o-transition:all .3s ease;'.
					'transition:all .3s ease'.
				'}'.
				'#wpadminbar:hover{'.
					'filter:alpha(opacity='.$active.');'.
					'opacity:'.((int)$active/100).
				'}';
			if(isset($this->settings[ 'autohide' ])){
				$style .= '#wpadminbar{'.
					'top:-'.(32-((float)$this->settings['hover_area']<5?5:(float)$this->settings['hover_area'])).'px;'.
				'}'.
				'#wpadminbar:hover{top:0px}';
			}
			$style .= '</style>';

			if(isset($this->settings[ 'show_admin' ])){
				if (!current_user_can('administrator') && !is_admin()) {
					show_admin_bar(false);
				}else{
					echo $style;
				}
			}else{
				if(isset($this->settings[ 'hide_admin_bar' ])){
					show_admin_bar(false);
				}else{
					echo $style;
				}
			}
		}

		function filter_plugin_actions($links, $file) {
		   $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
		   array_unshift( $links, $settings_link );
		   return $links;
		}
		
		function plugin_menu_link() {
			$this->plugin_admin_page = add_submenu_page(
				'options-general.php',
				__( 'Better Admin Bar', $this->domain ),
				__( 'Better Admin Bar', $this->domain ),
				'manage_options',
				basename(__FILE__),
				array( $this, 'admin_options_page' )
			);
			add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'filter_plugin_actions'), 10, 2 );
		}

		function admin_options_page() {
			if ( get_current_screen()->id != $this->plugin_admin_page ) return;
			$this->settings = get_option( "admin_bar_settings" );
			if(isset($_POST['plugin_sent'])){
				$this->settings = $_POST;
				update_option( "admin_bar_settings", $this->settings );
			} ?>
			<div class="wrap">
				<br>
				<h1><?php _e( 'Better Admin Bar', $this->domain ); ?></h1>
				<?php if(isset($_POST['plugin_sent'])) echo '<div id="message" class="below-h2 updated"><p>'.__( 'Settings saved.' ).'</p></div>'; ?>
				<form method="post" action="<?php admin_url( 'options-general.php?page=' . basename(__FILE__) ); ?>">
					<input type="hidden" name="plugin_sent" value="1">
					<table class="form-table">
						<tr>
							<th>
								<label for="hide_admin_bar"><?php _e('Hide admin bar for all users', $this->domain) ?></label> 
							</th>
							<td>
								<input type="checkbox" name="hide_admin_bar" value="checked" id="hide_admin_bar" <?php echo isset($this->settings["hide_admin_bar"]) ? $this->settings["hide_admin_bar"] : "" ?>>
							</td>
						</tr>
						<tr>
							<th>
								<label for="show_admin"><?php _e('Hide admin bar for all users except admin', $this->domain) ?></label> 
							</th>
							<td>
								<input type="checkbox" name="show_admin" value="checked" id="show_admin" <?php echo isset($this->settings["show_admin"]) ? $this->settings["show_admin"] : "" ?>>
							</td>
						</tr>
						<tr>
							<th>
								<label for="inactive_opacity"><?php _e("Admin bar opacity (inactive):", $this->domain) ?></label> 
							</th>
							<td>
								<input type="text" size="5" name="inactive_opacity" placeholder="30" value="<?php echo $this->settings["inactive_opacity"]; ?>" id="inactive_opacity"> %
							</td>
						</tr>
						<tr>
							<th>
								<label for="active_opacity"><?php _e("Admin bar opacity on hover (active):", $this->domain) ?></label> 
							</th>
							<td>
								<input type="text" size="5" name="active_opacity" placeholder="100" value="<?php echo $this->settings["active_opacity"]; ?>" id="active_opacity"> %
							</td>
						</tr>
						<tr>
							<th>
								<label for="autohide"><?php _e('Auto-hide admin bar (show on hover)', $this->domain) ?></label> 
							</th>
							<td>
								<input type="checkbox" name="autohide" value="checked" id="autohide" <?php echo isset($this->settings["autohide"]) ? $this->settings["autohide"] : "" ?>>
							</td>
						</tr>
						<tr>
							<th>
								<label for="hover_area"><?php _e("Top hover area height (if autohide):", $this->domain) ?></label> 
							</th>
							<td>
								<input type="text" size="5" name="hover_area" placeholder="5" value="<?php echo $this->settings["hover_area"]; ?>" id="hover_area"> px
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" class="button button-primary button-large" value="<?php _e( 'Save' ) ?>"></p>
				</form>
			</div><?php
		}
	}
}

if (class_exists('better_admin_bar')) { 
	$better_admin_bar_var = new better_admin_bar();
} ?>