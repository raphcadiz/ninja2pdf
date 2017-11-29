<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF{
    
    private static $instance;

    public static function get_instance()
    {
        if( null == self::$instance ) {
            self::$instance = new Ninja2PDF();
        }

        return self::$instance;
    }

    function __construct(){
        add_action('admin_enqueue_scripts', array( $this, 'admin_scripts' ));
        add_action('wp_enqueue_scripts', array($this, 'public_scripts'));
        add_action('init', array( $this, 'downlaod_completed_pdf' ));
    }

    public function admin_scripts($hook){
        global $post;
        global $post_type;
        // wp_die($hook);

        wp_enqueue_script( 'jquery' );
        wp_register_script('ninja2pdf-vue-main', N2PDF_URL . '/assets/js/vue.min.js', '1.0', true, true );
        wp_enqueue_script('ninja2pdf-vue-main');

        wp_enqueue_media();
        wp_enqueue_script( 'media-upload' );

        if ( ($hook == 'post-new.php' || $_GET['action'] == 'edit') && $post_type == 'ninja_merge') {
            wp_register_script('vue-scripts', N2PDF_URL . '/assets/js/vue-script-v2.js', '1.0', true, true );
            $ninjamerge_local = array(
                'ajaxurl' => admin_url( 'admin-ajax.php' ),
                'meta'  => get_post_meta($post->ID),
                'filename' => basename( get_attached_file( get_post_meta($post->ID, '_pdf_file_id', true) ) )
            );
            wp_localize_script('vue-scripts', 'ninjamerge', $ninjamerge_local );
            wp_enqueue_script('vue-scripts');
        }

        wp_register_style('ninja2pdf-admin-style', N2PDF_URL . '/assets/css/ninja2pdf-admin-style.css', '1.0', true );
        wp_enqueue_style('ninja2pdf-admin-style');
    }

    public function public_scripts(){

    }

    public function downlaod_completed_pdf() {
        if (isset($_GET['download-completed-pdf']) && $_GET['download-completed-pdf'] != ''):
            $file = $_GET['download-completed-pdf'];
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