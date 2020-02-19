<?php

/**
 * Plugin Name: Myplugin One
 * Description: Demonstration plugin.
 * Version: 1.0
 * Author: Jasom Dotnet
 * Author URI: https://www.jasom.net
 * Plugin URI: https://www.example.com
 * License: MIT License
 * License URI: http://opensource.org/licenses/MIT
 * Text Domain: mypluginslug1
 * Domain Path: /languages
 */
###############
# Defaults and options
###############
define( 'PREFIX_PLUGIN_NAME', 'Myplugin One' );
define( 'PREFIX_PLUGIN_VERSION', '1.0' ); // change verion here as well as in plugin definition
define( 'PREFIX_DOMAIN', 'https://www.example.com' );
define( 'PREFIX_REPOFOLDER', 'repositoryfolder' );

###############
# Logic
###############

function prefix_add_code_to_the_footer() {

    echo PHP_EOL . '<!-- it_works -->' . PHP_EOL;

}

add_action( 'wp_footer', 'prefix_add_code_to_the_footer' );

###############
# Automatic updates
###############

/*
 * Plugin pop-up when new realase is out
 */

function prefix_plugin_info( $res, $action, $args ) {

    // Do nothing if this is not about getting plugin information
    if ($action !== 'plugin_information') {
        return false;
    }

    // Do nothing if it is not our plugin
    if ('mypluginslug1' !== $args->slug) {
        return $res;
    }

    // Trying to get from cache first, to disable cache see https://rudrastyh.com/wordpress/self-hosted-plugin-update.html
    if (false == $remote = get_transient( 'prefix_upgrade_mypluginslug1' )) {

        $remote = wp_remote_get( PREFIX_DOMAIN . '/' . PREFIX_REPOFOLDER . '/get-info.php?slug=mypluginslug1&action=info', array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json'
            ))
        );

        if (!is_wp_error( $remote ) && isset( $remote[ 'response' ][ 'code' ] ) && $remote[ 'response' ][ 'code' ] == 200 && !empty( $remote[ 'body' ] )) {
            set_transient( 'prefix_upgrade_mypluginslug1', $remote, 21600 ); // 6 hours cache
        }
    }

    if (!is_wp_error( $remote )) {

        $remote = json_decode( $remote[ 'body' ] );

        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_homepage;
        $res->download_link = $remote->download_link;
        $res->trunk = $remote->download_link;
        $res->last_updated = $remote->last_updated;
        $res->sections = array(
            'description' => $remote->sections->description, // description tab
            'installation' => $remote->sections->installation, // installation tab
        );
        $res->banners = array(
            'low' => $remote->banners->low,
            'high' => $remote->banners->high,
        );

        return $res;
    }

    return false;

}

add_filter( 'plugins_api', 'prefix_plugin_info', 20, 3 );

/**
 * Plugin update
 */
function prefix_push_update( $transient ) {

    if (empty( $transient->checked )) {
        return $transient;
    }

    // trying to get from cache first, to disable cache comment 10,20,21,22,24
    if (false == $remote = get_transient( 'prefix_upgrade_mypluginslug1' )) {
        // info.json is the file with the actual plugin information on your server
        $remote = wp_remote_get( PREFIX_DOMAIN . '/' . PREFIX_REPOFOLDER . '/get-info.php?slug=mypluginslug1&action=update', array(
            'timeout' => 10,
            'headers' => array(
                'Accept' => 'application/json'
            ))
        );

        if (!is_wp_error( $remote ) && isset( $remote[ 'response' ][ 'code' ] ) && $remote[ 'response' ][ 'code' ] == 200 && !empty( $remote[ 'body' ] )) {
            set_transient( 'prefix_upgrade_mypluginslug1', $remote, 21600 ); // 6 hours cache
        }
    }

    if ($remote) {

        $remote = json_decode( $remote[ 'body' ] );

        // your installed plugin version should be on the line below! You can obtain it dynamically of course
        if ($remote && version_compare( PREFIX_PLUGIN_VERSION, $remote->version, '<' ) && version_compare( $remote->requires, get_bloginfo( 'version' ), '<' )) {
            $res = new stdClass();
            $res->slug = 'mypluginslug1';
            // it could be just mypluginslug1.php if your plugin doesn't have its own directory (my does)
            $res->plugin = 'mypluginslug1/mypluginslug1.php';
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_link;
            $transient->response[ $res->plugin ] = $res;
            //$transient->checked[$res->plugin] = $remote->version;
        }
    }
    return $transient;

}

add_filter( 'site_transient_update_plugins', 'prefix_push_update' );

/**
 * Cache the results to make update process fast
 */
function prefix_after_update( $upgrader_object, $options ) {
    if ($options[ 'action' ] == 'update' && $options[ 'type' ] === 'plugin') {
        // just clean the cache when new plugin version is installed
        delete_transient( 'prefix_upgrade_mypluginslug1' );
    }

}

add_action( 'upgrader_process_complete', 'prefix_after_update', 10, 2 );
