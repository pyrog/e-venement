<?php include_partial(
  'form_links_field',
  array(
    'name'  => 'contiguous',
    'label' => __('Contiguous?'),
    'helper'=> __('If checked, A3 is between A2 & A4. If not, A3 is between A1 & A5...'),
    'type'  => 'checkbox',
    'value' => 'yes',
    'size'  => false,
    'attributes' => array('checked' => 'checked'),
  )
) ?>
<?php include_partial(
  'form_links_field',
  array(
    'name'  => 'format',
    'label' => __('Format'),
    'value' => "%row%%num%",
    'size'  => '20',
    'with_submit' => true,
    'submit_label' => __('Build'),
  )
) ?>
