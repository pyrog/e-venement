<?php
class liOnlinePaymentPaylinePluginConfiguration extends sfPluginConfiguration
{
  const paylineVersion = 4;
  public function setup()
  {
    require_once __DIR__.'/../lib/PaylinePayment.class.php';
    $this->dispatcher->connect('pub.cart.done', array($this, 'cartDone'));
  }
  
  public function cartDone(sfEvent $event)
  {
    $event['action']->executeResponse($event['request']);
  }
}
