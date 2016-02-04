<?php
  $contact = NULL;
  if ( $survey_answer->contact_id )
    $contact = $survey_answer->Contact;
  elseif ( $survey_answer->Group->contact_id )
    $contact = $survey_answer->Group->Contact;
?>
<?php if ( $contact ): ?>
<?php echo cross_app_link_to(!$survey_answer->contact_id && $survey_answer->Group->professional_id ? $survey_answer->Group->Professional : $contact,
  'rp',
  'contact/edit?id='.$contact->id
) ?>
<?php endif ?>
