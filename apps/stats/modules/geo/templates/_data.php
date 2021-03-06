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
*    Copyright (c) 2006-2013 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2013 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
?>
<?php
    use_helper('Number');
    $g = new liGraph;
    
    $pie = new liPie;
    //$pie->set_style('{font-size: 12px; color: #78B9EC;');
    
    $total = array_sum($sf_data->getRaw('geo'));
    $names = $data = array();
    foreach ( $geo as $name => $value )
      $data[] = new liPieValue($value, $type != 'postalcodes' ? __($name).' '.format_number($total != 0 ? round(($value*100/$total),2) : 0).'%' : __($name));
    $pie->set_values($data);
    
    //To display value as tool tip
    $pie->set_tooltip( __("#label#: ".($type != 'postalcodes' ? '#val#' : '#percent#')."\nTotal: #total#") );
    $g->add_element($pie);
    
    echo $g;
