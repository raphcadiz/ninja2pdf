<div v-if="shared.data.pdf_fields.length < 1 || shared.data.form_fields.length < 1">
    <div class="select-warning">
        Please select PDF File and Ninja form to start mapping.
    </div>
</div>
<div v-else>
    <div id="mapping-labels">
        <div class="pdf-fields-label">
            <label>PDF Fields</label>
        </div>
        <div class="form-fields-label">
            <label style="margin-left: -29px;">Form Fields</label>
        </div>
    </div>
    <div v-for="(mapping, index) in mappings">
        <ninja2pdf-mapping :pdf-fields="shared.data.pdf_fields" :pdf-field-value="mapping.mapped_pdf" :form-fields="shared.data.form_fields"  :form-field-value="mapping.mapped_form"></ninja2pdf-mapping>
        <button class="button button-secondary mapping-remove" @click.prevent="removeMapping($event, index)">
            <span class="dashicons dashicons-minus"></span>
        </button>
    </div>
    <div v-for="(mapping, index) in mapping_count">
        <ninja2pdf-mapping :pdf-fields="shared.data.pdf_fields" :form-fields="shared.data.form_fields"></ninja2pdf-mapping>
    </div>
    <button class="button button-secondary" @click.prevent="addMapping(shared.data.pdf_fields[0], shared.data.form_fields[0])">Add Mapping</button>
</div>