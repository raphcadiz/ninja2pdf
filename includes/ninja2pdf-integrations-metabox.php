<table class="form-table" id="integration-table">
    <tbody>
        <?php 
            if (!empty($merge_integrations)):
                foreach ($merge_integrations[0] as $key => $integration):
                    foreach ($integration as $intkey => $value):
                        $ninja2pdf_integration_instance->generate_template($intkey, $ninja_integrations[$key], $value);
                    endforeach;
                endforeach;
            endif
        ?>
    </tbody>
</table>
<br />
<hr />
<div>
    <select id="intengration-select">
        <?php foreach($ninja_integrations as $ninja_integration): ?>
            <option value="<?= $ninja_integration['key'] ?>" data-el="<?= $ninja_integration['key'] ?>"><?= ucwords(str_replace("_", " ", $ninja_integration['key'])); ?></option>
        <?php endforeach; ?>
    </select>
    <button class="button button-secondary" id="add-integration">Add</button>
</div>