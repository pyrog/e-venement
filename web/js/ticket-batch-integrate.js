$(document).ready(function(){
  $('#batch-integrate .sf_admin_form_field_filetype input').change(function(){
    switch ( $(this).val() ) {
    case 'fb':
      $('#batch-integrate .sf_admin_form_field_translation_prices input[type=text] + input[type=text]')
        .prop('disabled',false)
        .fadeIn();
      break;
    default:
      $('#batch-integrate .sf_admin_form_field_translation_prices input[type=text] + input[type=text]')
        .prop('disabled',true)
        .fadeOut();
    }
    $('#batch-integrate .sample > [class]:not(.'+$(this).val()+')').hide();
    $('#batch-integrate .sample > [class].'+$(this).val()).show();
  });
  
  $('.sf_admin_form_field_gauges_list input[type=radio]:first').click();
  $('#batch-integrate .sf_admin_form_field_filetype input:checked').change();
});
