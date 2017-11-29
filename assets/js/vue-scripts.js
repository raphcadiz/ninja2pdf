/*
 * Vuejs scripts added here
 * to handle data reactivity
 * for the ninja2pdf merges
 **/
let $ = jQuery;

Vue.component('ninja2pdf-mapping', {
  data () {
    return {
      pdf_selected: '',
      form_selected: ''
    }
  },
  props: {
    pdfFields: {
      type: Object
    },
    formFields: {
      type: Object
    },
    pdfFieldValue: '',
    formFieldValue: ''
  },
  mounted () {
    this.pdf_selected = (this.pdfFieldValue) ? this.pdfFieldValue : this.pdfFields[0].FieldName
    this.form_selected = (this.formFieldValue) ? this.formFieldValue : this.formFields[0].id
  },
  template: `<div class="mapping-wrapper">
              <div class="pdf-field-select">
                <select class="form-control" name="_mapped_pdf_fields[]" v-model="pdf_selected">
                  <option v-for="(pdfField, index) in pdfFields" :value="pdfField.FieldName">{{pdfField.FieldName}}</option>
                </select>
              </div>
              <div class="form-field-select">
                <select class="form-control" name="_mapped_form_fields[]" v-model="form_selected">
                  <option v-for="(formField, index) in formFields" :value="formField.id">{{formField.label}}</option>
                </select>
              </div>
            </div>`
})

var app = new Vue({
  el: '#poststuff',
  data: {
    mapped_pdf_fields: {},
    merge_file_name: '',
    file_timestamps: '',
    pdf_file_id: '',
    pdf_fields: [],
    pdf_fields_strigified: '',
    is_fetching_fields: false,
    no_fields_found: false,
    pdf_filename: '',
    mapping_count: 0,
    mapped_form_fields: {},
    form_id: 0,
    form_fields: [],
    form_fields_strigified: '',
    is_fetching_form_fields: false,
    mappings: []
  },
  created () {
    let meta_data = ninjamerge.meta
    this.merge_file_name = meta_data._merge_file_name;
    this.file_timestamps = meta_data._file_timestamps;
    this.pdf_file_id = meta_data._pdf_file_id;
    this.pdf_filename = ninjamerge.filename
    this.mapped_pdf_fields = (meta_data.hasOwnProperty('_mapped_pdf_fields')) ? JSON.parse(meta_data._mapped_pdf_fields) : [];
    this.pdf_fields_strigified = meta_data._pdf_fields_strigified
    this.pdf_fields = (meta_data.hasOwnProperty('_pdf_fields_strigified') && meta_data._pdf_fields_strigified != '') ? JSON.parse(meta_data._pdf_fields_strigified) : [];
    this.form_id = (meta_data.hasOwnProperty('_form_id')) ? meta_data._form_id[0] : 0;
    
    // this.form_fields_strigified = meta_data._form_fields_strigified;
    // this.form_fields = (meta_data.hasOwnProperty('_form_fields_strigified') && meta_data._form_fields_strigified != '') ? JSON.parse(meta_data._form_fields_strigified) : [];
    /*
     * Trigger formChange() to have a
     * refreshed collection of form fields
     *
     **/
    this.formChange();

    this.mapped_form_fields = (meta_data.hasOwnProperty('_mapped_form_fields') && meta_data._mapped_form_fields != '') ? JSON.parse(meta_data._mapped_form_fields): [];


    for (var key in this.mapped_pdf_fields) {
      let mapping = {
        'mapped_pdf': this.mapped_pdf_fields[key],
        'mapped_form': this.mapped_form_fields[key]
      }
      this.mappings.push(mapping)
    }
  },
  methods: {
    uploadPDF () {
        let self = this
        var new_frame;

        // If the media new_frame already exists, reopen it.
        if ( new_frame ) {
          new_frame.open();
          return;
        }
        
        new_frame = wp.media({
          title: 'Select a PDF file',
          button: {
            text: 'Use this media'
          },
          library: { type : 'application/pdf'},
          multiple: false  // Set to true to allow multiple files to be selected
        });

        
        // When an image is selected in the media new_frame...
        new_frame.on( 'select', function() {
          // Get media attachment details from the new_frame state
          var attachment = new_frame.state().get('selection').first().toJSON();
          self.getFields(attachment);
        });

        // Finally, open the modal on click
        new_frame.open();
    },
    getFields (attachment) {
      let self = this

      // don't trigger change if same pdf is selected
      if (attachment.id == self.pdf_file_id) {
        return
      }
      
      self.is_fetching_fields = true
      $.post(
        ninjamerge.ajaxurl,
        { 
        data: { 
          'attachment_id' : attachment.id, 
        },
        action : 'get_pdf_fields'
        }, 
        function( result, textStatus, xhr ) {
          final_data = JSON.parse(result);
          self.pdf_fields = final_data;

          if (Object.keys(self.pdf_fields).length) {
            self.pdf_file_id = attachment.id
            self.pdf_fields = final_data;
            self.pdf_fields_strigified = JSON.stringify(final_data);
            self.no_fields_found = false
            self.pdf_filename = attachment.filename
            self.mapped_pdf_fields = {}
          } else {
            self.no_fields_found = true
            self.mapped_pdf_fields = {}
            self.pdf_file_id = 0
          }
          
          
          self.is_fetching_fields = false
        }).fail(function() {
          self.is_fetching_fields = false
      });
    },
    formChange () {
      let self = this
      let id = self.form_id

      self.is_fetching_form_fields = true;
      $.post(
        ninjamerge.ajaxurl,
        { 
        data: { 
          'form_id' : id, 
        },
        action : 'get_form_fields'
        }, 
        function( result, textStatus, xhr ) {
          self.form_fields = JSON.parse(result)
          self.form_fields_strigified = result

          self.is_fetching_form_fields = false
        }).fail(function() {
          self.is_fetching_form_fields = false
      });
    },
    addMapping (pdffield, formfield) {
      let mapping = {
        'mapped_pdf': pdffield.FieldName,
        'mapped_form': formfield.id
      }
      this.mappings.push(mapping)
    },
    removeMapping (event, index) {
      var self = event.currentTarget;
      $(self).parent().remove();
    },
    addIntegration (e) {
      self = e.currentTarget;
      $(self).attr('disabled', 'disabled');
      integration = $('#intengration-select').val();
      el = $('#intengration-select option:selected').attr('data-el');
      key = $('.' + el).length;

      $.post(
        ninjamerge.ajaxurl,
        { 
          data: {key: (key), integration:integration},
          action : 'getIntegrationTemplate'
        }, 
        function( result, textStatus, xhr ) {
          $('#integration-table tbody').append(result);
          $(self).removeProp('disabled');
        }).fail(function() {
          $(self).removeProp('disabled');
      });
    },
    removeIntegration (e) {
      self = e.currentTarget;
      $(self).parent().parent().remove();
      key = $(self).attr('data-el');

      $('#integration-table tr.' + key).each(function(index) {
        $(this).find(':input').each(function(x) {
          name = $(this).attr('name');
          name = name.replace(/[^0-9]/g,''+index);
          // alert(name);
          $(this).addClass(''+index);
        })
        console.log(index);
      })
    }
  }
});