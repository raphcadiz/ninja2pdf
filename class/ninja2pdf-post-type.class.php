<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Post_Type {

    public function __construct() {
        add_action('init', array($this, 'register_post_type'));
        add_filter('manage_edit-ninja_merge_columns', array($this, 'merge_edit_columns'));
        add_action('manage_posts_custom_column', array($this, 'merges_extra_columns'), 10, 2);
        add_action('init', array($this, 'disable_supports'));
        add_action('save_post', array($this, 'save_field' ));
    }

    public function register_post_type() {
        register_post_type( 'ninja_merge', array(
            'labels' => array(
                'name' => __('Ninja 2 PDF', 'ninja-pdf'),
                'singular_name' => __('Merge', 'ninja-pdf'),
                'add_new' => _x('New Merge', 'Merge', 'ninja-pdf' ),
                'add_new_item' => __('Add New Merge', 'ninja-pdf' ),
                'edit_item' => __('Edit Merge', 'ninja-pdf' ),
                'new_item' => __('New Merge', 'ninja-pdf' ),
                'view_item' => __('View Merge', 'ninja-pdf' ),
                'search_items' => __('Search Merges', 'ninja-pdf' ),
                'not_found' =>  __('No Merges found', 'ninja-pdf' ),
                'not_found_in_trash' => __('No Merges found in Trash', 'ninja-pdf' ),
            ),
            'description' => __('Ninja2PDF Merges', 'ninja-pdf'),
            'public' => false,
            'publicly_queryable' => true,
            'query_var' => true,
            'rewrite' => true,
            'exclude_from_search' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 60, // probably have to change, many plugins use this
            'menu_icon' => 'dashicons-media-document',
            'supports' => array(
                'title'
            ),
        ));

    }


    function merge_edit_columns($columns) {
        $newcolumns = array(
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'pdf-file' => esc_html__('PDF', 'ninja-pdf'),
            'form'  => esc_html__('Form', 'ninja-pdf')
        );
        
        $columns= array_merge($newcolumns, $columns);
        
        return $columns;
    }

    public function merges_extra_columns($column, $post_id) {

        if ($column == 'pdf-file') {
            $pdf = get_post_meta($post_id, '_pdf_file_id', true);
            if ($pdf) {
                echo basename( get_attached_file($pdf) );
            } else {
                echo 'No PDF';
            }
        }
        elseif ($column == 'form') {
            $form_id = get_post_meta($post_id, '_form_id', true);
            if ($form_id) {
                $form = Ninja_Forms()->form($form_id)->get();
                echo $form->get_setting('title');
            } else {
                echo '';
            }
        }
        
    }

    public function disable_supports() {
        remove_post_type_support( 'ninja_merge', 'comments' );
    }

    public function save_field( $post_id ){
        // Avoid autosaves
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $slug = 'ninja_merge';
        if ( ! isset( $_POST['post_type'] ) || $slug != $_POST['post_type'] ) {
            return;
        }

        if ( isset( $_POST['_merge_file_name']  ) ) 
            update_post_meta( $post_id, '_merge_file_name',  $_POST['_merge_file_name']);

        if ( isset( $_POST['_file_timestamps']  ) ) 
            update_post_meta( $post_id, '_file_timestamps',  $_POST['_file_timestamps']);
        else
            update_post_meta( $post_id, '_file_timestamps',  0);
        
        if ( isset( $_POST['_pdf_file_id']  ) ) 
            update_post_meta( $post_id, '_pdf_file_id',  $_POST['_pdf_file_id']);

        if ( isset( $_POST['_pdf_fields_strigified']  ) ) 
            update_post_meta( $post_id, '_pdf_fields_strigified',  $_POST['_pdf_fields_strigified']);

        if ( isset( $_POST['_mapped_pdf_fields']  ) ) 
            update_post_meta( $post_id, '_mapped_pdf_fields',  json_encode($_POST['_mapped_pdf_fields']));
        else
            update_post_meta( $post_id, '_mapped_pdf_fields',  json_encode([]));

        if ( isset( $_POST['_form_id']  ) ) 
            update_post_meta( $post_id, '_form_id', $_POST['_form_id']);

        if ( isset( $_POST['_mapped_form_fields']  ) ) 
            update_post_meta( $post_id, '_mapped_form_fields',  json_encode($_POST['_mapped_form_fields']));
        else
            update_post_meta( $post_id, '_mapped_form_fields',  json_encode([]));

        if ( isset( $_POST['_form_fields_strigified']  ) ) 
            update_post_meta( $post_id, '_form_fields_strigified',  $_POST['_form_fields_strigified']);

        if ( isset( $_POST['_pdf_options']  ) ) 
            update_post_meta( $post_id, '_pdf_options',  $_POST['_pdf_options']);

        if ( isset( $_POST['_integrations']  ) ) 
            update_post_meta( $post_id, '_integrations',  $_POST['_integrations']);
        else
            update_post_meta( $post_id, '_integrations',  []);
        
    }

}

new Ninja2PDF_Post_Type;