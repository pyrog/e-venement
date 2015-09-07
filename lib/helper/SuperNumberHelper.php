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
*    Copyright (c) 2006-2015 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2015 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
function format_normal_currency($amount, $currency = null, $culture = null, $allow_trailing_zeros = false)
{
  if (null === $amount)
  {
    return null;
  }

  $numberFormat = new sfNumberFormat(_current_language($culture));

  $r = $numberFormat->format($amount, 'c', $currency);
  if ( $allow_trailing_zeros )
    return $r;
  return preg_replace('/(\d)[\.,]00\xc2\xa0/', '$1 ', $r);
}
