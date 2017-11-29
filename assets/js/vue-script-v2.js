/*
 * Vuejs scripts added here
 * to handle data reactivity
 * for the ninja2pdf merges
 **/
let $ = jQuery;

let meta_data = ninjamerge.meta
let store = {
  data: {
    merge_file_name         : meta_data._merge_file_name,
    file_timestamps         : meta_data._file_timestamps,
    pdf_file_id             : meta_data._pdf_file_id,
    pdf_filename            : ninjamerge.filename,
    mapped_pdf_fields       : (meta_data.hasOwnProperty('_mapped_pdf_fields')) ? JSON.parse(meta_data._mapped_pdf_fields) : [],
    pdf_fields_strigified   : meta_data._pdf_fields_strigified,
    pdf_fields              : (meta_data.hasOwnProperty('_pdf_fields_strigified') && meta_data._pdf_fields_strigified != '') ? JSON.parse(meta_data._pdf_fields_strigified) : [],
    form_id                 : (meta_data.hasOwnProperty('_form_id')) ? meta_data._form_id[0] : 0,
    form_fields_strigified  : '',
    mapped_form_fields      : (meta_data.hasOwnProperty('_mapped_form_fields') && meta_data._mapped_form_fields != '') ? JSON.parse(meta_data._mapped_form_fields): [],
    form_fields             : [],
  }
}

Vue.component('ninja2pdf-mapping', {
  data () {
    return {
      pdf_selected: '',
      form_selected: ''
    }
  },
  props: {
    pdfFields: {
      type: Array
    },
    formFields: {
      type: Array
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

// general info vue handle
new Vue({
  el: '#ninja2pdf_general_info',

  data() {
    return {
      no_fields_found: false,
      is_fetching_fields: false,
      form_fields_strigified: [],
      is_fetching_form_fields: false,
      available_variables: '',
      shared: store
    }
  },

  created() {
    if (this.shared.data.form_id) {
      this.formChange();
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
      if (attachment.id == store.data.pdf_file_id) {
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
          store.data.pdf_fields = final_data;

          if (Object.keys(store.data.pdf_fields).length) {
            store.data.pdf_file_id = attachment.id
            store.data.pdf_fields = final_data;
            store.data.pdf_fields_strigified = JSON.stringify(final_data);
            self.no_fields_found = false
            store.data.pdf_filename = attachment.filename
            store.data.mapped_pdf_fields = {}
          } else {
            self.no_fields_found = true
            store.data.mapped_pdf_fields = {}
            store.data.pdf_file_id = 0
          }
          
          
          self.is_fetching_fields = false
        }).fail(function() {
          self.is_fetching_fields = false
      });
    },
    formChange () {
      let self = this
      let id = this.shared.data.form_id

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
          store.data.form_fields = JSON.parse(result)
          store.data.form_fields_strigified = result

          self.is_fetching_form_fields = false

          let fields = JSON.parse(result);
          let temp = '';
          for (var key in fields) {
            temp = temp + ', ' + '{' + fields[key].id + '_' + fields[key].label.replace(/\s+/g, '-').toLowerCase() + '} '
          }
          self.available_variables = temp.substring(1);
        }).fail(function() {
          self.is_fetching_form_fields = false
      });
    }
  }
})

// mapping vue handle
new Vue({
  el: '#ninja2pdf_mapping',

  data() {
    return {
      shared: store,
      mappings: [],
      mapping_count: 0
    }
  },

  created() {
    for (var key in this.shared.data.mapped_pdf_fields) {
      let mapping = {
        'mapped_pdf': this.shared.data.mapped_pdf_fields[key],
        'mapped_form': this.shared.data.mapped_form_fields[key]
      }
      this.mappings.push(mapping)
    }
  },

  methods: {
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
    }
  }
})

jQuery(function(e){
  
  $('#add-integration').on('click', function(e){
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
  })

  $('.remove-integration').live('click', function(e){
    self = e.currentTarget;
    $(self).parent().parent().remove();
    key = $(self).attr('data-el');

    $('#integration-table tr.' + key).each(function(index) {
      $(this).find(':input').each(function(x) {
        name = $(this).attr('name');
        name = name.replace(/[^0-9]/g,''+index);

        $(this).addClass(''+index);
      })
      // console.log(index);
    })
  })

});