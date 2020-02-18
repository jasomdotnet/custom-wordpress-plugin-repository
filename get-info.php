<?php

/**
 * Custom print_r function for development
 */
function pa( $data ) {

    print '<hr><pre>' . PHP_EOL;
    is_array( $data ) || is_object( $data ) ? print_r( $data ) : var_dump( $data ) . PHP_EOL;
    print '</pre><hr>' . PHP_EOL;

}

/**
 * Configuration class
 */
class Config {

    // repository server domain
    const DOMAIN = 'https://www.domain.com';
    // plugin tested up to version
    const TESTED = '5.3.2';
    // required wordpress version
    const REQUIRES = '5.0';
    // repository server folder
    const DIR = 'repo';
    // array of repo server plugin slugs
    const PLUGINS = ['mypluginslug1', 'mypluginslug2'];

}

/**
 * Repository server
 */
class RepoServer {

    /**
     * Slug of acting plugin
     * @var string
     */
    public $slug;

    /**
     * Data for returning jSon
     * @var array
     */
    public $data = [];

    /**
     * Loaded main PHP script of the plugin with definition values
     * @var array
     */
    public $plugin_definition_file;

    /**
     * Main constructor as router
     */
    public function __construct() {

        if (isset( $_GET[ 'slug' ] )) {
            $this->slug = $_GET[ 'slug' ] ?? null;
            $this->getJson();
        } elseif (isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] === 'cron') {
            $this->cron();
            echo 'done' . PHP_EOL;
        }

    }

    /**
     * Returns update information about plugin in jSon format
     */
    public function getJson() {

        // if requested plugin is in repository
        if (in_array( $this->slug, Config::PLUGINS )) {
            // decode json info file
            if ($info_json = json_decode( file_get_contents( $this->slug . '_info.json' ), TRUE )) {
                // create array
                $this->data[ 'status' ] = 'ok';
                $this->data[ 'name' ] = $info_json[ 'name' ];
                $this->data[ 'slug' ] = $this->slug;
                $this->data[ 'download_link' ] = Config::DOMAIN . '/' . Config::DIR . '/' . $this->slug . '.zip';
                $this->data[ 'version' ] = $info_json[ 'version' ];
                $this->data[ 'requires' ] = Config::REQUIRES;
                $this->data[ 'tested' ] = Config::TESTED;
                $this->data[ 'last_updated' ] = date( "Y-m-d H:i:s", $info_json[ 'created' ] );
                $this->data[ 'upgrade_notice' ] = 'Plugin update is available.';
                $this->data[ 'author' ] = $info_json[ 'author' ];
                $this->data[ 'author_homepage' ] = $info_json[ 'author_homepage' ];
                $this->data[ 'sections' ] = [
                    'description' => $info_json[ 'desc' ],
                    'installation' => 'Upload the plugin to your blog, activate it and that is it!',
                        //'changelog' => '<h4>1.1 –  August 17, 2017</h4><ul><li>Some bugs are fixed.</li><li>Release date.</li></ul>',
                ];
                $this->data[ 'banners' ] = [
                    'low' => Config::DOMAIN . '/' . Config::DIR . '/' . $this->slug . '-banner-772x250.jpg',
                    'high' => Config::DOMAIN . '/' . Config::DIR . '/' . $this->slug . '-banner-1544x500.jpg',
                ];
            } else {
                $this->data[ 'error' ] = 'no_valid_json_info_file';
            }
        } else {
            $this->data[ 'error' ] = 'no_such_plugin';
        }

        // Log request
        $this->logRequest();
        // Send as json
        header( 'Content-Type: application/json' );
        echo json_encode( $this->data );

    }

    /**
     * Run cron jobs for repository script every 6 hours.
     */
    function cron() {

        // Loops through all zip files in folder
        foreach (glob( '*.zip' ) as $filename) {

            // Get plugin slug from zip file
            $this->slug = $this->slugFromFilename( $filename );
            // Prepares name for json info file
            $json_info_file = $this->slug . '_info.json';

            // If plugin is new and has no json info file create one
            if (!file_exists( $json_info_file )) {
                $this->createJsonInfoFile( $filename );
            }

            if ($info_json = json_decode( file_get_contents( $json_info_file ), TRUE )) {
                // If plugin.zip is newer then json info file create new json info file
                if (filemtime( $filename ) > $info_json[ 'created' ]) {
                    $this->createJsonInfoFile( $filename );
                } else {
                    // Do nothing because slug-info.json is up to date
                }
            } else {
                // jSon info file is corrupted, create new one
                $this->createJsonInfoFile( $filename );
            }
        } // endforeach
        // Some info that cron runs
        $this->logCron();

    }

    /**
     *  Create jSon with data for plugin update info request
     * @param string $filename
     */
    function createJsonInfoFile( $filename ) {

        // Deletes previously unzipped folder of plugin
        $this->deleteDirectory( $this->slug );
        // Unzips plugin again
        $this->unzipPlugin( $filename );

        $data[ 'name' ] = $this->getFromDefinition( '/Plugin Name:/' );
        $data[ 'desc' ] = $this->getFromDefinition( '/Description:/' );
        $data[ 'author_homepage' ] = $this->getFromDefinition( '/Author URI:/' );
        $data[ 'author' ] = $this->getFromDefinition( '/Author:/' );
        $data[ 'version' ] = $this->getFromDefinition( '/Version:/' );
        $data[ 'created' ] = filemtime( $filename );
        $data[ 'created_human' ] = date( 'Y-m-d H:i:s', filemtime( $filename ) );

        $name_for_new_json = $this->slug . '_info.json';

        file_put_contents( $name_for_new_json, json_encode( $data ) );

    }

    /**
     * Removes directory using shell command (because it is convenient)
     * @param string $dir Directory to remove
     * @return boolean true when directory was removed successfully, false otherwise
     */
    public function deleteDirectory( $dir ) {

        // Need access to command line on server, as alternative can by use 'shell_exec' or 'exec'
        system( 'rm -rf ' . escapeshellarg( $dir ), $retval );
        // UNIX commands return zero on success
        return $retval === 0;

    }

    /**
     *  Returns plugin slug from zip filename. From 'plugin.zip' returns 'plugin'
     * @param string $filename
     * @return string Plugin slug
     */
    public function slugFromFilename( $filename ) {

        return substr( $filename, 0, -4 );

    }

    /**
     * Extract data from plugin definition header: 'Author URI: https://www.jasom.net' returns 'https://www.jasom.net'
     * @param string $line
     * @return string Value from line separated by ':'
     */
    function getFromDefinition( $pattern ) {


        if (!is_array( $this->plugin_definition_file )) {
            // Loads plugin definition file as an array
            $this->plugin_definition_file = file( $this->slug . '/' . $this->slug . '.php' ) or exit( 'Cannot load plugin definitions according current patten "plugin/plugin.php".' );
        }

        foreach ($this->plugin_definition_file as $line) {
            if (preg_match( $pattern, $line )) {
                $line_for_explosion = $line;
                break;
            }
        }

        $e = explode( ':', $line_for_explosion );
        unset( $e[ 0 ] );
        return trim( implode( ':', $e ) );

    }

    /**
     * Unzips plugin file to current directory
     * @param string $filename
     * @return boolean Returns true if unzipped successfully, false otherwise
     */
    public function unzipPlugin( $filename ) {

        $zip = new ZipArchive();

        if ($zip->open( $filename )) {
            $zip->extractTo( '.' );
            $zip->close();
            //echo 'done';
            return TRUE;
        } else {
            //echo 'error';
            return FALSE;
        }

    }

    /**
     * Function to log jSon request
     */
    public function logRequest() {

        // Domain the request came from
        $e = explode( '; ', $_SERVER[ 'HTTP_USER_AGENT' ] );
        $parse = parse_url( $e[ 1 ] );
        $host = $parse[ 'host' ];
        // Comma separated values (CSV) format
        // Plugin name, wordpress domain, unix timestamp, human readable timestamp
        $log = $this->slug . ',' . $host . ',' . time() . ',' . date( 'Y-m-d H:i:s', time() ) . PHP_EOL;

        error_log( $log, 3, './requests.log' );

    }

    /**
     * Function to log cronjob (feedback it's working)
     */
    public function logCron() {

        error_log( date( 'Y-m-d H:i:s', time() ) . PHP_EOL, 3, './cron.log' );

    }

}

$repo = new RepoServer();
