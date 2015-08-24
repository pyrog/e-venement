<?php

/**
 * OptionLabels form.
 *
 * @package    e-venement
 * @subpackage form
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class OptionMCForm extends OptionForm
{
  /**
   * @see OptionForm
   */
  public function configure()
  {
    $this->db_type = 'mc_alerts';
    $this->model = 'Option';
    
    sfContext::getInstance()->getConfiguration()->loadHelpers('I18N');
    parent::configure();
    
    self::enableCSRFProtection();
    
    foreach ( array('type','name','value','sf_guard_user_id','created_at','updated_at',) as $id )
    {
      unset($this->widgetSchema   [$id]);
      unset($this->validatorSchema[$id]);
    }
    
    $this->widgets = array(
      '' => array(
        'enabled'        => array('label' => 'Activate alerts', 'type' => 'boolean', 'default' => '', 'helper' => NULL),
        'delay_before'   => array('label' => 'Alert before expiration', 'type' => 'integer', 'helper' => __('Days'), 'default' => '7'),
        'delay_after'    => array('label' => 'Alert after expiration', 'type' => 'integer', 'helper' => __('Days'), 'default' => '1'),
        'email_from'     => array('label' => 'Email sender', 'type' => 'email', 'helper' => NULL, 'default' => ''),
        'email_subject'  => array('label' => 'Subject:', 'type' => 'string', 'helper' => NULL, 'default' => 'Member card expiration'),
        'email_content'  => array('label' => 'Content:', 'type' => 'string', 'helper' => __('##EXPIRATION## is replaced by the expiration date'), 'default' => 'Your member card will expire or has expired on ##EXPIRATION##'),
      ),
    );

    $tinymce = array(
      'config'  => array(
        'extended_valid_elements' => 'html,head,body,hr[class|width|size|noshade],iframe[src|width|height|name|align],style',
        'convert_urls' => false,
        'urlconvertor_callback' => 'email_urlconvertor',
        'paste_as_text' => false,
        'plugins' => 'textcolor link image',
        'toolbar1' => 'formatselect fontselect fontsizeselect | link image | forecolor backcolor | undo redo',
        'toolbar2' => 'bold underline italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent blockquote',
        'force_br_newlines' => false,
        'force_p_newlines'  => false,
        'forced_root_block' => '',
      ),
    );
    $this->widgetSchema['email_content'] = new liWidgetFormTextareaTinyMCE($tinymce);
    $this->widgetSchema['enabled'] = new sfWidgetFormChoice(array(
      'expanded' => true,
      'choices' => array('' => 'no', '1' => 'yes'),
    ));
    
    foreach ( $this->widgets as $fieldset )
    foreach ( $fieldset as $name => $value )
    {
      $validator_class = 'sfValidator'.ucfirst($value['type']);
      
      if ( isset($this->widgetSchema[$name]) )
      {
        $this->widgetSchema[$name]
          ->setOption('label', $value['label'])
          ->setDefault($value['default']);
      }
      else
      $this->widgetSchema[$name]    = new sfWidgetFormInputText(array(
          'label'         => $value['label'],
          'default'       => $value['default'],
        ),
        array(
          'title'         => __('default:').' '.$value['default'].' '.$value['helper'],
      ));
      $this->validatorSchema[$name] = new $validator_class(array(
        'required' => false,
      ));
    }
  }
  
  public static function getDBOptions()
  {
    $r = array();
    
    $r['delay_before'] = 7;
    $r['delay_after'] = 1;
    $r['enabled'] = '';
    $r['email_from'] = '';
    $r['email_subject'] = 'Member card expiration';
    $r['email_content'] = 'Your member card will expire or has expired on the ##EXPIRATION##';
    
    foreach ( self::buildOptionsQuery()->fetchArray() as $opt )
      $r[$opt['name']] = $opt['value'];
    return $r;
  }
  
  protected static function buildOptionsQuery()
  {
    return $q = Doctrine::getTable('Option')->createQuery('o')
      ->andWhere('o.type = ?','mc_alerts');
  }
}
