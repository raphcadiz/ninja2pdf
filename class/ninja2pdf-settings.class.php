<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Settings {

    public function __construct() {
        add_action('admin_menu', array( $this, 'admin_menus'), 10 );
        add_action('admin_init', array( $this, 'register_settings' ));
    }

    public function register_settings() {
        register_setting( 'ninja2pdf_settings', 'ninja2pdf_settings', '' );
        register_setting( 'ninja2pdf_completed_merges', 'ninja2pdf_completed_merges', '' );
    }

    public function admin_menus(){
        add_submenu_page ( 'edit.php?post_type=ninja_merge' , 'Settings' , 'Settings' , 'manage_options' , 'ninja2pdf-settings' , array( $this , 'ninja2pdf_settings_page' ));
        add_submenu_page ( 'edit.php?post_type=ninja_merge' , 'Completed Merges' , 'Completed Merges' , 'manage_options' , 'completed-merges' , array( $this , 'ninja2pdf_completed_merges' ));
    }

    public function ninja2pdf_settings_page() {
        $ninja2pdf_settings = get_option('ninja2pdf_settings');
        include_once(N2PDF_PATH_INCLUDES . '/settings.php');
    }

    public function ninja2pdf_completed_merges() {
        $ninja2pdf_completed_merges = get_option('ninja2pdf_completed_merges');
        include_once(N2PDF_PATH_INCLUDES . '/completed-merges.php');
    }
}

new Ninja2PDF_Settings;