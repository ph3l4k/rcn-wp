<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class RCN_Settings {
    private $options;

    public function __construct() {
        $this->options = get_option( 'rcn_settings', array() );
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    public function add_settings_page() {
        add_options_page(
            __( 'Recursive Category Navigator Settings', 'recursive-category-navigator' ),
            __( 'RCN Settings', 'recursive-category-navigator' ),
            'manage_options',
            'rcn-settings',
            array( $this, 'render_settings_page' )
        );
    }

    public function register_settings() {
        register_setting( 'rcn_settings', 'rcn_settings', array( $this, 'sanitize_settings' ) );

        add_settings_section(
            'rcn_general_settings',
            __( 'General Settings', 'recursive-category-navigator' ),
            array( $this, 'render_general_settings_section' ),
            'rcn-settings'
        );

        add_settings_field(
            'rcn_cache_duration',
            __( 'Cache Duration (seconds)', 'recursive-category-navigator' ),
            array( $this, 'render_cache_duration_field' ),
            'rcn-settings',
            'rcn_general_settings'
        );
    }

    public function sanitize_settings( $input ) {
        $sanitized = array();
        if ( isset( $input['cache_duration'] ) ) {
            $sanitized['cache_duration'] = absint( $input['cache_duration'] );
        }
        return $sanitized;
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'rcn_settings' );
                do_settings_sections( 'rcn-settings' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_general_settings_section() {
        echo '<p>' . __( 'Configure general settings for the Recursive Category Navigator plugin.', 'recursive-category-navigator' ) . '</p>';
    }

    public function render_cache_duration_field() {
        $value = isset( $this->options['cache_duration'] ) ? $this->options['cache_duration'] : 3600;
        echo '<input type="number" name="rcn_settings[cache_duration]" value="' . esc_attr( $value ) . '" />';
    }

    public function get( $key, $default = null ) {
        return isset( $this->options[$key] ) ? $this->options[$key] : $default;
    }
}

