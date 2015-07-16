<?php

/**
 * MetaEventTranslation form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MetaEventTranslationForm extends BaseMetaEventTranslationForm
{
  public function configure()
  {
    $tinymce = array(
      'width'   => 600,
      'height'  => 150,
      'config'  => array(
        'extended_valid_elements' => 'html,head,body,hr[class|width|size|noshade],iframe[src|width|height|name|align],style',
        'convert_urls' => false,
        'urlconvertor_callback' => 'email_urlconvertor',
        'paste_as_text' => false,
        'plugins' => 'textcolor link image',
        'toolbar1' => 'formatselect fontselect fontsizeselect | link image | forecolor backcolor',
        'toolbar2' => 'undo redo | bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote',
        'force_br_newlines' => false,
        'force_p_newlines'  => false,
        'forced_root_block' => '',
      ),
    );
    $this->widgetSchema['description'] = new liWidgetFormTextareaTinyMCE($tinymce);
  }
}
