if ( LI == undefined )
    var LI = {};

LI.submitAllSurveyForms = function() {
  console.log('LI.submitAllSurveyForms');
  var $forms = $('section.srv-direct-survey-container form');
  $forms.data('submitted', false);
  LI.submitNextSurveyForm();
};

LI.submitNextSurveyForm = function() {
  console.log('LI.submitNextForm');
  var $forms = $('section.srv-direct-survey-container form');
  var nb_done = 0;
  $forms.each(function(){
    if ( !$(this).data('submitted') ) {
      $(this).submit();
      nb_done = 1;
      return false;
    }
  });
  if ( nb_done === 0 )
    $('#transition .close').click();
};

$(document).ready(function(){

  $('section.srv-direct-survey-container form').submit(function(e){
    var $form = $(this);
    // ajax post of the form
    $.ajax({
      type: "POST",
      url: $(this).attr('action'),
      data: $(this).serialize(),
      success: function(data)
      {
        $form.data('submitted', true);

        data = $.parseHTML(data);
        if ( $(data).find('.error_list').length > 0 ) // some errors
        {
          var msg = [];
          $(data).find('.error_list li').each(function(){
            msg.push($(this).closest('tr').find('th').text()+' '+$(this).text());
          });
          LI.alert(msg.join('<br/>'));
        }
        else // everything's ok
        {
          LI.submitNextSurveyForm();
        }
      }
    });
    // avoid to execute the actual submit of the form.
    e.preventDefault();
    return false;
  });

  $('#submit-all-forms').click(LI.submitAllSurveyForms);
});