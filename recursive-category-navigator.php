<?php
/**
 * Plugin Name: Recursive Category Navigator
 * Description: Advanced navigation for WooCommerce categories with performance optimization
 * Version: 3.0.1
 * Author: Pablo Sánchez
 * Text Domain: recursive-category-navigator
 * Domain Path: /languages
 * Requires at least: 5.6
 * Requires PHP: 7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define( 'RCN_VERSION', '3.0.1' );
define( 'RCN_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'RCN_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * The main plugin class
 */
final class Recursive_Category_Navigator {

    /**
     * The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Main Recursive_Category_Navigator Instance
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Recursive_Category_Navigator Constructor
     */
    public function __construct() {
        $this->includes();
        $this->init_hooks();
    }

    /**
     * Include required core files
     */
    public function includes() {
        require_once RCN_PLUGIN_DIR . 'includes/class-rcn-category-renderer.php';
        require_once RCN_PLUGIN_DIR . 'includes/class-rcn-cache-manager.php';
        require_once RCN_PLUGIN_DIR . 'includes/class-rcn-settings.php';
    }

    /**
     * Hook into actions and filters
     */
    private function init_hooks() {
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ), -1 );
        add_action( 'init', array( $this, 'init' ), 0 );
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        $this->load_plugin_textdomain();
        
        if ( class_exists( 'WooCommerce' ) ) {
            $this->init_category_navigator();
        } else {
            add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
        }

        // Check if Elementor is installed and activated
        if ( did_action( 'elementor/loaded' ) ) {
            add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_elementor_widget' ) );
        }
    }

    /**
     * Load Localisation files
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( 'recursive-category-navigator', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Initialize the category navigator functionality
     */
    private function init_category_navigator() {
        $category_renderer = new RCN_Category_Renderer();
        $cache_manager = new RCN_Cache_Manager();
        $settings = new RCN_Settings();

        // Add shortcode
        add_shortcode( 'recursive_category_nav', array( $category_renderer, 'render_category_nav' ) );

        // Add other hooks and filters as needed
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_filter( 'woocommerce_product_categories_widget_args', array( $this, 'modify_category_widget' ), 10, 1 );
        add_action( 'woocommerce_before_shop_loop', array( $category_renderer, 'display_subcategories' ), 5 );
        add_filter( 'woocommerce_product_query', array( $category_renderer, 'filter_products_by_current_category' ), 10, 1 );
    }

    /**
     * Register Elementor widget
     */
    public function register_elementor_widget() {
        require_once RCN_PLUGIN_DIR . 'includes/class-rcn-elementor-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new RCN_Elementor_Widget() );
    }

    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style( 'rcn-styles', RCN_PLUGIN_URL . 'assets/css/rcn-styles.css', array(), RCN_VERSION );
        wp_enqueue_script( 'rcn-scripts', RCN_PLUGIN_URL . 'assets/js/rcn-scripts.js', array( 'jquery' ), RCN_VERSION, true );
    }

    /**
     * Modify the category widget to show only top-level categories
     */
    public function modify_category_widget( $args ) {
        $args['parent'] = 0;
        $args['hide_empty'] = true; // Only show categories with products
        return $args;
    }

    /**
     * Admin notice for missing WooCommerce
     */
    public function woocommerce_missing_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'Recursive Category Navigator requires WooCommerce to be installed and active.', 'recursive-category-navigator' ); ?></p>
        </div>
        <?php
    }

    /**
     * On plugins loaded
     */
    public function on_plugins_loaded() {
        do_action( 'rcn_loaded' );
    }
}

/**
 * Returns the main instance of Recursive_Category_Navigator
 */
function RCN() {
    return Recursive_Category_Navigator::instance();
}

// Global for backwards compatibility
$GLOBALS['recursive_category_navigator'] = RCN();

