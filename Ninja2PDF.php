<?php
/**
 * Plugin Name: Ninja Forms to PDF
 * Plugin URI:  https://www.gravity2pdf.com
 * Description: Convert Ninja Form data to Pdf Form Data
 * Version:     1.0
 * Author:      gravity2pdf
 * Author URI:  https://github.com/raphcadiz
 * Text Domain: ninja-pdf
 */

if (!class_exists('Ninja2PDF')):

    define( 'N2PDF_PATH', dirname( __FILE__ ) );
    define( 'N2PDF_PATH_INCLUDES', dirname( __FILE__ ) . '/includes' );
    define( 'N2PDF_PATH_CLASS', dirname( __FILE__ ) . '/class' );
    define( 'N2PDF_PATH_INTEGRATIONS', dirname( __FILE__ ) . '/integrations' );
    define( 'N2PDF_FOLDER', basename( N2PDF_PATH ) );
    define( 'N2PDF_URL', plugins_url() . '/' . N2PDF_FOLDER );
    define( 'N2PDF_URL_INCLUDES', N2PDF_URL . '/includes' );
    define( 'N2PDF_URL_CLASS', N2PDF_URL . '/class' );
    define( 'N2PDF_URL_INTEGRATIONS', N2PDF_URL . '/integrations' );
    define( 'N2PDF_VERSION', 1.0 );

    register_activation_hook( __FILE__, 'ninja2pdf_activation' );
    function ninja2pdf_activation(){

        if ( ! class_exists('Ninja_Forms') ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die('Sorry, but this plugin requires the Ninja Forms to be installed and active.');
        }

        if ( ! wp_next_scheduled( 'ninja2pdf_weekly_scheduled_events' ) ) {
            wp_schedule_event( current_time( 'timestamp', true ), 'weekly', 'ninja2pdf_weekly_scheduled_events' );
        }

    }

    add_action( 'admin_init', 'ninja2pdf_activate' );
    function ninja2pdf_activate(){
        if ( ! class_exists('Ninja_Forms') ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
    }

    /*
     * include necessary files
     */
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-main.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-post-type.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-settings.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-api.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-merges.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-pdf-options.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-integrations.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-meta-boxes.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-processing.class.php');
    require_once(N2PDF_PATH_CLASS . '/ninja2pdf-license-handler.class.php');
    require_once(N2PDF_PATH_INCLUDES . '/functions.php');

    /* Intitialize licensing
     * for this plugin.
     */
    if( class_exists( 'Ninja2PDF_License_Handler' ) ) {
        $ninja2pdf = new Ninja2PDF_License_Handler( __FILE__, 'Ninja Forms to PDF', N2PDF_VERSION, 'gravity2pdf');
    }

    /*
     * register default integrations
     */
    require_once(N2PDF_PATH_INTEGRATIONS . '/confirmation-page-download.php');
    require_once(N2PDF_PATH_INTEGRATIONS . '/email.php');


    add_action( 'plugins_loaded', array( 'Ninja2PDF', 'get_instance' ) );
endif;