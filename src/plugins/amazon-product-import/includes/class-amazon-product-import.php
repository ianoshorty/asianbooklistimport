<?php

require_once(__DIR__ .'/../vendor/autoload.php'); 

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/ianoshorty/amazon-product-import
 * @since      1.0.0
 *
 * @package    Amazon_Product_Import
 * @subpackage Amazon_Product_Import/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Amazon_Product_Import
 * @subpackage Amazon_Product_Import/includes
 * @author     Ian Outterside
 */
class Amazon_Product_Import {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Amazon_Product_Import_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'AMAZON_PRODUCT_IMPORT_VERSION' ) ) {
			$this->version = AMAZON_PRODUCT_IMPORT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'amazon-product-import';

		$this->load_credentials();
		$this->load_dependencies();
		$this->define_admin_hooks();
	}

	/**
	 * Read the .env file to import Amazon Product SDK credentials
	 *
	 * @since    1.0.0
	 */
	public function load_credentials() {
		$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
		$dotenv->load();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Amazon_Product_Import_Loader. Orchestrates the hooks of the plugin.
	 * - Amazon_Product_Import_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amazon-product-import-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-amazon-product-import-admin.php';

		$this->loader = new Amazon_Product_Import_Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Amazon_Product_Import_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'register_import_settings_page' );
		$this->loader->add_action( 'admin_post_amazon_product_import_triggered', $plugin_admin, 'import_products' );
	
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'amazon_identifier_add_meta_box' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'amazon_identifier_save_post_meta',  10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Amazon_Product_Import_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
