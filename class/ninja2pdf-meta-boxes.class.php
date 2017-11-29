<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Metas {

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'ninja2pdf_meta_boxes'));
    }

    public function ninja2pdf_meta_boxes() {
        add_meta_box(
            'ninja2pdf_general_info',
            __( 'Merge Information', 'ninja-pdf' ), 
            array( $this, 'general_info_metabox' ),
            'ninja_merge',
            'normal',
            'high'
        );

        add_meta_box(
            'ninja2pdf_mapping',
            __( 'Merge Mapping', 'ninja-pdf' ), 
            array( $this, 'ninja2pdf_mapping_metabox' ),
            'ninja_merge',
            'normal',
            'high'
        );

        add_meta_box(
            'ninja2pdf_pdf_options',
            __( 'PDF Permissions', 'ninja-pdf' ), 
            array( $this, 'ninja2pdf_pdf_options_metabox' ),
            'ninja_merge',
            'normal'
        );

        add_meta_box(
            'ninja2pdf_integrations',
            __( 'Integrations', 'ninja-pdf' ), 
            array( $this, 'ninja2pdf_integrations_metabox' ),
            'ninja_merge',
            'normal'
        );
    }

    public function general_info_metabox() {
        global $post;
        include_once(N2PDF_PATH_INCLUDES . '/ninja2pdf-general-info-metabox.php');
    }

    public function ninja2pdf_mapping_metabox() {
        global $post;
        include_once(N2PDF_PATH_INCLUDES . '/ninja2pdf-mapping-metabox.php');
    }

    public function ninja2pdf_pdf_options_metabox() {
        global $post;
        $pdf_option_instance = new Ninja2PDF_Pdf_Options;
        $pdf_options = apply_filters('ninja2pdf_pdf_options', array());
        include_once(N2PDF_PATH_INCLUDES . '/ninja2pdf-pdf-options-metabox.php');
    }

    public function ninja2pdf_integrations_metabox() {
        global $post;
        $ninja2pdf_integration_instance = new Ninja2PDF_Integration;
        $ninja_integrations = apply_filters('ninja2pdf_integrations', array());
        $merge_integrations = get_post_meta($post->ID, '_integrations');
        include_once(N2PDF_PATH_INCLUDES . '/ninja2pdf-integrations-metabox.php');
    }

}

new Ninja2PDF_Metas;