<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Processing{
    
    public function __construct(){
        add_action('ninja_forms_after_submission', array($this, 'process_merge'));
    }

    public function process_merge($form_data) {
        $merges = $this->get_merges_by_form_id($form_data['form_id']);

        $api = new Ninja2PDF_Api;
        foreach ($merges as $key => $merge) {
            $mapped_pdf_fields = json_decode(get_post_meta($merge->ID, '_mapped_pdf_fields', true));
            $mapped_form_fields = json_decode(get_post_meta($merge->ID, '_mapped_form_fields', true));

            $mapping = array();
            foreach ($mapped_form_fields as $key => $mapped_form_field) {
                $temp_array = array();

                if (sizeof(explode('-', $mapped_form_field)) > 1)
                    $mapped_form_field = explode('-', $mapped_form_field)[0];

                if ($form_data['fields'][$mapped_form_field]['type'] == 'listmultiselect') {
                    $position = array_search(explode('-', $mapped_form_field)[1], $form_data['fields'][$mapped_form_field]['value']);
                    if (sizeof(explode('||', $mapped_pdf_fields[$key])) > 1) {
                        $exploded_val = explode('||', $mapped_pdf_fields[$key]);
                        $temp_array[$exploded_val[0]] = 'Off';
                    } else {
                        $temp_array[$mapped_pdf_fields[$key]] = $form_data['fields'][$mapped_form_field]['value'][$position];
                    }
                }
                else {
                    if (sizeof(explode('||', $mapped_pdf_fields[$key])) > 1) {
                        $exploded_val = explode('||', $mapped_pdf_fields[$key]);
                        if (!empty($form_data['fields'][$mapped_form_field]['value'])) {
                            $temp_array[$exploded_val[0]] = $exploded_val[1];
                        } else {
                            $temp_array[$exploded_val[0]] = 'Off';
                        }
                    } else {
                        $temp_array[$mapped_pdf_fields[$key]] = $form_data['fields'][$mapped_form_field]['value'];
                    }
                }

                $mapping = array_merge($mapping, $temp_array);
            }

            $pdf_id = get_post_meta($merge->ID, '_pdf_file_id', true);
            $pdf_file = get_attached_file($pdf_id);
            $file_name = get_post_meta($merge->ID, '_merge_file_name', true);
            $pdf_options = get_post_meta($merge->ID, '_pdf_options', true);
            $integrations = get_post_meta($merge->ID, '_integrations');

            if (get_post_meta($merge->ID, '_file_timestamps', true))
                $file_name = $file_name.'-'.time();

            $ninja2pdf_settings = get_option('ninja2pdf_settings');
            $license = array(
                'name'  => 'Ninja Forms to PDF',
                'license'   => $ninja2pdf_settings['ninja2pdf_ninja_forms_to_pdf_license_key'],
                'url'       => home_url()
            );

            $result = $api->fill_pdf($pdf_file, $file_name, $mapping, $pdf_options, $license);

            if (json_decode($result) == 'error') {
                error_log("Failed to generate PDF!", 0);
            } else {
                $final_file = $this->download_pdf($file_name, $result);
                do_action('ninja2pdf_after_merge', $final_file, $file_name, $integrations[0]);

                $ninja2pdf_completed_merges = get_option('ninja2pdf_completed_merges') ? get_option('ninja2pdf_completed_merges') : [];
                $completed_merge = array(
                    'user' => get_current_user_id(),
                    'time_submitted' => date("Y-m-d H:i:s"),
                    'file' => $final_file
                );
                array_push($ninja2pdf_completed_merges, json_encode($completed_merge));
                update_option('ninja2pdf_completed_merges', $ninja2pdf_completed_merges);
            }

        } //end foreach

        return;
    
    }

    public function download_pdf($filename, $result){
        $upload_dir = wp_upload_dir();
        $pdf_dir_path = $upload_dir['basedir'] . '/finished-pdf';
        $path = $pdf_dir_path . '/' . $filename . '.pdf';
        if (!file_exists($pdf_dir_path)){
            mkdir($pdf_dir_path, 0777, true);
            if (file_put_contents($path, $result) ) {
                return $path;
            }
        } else {
            if (file_put_contents($path, $result) ) {
                return $path;
            }
        }

        return false;
    }

    private function get_merges_by_form_id($form_id) {
        $args = array(
            'post_type' => 'ninja_merge',
            'posts_per_page' => 500,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_form_id',
                    'value' =>  $form_id,
                    'compare' => '==',
                ),
            ),
        );

        $merges = get_posts($args);

        return $merges;
    }

}

new Ninja2PDF_Processing;