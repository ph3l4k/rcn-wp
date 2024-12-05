<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class RCN_Cache_Manager {
    public function get( $key ) {
        return get_transient( $key );
    }

    public function set( $key, $value, $expiration = 3600 ) {
        set_transient( $key, $value, $expiration );
    }

    public function delete( $key ) {
        delete_transient( $key );
    }

    public function flush() {
        global $wpdb;
        $wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE ('_transient_rcn_%')" );
    }
}

