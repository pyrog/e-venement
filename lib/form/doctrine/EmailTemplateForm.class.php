<?php

/**
 * EmailTemplate form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class EmailTemplateForm extends BaseEmailTemplateForm
{
  public function configure()
  {
    $this->widgetSchema['content'] = new liWidgetFormTextareaTinyMCE(array(
      'width'   => 650,
      'height'  => 420,
      'config'  => array(
        'extended_valid_elements' => 'html,head,body,hr[class|width|size|noshade],iframe[src|width|height|name|align],style',
        'convert_urls' => false,
        'urlconvertor_callback' => 'email_urlconvertor',
        'paste_as_text' => false,
        'plugins' => 'textcolor link image code',
        'toolbar1' => 'formatselect fontselect fontsizeselect | link image | forecolor backcolor | undo redo',
        'toolbar2' => 'bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote',
        'force_br_newlines' => false,
        'force_p_newlines'  => false,
        'forced_root_block' => '',
        'setup' => "__function(ed){
          ed.on('LoadContent', function(e) {
            if ( $($.parseHTML($('[name=\"email[content]\"]').val())).find('body').length > 0 )
            $('#email_content_ifr').contents().find('html').html($('#email_content').val()).find('body')
              .addClass('mce-content-body').prop('id','tinymce').prop('contenteditable','true')
              .load(function(){ window.parent.tinymce.get('email_content').fire('load'); });
          });
        }",
      ),
    ));
  }
}
