<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Confirmation_Page_Download {

    public function __construct() {
        add_filter('ninja2pdf_integrations', array( $this, 'register_integration' ), 1);
        add_action('ninja2pdf_after_merge', array( $this, 'process_integration' ), 10, 3);
        add_shortcode('ninja2pdf_direct_download', array( $this, 'ninja2pdf_direct_download' ));
        add_action('init', array( $this, 'downlaod_merged_pdf' ));
    }

    public function register_integration($array) {
        $confirmation_page_download = array(
            'confirmation_page_download' => array(
                'key'       => 'confirmation_page_download',
                'label'     => 'Confirmation Page Download',
                'fields'    => array(
                    array (
                        'name'          => 'enable',
                        'type'          => 'hidden',
                        'description'   => 'Use <strong style="color: #ca6535">[ninja2pdf_direct_download]</strong> on the success page.'
                    ),
                )
            )
        );

        return array_merge( $array, $confirmation_page_download );
    }

    public function process_integration($final_file, $file_name, $integration) {
        if (!array_key_exists('confirmation_page_download', $integration))
            return;
        
        set_transient( 'ninjamerge_download_file', $final_file );

    }

    public function ninja2pdf_direct_download() {
        $url = get_site_url() . '?download-pdf=true';

        return '<a href="'.$url.'" target="_blank">here</a>';
    }

    public function downlaod_merged_pdf() {
        if (isset($_GET['download-pdf']) && $_GET['download-pdf'] == 'true'):
            $file = get_transient( 'ninjamerge_download_file' );
            if(!empty($file)){
                $final_file = $file;
                if (file_exists($final_file)) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="'.basename($final_file).'"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($final_file));
                    readfile($final_file);
                    exit;
                }
            }
        endif;
    }
}

new Confirmation_Page_Download;