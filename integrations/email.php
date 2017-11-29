<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Email {

    public function __construct() {
        add_filter('ninja2pdf_integrations', array( $this, 'register_integration' ), 1);
        add_action('ninja2pdf_after_merge', array( $this, 'process_integration' ), 10, 3);
    }

    public function register_integration($array) {
        $email = array(
            'email' => array(
                'key'       => 'email',
                'label'     => 'Email',
                'fields'    => array(
                    array (
                        'name'          => 'to',
                        'type'          => 'text',
                        'description'   => 'Add Email to send the file.'
                    ),
                    array (
                        'name'          => 'subject',
                        'type'          => 'text',
                        'description'   => 'Add Email subject.'
                    ),
                    array (
                        'name'          => 'body',
                        'type'          => 'textarea',
                        'description'   => 'Add the body of the email.'
                    ),
                )
            )
        );

        return array_merge( $array, $email );
    }

    public function process_integration($final_file, $file_name, $integration) {
        if (!array_key_exists('email', $integration))
            return;

        $deleveries = $integration['email'];
        $file = WP_CONTENT_DIR . "/uploads/finished-pdf/$file_name.pdf";

        foreach ($deleveries as $key => $delivery):
            $to = $delivery['to'];
            $subject = $delivery['subject'];
            $body = $delivery['body'];
            $headers = array('Content-Type: text/html; charset=UTF-8');
            $attachments = [$file];
             
            wp_mail( $to, $subject, $body, $headers, $attachments );
        endforeach;

    }

}

new Email;