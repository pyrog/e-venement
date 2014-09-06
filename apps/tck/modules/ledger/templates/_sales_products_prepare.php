<?php
/**********************************************************************************
*
*	    This file is part of e-venement.
*
*    e-venement is free software; you can redistribute it and/or modify
*    it under the terms of the GNU General Public License as published by
*    the Free Software Foundation; either version 2 of the License.
*
*    e-venement is distributed in the hope that it will be useful,
*    but WITHOUT ANY WARRANTY; without even the implied warranty of
*    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*    GNU General Public License for more details.
*
*    You should have received a copy of the GNU General Public License
*    along with e-venement; if not, write to the Free Software
*    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*
*    Copyright (c) 2006-2014 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2014 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
// total qty
foreach ( $products as $product )
{
  // initialization
  if ( !isset($pdts[$product->name]) )
    $pdts[$product->name] = array('id' => false, 'value' => 0, 'qty' => 0, 'declinations' => array());
  if ( !isset($pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]) )
    $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id] = array('id' => false, 'name' => '', 'value' => 0, 'qty' => 0, 'prices' => array());
  if ( !isset($pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['prices'][$product->sf_guard_user_id.'|-|'.$product->price_name]) )
    $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['prices'][$product->sf_guard_user_id.'|-|'.$product->price_name] = array('value' => 0, 'qty' => 0, 'user' => '', 'name' => '');
  
  // feeding
  if ( $product->product_declination_id )
  {
    $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['id'] = $product->product_declination_id;
    if ( $product->Declination->product_id )
      $pdts[$product->name]['id'] = $product->Declination->product_id;
  }
  else
  {
    if ( !isset($pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['id']) )
      $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['id'] = '__'.$product->id.'__';
    if ( !isset($pdts[$product->name]['id']) )
      $pdts[$product->name]['id'] = '__'.$product->id.'__';
  }
  $pdts[$product->name]['qty']++;
  $pdts[$product->name]['value'] += $product->value;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['name'] = $product->declination;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['qty']++;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['value'] += $product->value;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['prices'][$product->sf_guard_user_id.'|-|'.$product->price_name]['name'] = $product->price_name;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['prices'][$product->sf_guard_user_id.'|-|'.$product->price_name]['user'] = (string)$product->User;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['prices'][$product->sf_guard_user_id.'|-|'.$product->price_name]['qty']++;
  $pdts[$product->name]['declinations'][$product->code.' '.$product->product_declination_id]['prices'][$product->sf_guard_user_id.'|-|'.$product->price_name]['value'] += $product->value;
  
  // taxes
  foreach ( $total['vat'] as $key => $value )
  {
    // initialization
    if ( !isset($vat[$key]) )
     $vat[$key] = array('__total__' => 0);
    if ( !isset($vat[$key][$product->name]) )
      $vat[$key][$product->name] = array('__total__' => 0);
    if ( !isset($vat[$key][$product->name][$product->code.' '.$product->product_declination_id]) )
      $vat[$key][$product->name][$product->code.' '.$product->product_declination_id] = array('__total__' => 0);
    if ( !isset($vat[$key][$product->name][$product->code.' '.$product->product_declination_id][$product->sf_guard_user_id.'|-|'.$product->price_name]) )
      $vat[$key][$product->name][$product->code.' '.$product->product_declination_id][$product->sf_guard_user_id.'|-|'.$product->price_name] = 0;
    
    // feeding
    $vat[$key][$product->name][$product->code.' '.$product->product_declination_id][$product->sf_guard_user_id.'|-|'.$product->price_name] += $product->vat != $key ? 0 : $product->value - $product->value/(1+$product->vat);
    $vat[$key][$product->name][$product->code.' '.$product->product_declination_id][$product->sf_guard_user_id.'|-|'.$product->price_name] += $product->vat != $key ? 0 : $product->value - $product->value/(1+$product->vat);
    $vat[$key][$product->name][$product->code.' '.$product->product_declination_id]['__total__'] += $product->vat != $key ? 0 : $product->value - $product->value/(1+$product->vat);
    $vat[$key][$product->name]['__total__'] += $product->vat != $key ? 0 : $product->value - $product->value/(1+$product->vat);
    $vat[$key]['__total__'] += $product->vat != $key ? 0 : $product->value - $product->value/(1+$product->vat);
  }
  
  // total
  $total['qty']++;
  $total['value'] += $product->value;
  $total['vat'][$product->vat] += $product->value - $product->value/(1+$product->vat);
}
