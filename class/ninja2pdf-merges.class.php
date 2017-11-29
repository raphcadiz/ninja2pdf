<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Merges{
    
    public function __construct(){
        add_action( 'wp_ajax_get_pdf_fields' , array( $this , 'get_pdf_fields' ) );
        add_action( 'wp_ajax_get_form_fields' , array( $this , 'get_form_fields' ) );
    }

    public function get_pdf_fields() {
        if( isset( $_POST['data'] ) ):
            $attachment_id = $_POST['data']['attachment_id'];

            $api = new Ninja2PDF_Api;
            $result = $api->get_fields($attachment_id);

            $final = array();
            foreach (json_decode($result) as $key => $field) {
                if ($field->FieldType == 'Button') {
                    foreach ($field->FieldStateOption as $option) {
                        $final[] = array(
                            'FieldName' => $field->FieldName . '||' . $option
                        );
                    }
                } else {
                    $final[] = array(
                        'FieldName' => $field->FieldName
                    );
                }
            }

            echo json_encode($final);
            die();
        endif;
    }

    public function get_form_fields() {
        if( isset( $_POST['data'] ) ):
            $form_id = $_POST['data']['form_id'];

            $models = Ninja_Forms()->form($form_id)->get_fields();

            $fields = array();
            foreach ($models as $model) {
                if ($model->get_setting('type') == 'submit')
                    continue;

                if ($model->get_setting('type') == 'listmultiselect') {
                    foreach ($model->get_setting('options') as $key => $option) {
                        $temp_field = array(
                            'id'        => $model->get_id() . '-' . $option['value'],
                            'label'     => $model->get_setting('label') . ' - ' . $option['label'],
                            'type'      => $model->get_setting('type')
                        );
                        array_push($fields, $temp_field);
                    }
                }
                else {
                    $temp_field = array(
                        'id'        => $model->get_id(),
                        'label'     => $model->get_setting('label'),
                        'type'      => $model->get_setting('type')
                    );
                    array_push($fields, $temp_field);
                }
                
            }

            echo json_encode($fields);
            die();
        endif;
    }
}

new Ninja2PDF_Merges;