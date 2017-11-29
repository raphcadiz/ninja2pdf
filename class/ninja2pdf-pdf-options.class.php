<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Pdf_Options {

    public function __construct() {
       add_filter('ninja2pdf_pdf_options', array( $this, 'pdf_default_options' ), 1);
    }

    public function generate_input($option = array()) {
        global $post;

        ob_start();
        ?> <tr class="form-field"> <?php
        if ($option['type'] == 'boolean') {
            ?>
            <th scope="row">
                <label><?= $option['parameters']['label'] ?></label>
            </th>
            <td>
                <input type="checkbox" name="_pdf_options[<?= $option['parameters']['name'] ?>]" value="1" <?= (get_post_meta($post->ID, '_pdf_options', true) && get_post_meta($post->ID, '_pdf_options', true)[$option['parameters']['name']]) ? 'checked' : '' ?>>
                <em><?= $option['parameters']['info'] ?></em>
            </td>
            <?php
        }
        if ($option['type'] == 'text') {
            ?>
            <th scope="row">
                <label><?= $option['parameters']['label'] ?></label>
            </th>
            <td>
                <input type="text" name="_pdf_options[<?= $option['parameters']['name'] ?>]" value="<?= (get_post_meta($post->ID, '_pdf_options', true)) ? get_post_meta($post->ID, '_pdf_options', true)[$option['parameters']['name']] : '' ?>" />
                <p class="description"><?= $option['parameters']['info'] ?></p>
            </td>
            <?php
        }
        ?> </tr> <?php

        $output = ob_get_contents();
        ob_end_clean();

        return $output;

    }

    public function pdf_default_options($array) {
        $options = array();

        $options['flatten'] = array(
            'type' => 'boolean',
            'parameters' => array(
                'label'         => 'Flatten PDF',
                'name'          => 'flatten',
                'class'         => '',
                'info'          => '',
                'is_required'   => 0
            )
        );

        $options['user_pw'] = array(
            'type' => 'text',
            'parameters' => array(
                'label'         => 'Set Password',
                'name'          => 'user_pw',
                'class'   => '',
                'info'          => 'Leave empty if you don\'t want to set password to the pdf.',
                'is_required'   => 0
            )
        );

        return array_merge( $array, $options );
    }

}

new Ninja2PDF_Pdf_Options;