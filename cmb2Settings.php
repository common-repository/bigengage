<?php
/**
 * Theme Options
 * @version 1.1.3
 */
class bigengage_pixel_placement_Admin {

	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	private $key = 'bigengage_pixel_placement_options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'bigengage_pixel_placement_option_metabox';

	/**
	 * Array of metaboxes/fields
	 * @var array
	 */
	protected $option_metabox = array();

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';
	
	private function load_dependencies() {

		/**
		 * The class responsible for adding the sidebar widget
		 */
		require_once 'includes/class-bigengage-sidebar-widget.php';
	}

	/**
	 * Constructor
	 * @since 1.1.3
	 */
	public function __construct() {
		// Set our title
		$this->title = __( 'BigEngage', 'bigengage_pixel_placement' );
		$this->load_dependencies();
	}

	/**
	 * Register sidebar widget
	 *
	 * @since    1.1.3
	 */
	public function sidebar_widget() {
		register_widget( 'BigEngage_Sidebar_Widget' );
	}
	
	public function settings_link($links) {
	  $settings_link = '<a href="admin.php?page=bigengage-settings">Settings</a>';
	  array_unshift($links, $settings_link);
	  return $links;
	}
	
	/**
	 * Initiate our hooks
	 * @since 1.1.3
	 */
	public function hooks() {
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'add_options_page' ) );
		add_action( 'cmb2_init', array( $this, 'add_options_page_metabox' ) );
		add_action('wp_head',array( $this, 'add_header_code' ) );
		//add_action('wp_footer',array( $this, 'add_footer_code' ) );
		add_action('wp_head', array( $this, 'change_bar_color' ) );
		add_action('admin_head', array( $this, 'change_bar_color' ) );
		
		$autoEmbed = get_option('bigengage_wordpress_auto_embed');
		if (empty($autoEmbed) || $autoEmbed == 'yes') {
			//Embed form
			add_filter( 'the_content',array( $this,'add_post_containers' ) );
		}
		// Sidebar widget
		add_action( 'widgets_init',array( $this, 'sidebar_widget' ) );
	}

		
	public function change_bar_color() {
	?>
		<style>
		#wpadminbar{
		background: #07abfc !important;
		}
		</style>
	<?php
	}
	/**
	 * Adds BigEngage form container in middle of paragraphs
	 *
	 * @since    1.1.3
	 */
	function insert_form_after_paragraph($insertion, $paragraph_id, $content) {
	  $closing_p = '</p>';
	  $paragraphs = explode($closing_p, $content);
	  if ($paragraph_id == "middle") {
	    $paragraph_id = round(sizeof($paragraphs)/2);
	  }

	  foreach ($paragraphs as $index => $paragraph) {
	    if (trim($paragraph)) {
	      $paragraphs[$index] .= $closing_p;
	    }

	    if ($paragraph_id == $index + 1) {
	      $paragraphs[$index] .= $insertion;
	    }
	  }
	  return implode('', $paragraphs);
	}

	/**
	 * Adds post containers for before, after and in the middle of post
	 *
	 * @since    1.1.3
	 */
	public function add_post_containers($content) {
		if (is_single() || is_page()) {
		  $content = $this->insert_form_after_paragraph("<div class='bigengage-wordpress-forms-in-post-middle' style='display: none !important;'></div>", "middle", $content);
		  $content = "<div class='bigengage-wordpress-forms-before-post' style='display: none !important;'></div>" . $content . "<div class='bigengage-wordpress-forms-after-post' style='display: none !important;'></div>";
		}

		return $content;
	}
	
	/**
	 * Register our setting to WP
	 * @since  1.1.3
	 */
	public function init() {
		register_setting( $this->key, $this->key );
	}

	
	
	/**
	 * Add menu options page
	 * @since 1.1.3
	 */
	public function add_options_page() {
		$this->options_page = add_menu_page( $this->title, $this->title, 'manage_options', $this->key, array( $this, 'admin_page_display' ),plugins_url( 'images/logo.png' , __FILE__ ));
		
		//Adding Setting sub menu page
		add_submenu_page( $this->key, 'BigEngage Settings', 'Settings', 'manage_options', 'bigengage-settings', array($this, 'settings_page') );
		
	}

	function getSetting($settingName) {
      return get_option('bigengage_wordpress_'. $settingName);
    }

    function setSetting($settingName, $value=null) {
      return update_option('bigengage_wordpress_'. $settingName, $value);
    }
	/**
	 * Settings Page
	 *
	 * @since    1.1.3
	 */
	public function settings_page() {
		if ($_POST) {
			$this->setSetting('auto_embed', $_POST['auto_embed']);
		}
		require_once(plugin_dir_path(__FILE__) . 'partials/bigengage-settings.php');
	}
	
	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  1.1.3
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2_options_page <?php echo $this->key; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->key ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  1.1.3
	 * @param  array $meta_boxes
	 * @return array $meta_boxes
	 */
	function add_options_page_metabox() {

		$cmb = new_cmb2_box( array(
			'id'      => $this->metabox_id,
			'hookup'  => false,
			'show_on' => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->key, )
			),
		) );

		// Set our CMB2 fields

		$cmb->add_field( array(
			'name' => __( 'Paste the BigEngage JavaScript code for WordPress here.', 'bigengage_pixel_placement' ),
			'desc' => __( 'If you don’t have it yet, then visit BigEngage.com, and create your free account. Once you are in, on the bottom left of the dashboard look for “My JavaScript” button. Click the button to get your WordPress JavaScript.  Come back here and paste that code and press Save.', 'bigengage_pixel_placement' ),
			'id'   => 'header_code',
			'type' => 'textarea_code',
			//'default' => 'Default Text',
		) );

	/*	$cmb->add_field( array(
			'name' => __( 'Footer Code', 'bigengage_pixel_placement' ),
			'desc' => __( 'Place the bigengage code that needs to appear in the footer of the page here.', 'bigengage_pixel_placement' ),
			'id'   => 'footer_code',
			'type' => 'textarea_code',
			//'default' => 'Default Text',
		) ); */

	}
	
	public function add_header_code(){
		echo PHP_EOL.cmb2_get_option( $this->key, 'header_code' ).PHP_EOL;
	}


	/*public function add_footer_code(){
		echo PHP_EOL.cmb2_get_option( $this->key, 'footer_code' ).PHP_EOL;
	}*/
	
	/**
	 * Defines the theme option metabox and field configuration
	 * @since  1.1.3
	 * @return array
	 */
	public function option_metabox() {
		return ;
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  1.1.3
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'fields', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

// Get it started
$GLOBALS['bigengage_pixel_placement_Admin'] = new bigengage_pixel_placement_Admin();
$GLOBALS['bigengage_pixel_placement_Admin']->hooks();

/**
 * Helper function to get/return the bigengage_pixel_placement_Admin object
 * @since  1.1.3
 * @return bigengage_pixel_placement_Admin object
 */
function bigengage_pixel_placement_Admin() {
	global $bigengage_pixel_placement_Admin;
	return $bigengage_pixel_placement_Admin;
}

/**
 * Wrapper function around cmb2_get_option
 * @since  1.1.3
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function bigengage_pixel_placement_get_option( $key = '' ) {
	global $bigengage_pixel_placement_Admin;
	return cmb2_get_option( $bigengage_pixel_placement_Admin->key, $key );
	
}
