<div class="form-grp">
    <label>File Name</label>
    <input type="text" name="_merge_file_name" class="form-ctrl" v-model="shared.data.merge_file_name">
    <p class="description">Available name variable: <span>{{available_variables}}</span></p>
</div>

<div class="form-grp">
    <label>
        <input type="checkbox" name="_file_timestamps" value="1" v-model="shared.data.file_timestamps" />
        Include Timestamp
    </label>
</div>


<div class="form-grp">
    <div class="half first">
        <label>PDF File</label> <br />
        <a href="#" @click="uploadPDF" class="button button-secondary">Browse Files</a>
        <input type="hidden" name="_pdf_file_id" v-model="shared.data.pdf_file_id" />
        <input type="hidden" name="_pdf_fields_strigified" v-model="shared.data.pdf_fields_strigified" />
        <span :class="['spinner', {'is-active': is_fetching_fields}]" style="float: none"></span>
        <div v-if="no_fields_found" class="pdf-file-feedback err">No Fields Found!</div>
        <div v-else-if="shared.data.pdf_filename" class="pdf-file-feedback">{{shared.data.pdf_filename}}</div>
    </div>
    <div class="half">
        <?php
        $forms = Ninja_Forms()->form()->get_forms();
        ?>
        <label>Ninja Form</label> <br />
        <select name="_form_id" class="form-control" v-model="shared.data.form_id" @change="formChange">
            <?php
            foreach ($forms as $key => $form) {
                ?>
                <option value="<?= $form->get_id() ?>"><?= $form->get_setting('title') ?></option>
                <?php
            }
            ?>
        </select>
        <input type="hidden" name="_form_fields_strigified" v-model="shared.data.form_fields_strigified" />
        <span :class="['spinner', {'is-active': is_fetching_form_fields}]" style="float: none"></span>
    </div>
</div>



