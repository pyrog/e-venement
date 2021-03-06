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
    use_helper('Date');
    $g = new liGraph;
    
    $bars = new liBarStack;
    $bars->set_alpha(0.8);
    
    $line = new liLine;
    
    $criterias = $sf_user->getAttribute('stats.criterias',array(),'admin_module');
    $line_values = $names = $max = array();
    foreach ( $dates as $date )
    {
      $names[] = new liXAxisLabel(isset($criterias['interval']) && intval($criterias['interval']) > 1
        ? format_date($date['date']).' -> '.format_date($date['end'])
        : format_date($date['date'])
      , '#000', 11, 45);
      
      $max[] = max(array(
        $date['passing'],
        $date['printed'] + $date['ordered'] + (sfConfig::get('project_tickets_count_demands',false) ? $date['asked'] : 0)
      ));
      
      $line_values[] = $date['passing'];
      
      $bars->append_stack(array(
        $date['printed'],
        $date['ordered'],
        sfConfig::get('project_tickets_count_demands',false) ? $date['asked'] : 0,
      ));
    }
    
    $bars->set_keys(array(
      new liBarStackKey('#DE0202', __('Printed'), 11),
      new liBarStackKey('#FF7800', __('Ordered'), 11),
      sfConfig::get('project_tickets_count_demands',false) ? new liBarStackKey('#00A0B0', __('Asked'), 11) : new liBarStackKey('', '', 11),
    ));
    $bars->set_tooltip("#x_label#: #val#\nTotal: #total#");
    
    $dot = new liDotSolid;
    $dot->size(3)->halo_size(1); //->colour('#f00000');
    $line->set_values($line_values);
    $line->set_width(2);
    $line->set_colour('#17b912');
    $line->set_default_dot_style($dot);
    
    $y = new liYAxis;
    $y->set_range(0, max($max), round(max($max)/100)*10);
    
    $x = new liXAxis;
    $x->set_labels_from_array($names);
    
    $g->add_element($bars);
    $g->add_element($line);
    $g->set_x_axis($x);
    $g->add_y_axis($y);
    
    $tt = new liTooltip;
    $tt->set_hover();
    $g->set_tooltip($tt);
    
    echo $g;
