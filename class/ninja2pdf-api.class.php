<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Api {

    private $api_url = 'http://merge.ninja2pdf.com/public/api';
    private $curl_result = [];


    public function get_fields($attachment_id) {
        $endpoint = '/get-fields';

        $fullsize_path = get_attached_file( $attachment_id );

        if (function_exists('curl_file_create')) {
            $file_data = curl_file_create($fullsize_path);
        } else { // 
            $file_data = '@' . realpath($fullsize_path);
        }
        $params = ['file' => $file_data];

        $result = $this->process_curl($endpoint, $params);

        return $result;
    }

    public function fill_pdf($pdf_file, $file_name, $mapping, $pdf_options, $license = array()) {
        $endpoint = '/fill-pdf';

        if (function_exists('curl_file_create')) {
            $file_data = curl_file_create($pdf_file);
        } else { // 
            $file_data = '@' . realpath($pdf_file);
        }

        $params = [
            'file_name'     => $file_name,
            'file'          => $file_data,
            'mapping'       => json_encode($mapping),
            'pdf_options'   => json_encode($pdf_options),
            'license'       => json_encode($license)
        ];

        $response = $this->process_curl($endpoint, $params);

        return $response;
    }

    public function process_curl($endpoint, $params) {
        $url = $this->api_url.$endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json, application/pdf'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        $result = curl_exec($ch);
        curl_close ($ch);

        return $result;
    }

}
