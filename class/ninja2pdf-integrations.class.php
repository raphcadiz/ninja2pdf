<?php if ( ! defined( 'ABSPATH' ) ) exit;

class Ninja2PDF_Integration {
    private $current_key_integration = '';

    public function __construct() {
        add_action('wp_ajax_getIntegrationTemplate', array($this, 'ajax_generate_template'));
    }

    public function ajax_generate_template() {
        $ninja_integrations = apply_filters('ninja2pdf_integrations', array());
        $index = $_POST['data']['key'];
        $option = $ninja_integrations[$_POST['data']['integration']];
 
        ob_start();
        ?> 
            <tr class="form-field <?= $option['key'] ?>"> 
                <th scope="row">
                    <button class="button buttom-secondary remove-integration" data-el="<?= $option['key'] ?>">&#10008;</button>
                    <label><?= $option['label'] ?></label>
                </th>
                <td>
                    <?php
                    foreach ($option['fields'] as $key => $field):
                        if ($key > 0)
                            echo '<br />';

                            echo $this->generate_input_types($index, $option, $field);
                    endforeach;
                    ?>
                </td>
            </tr> 
        <?php

        $output = ob_get_contents();
        ob_end_clean();

        echo $output;
        die();
    }

    public function generate_template($index, $option = array(), $value = array()) {
        ob_start();
        ?> 
            <tr class="form-field <?= $option['key'] ?>"> 
                <th scope="row">
                    <button class="button buttom-secondary remove-integration" data-el="<?= $option['key'] ?>">&#10008;</button>
                    <label><?= $option['label'] ?></label>
                </th>
                <td>
                    <?php
                    foreach ($option['fields'] as $key => $field):
                        if ($key > 0)
                            echo '<br />';

                            echo $this->generate_input_types($index, $option, $field, $value);
                    endforeach;
                    ?>
                </td>
            </tr> 
        <?php

        $output = ob_get_contents();
        ob_end_clean();

        echo $output;

    }

    public function generate_input_types($index = 0, $option = array(), $field = array(), $value = array()) {
        ob_start();

        if ($field['type'] == 'text') {
            ?>
            <input type="text" name="_integrations[<?= $option['key'] ?>][<?= $index ?>][<?= $field['name'] ?>]" value="<?= $value[$field['name']] ?>" />
            <p class="description"><?= $field['description'] ?></p>
            <?php
        }
        elseif ($field['type'] == 'hidden') {
            ?>
            <input type="hidden" name="_integrations[<?= $option['key'] ?>][<?= $index ?>][<?= $field['name'] ?>]" value="<?= $value[$field['name']] ?>" />
            <p class="description"><?= $field['description'] ?></p>
            <?php
        }
        elseif ($field['type'] == 'textarea') {
            ?>
            <textarea rows="4" name="_integrations[<?= $option['key'] ?>][<?= $index ?>][<?= $field['name'] ?>]"><?= $value[$field['name']] ?></textarea>
            <p class="description"><?= $field['description'] ?></p>
            <?php
        }

        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

}

new Ninja2PDF_Integration;