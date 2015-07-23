/**********************************************************************************
*
*	    This file is part of e-venement and the jQuery middle click plugin
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
*    Copyright (c) 2015 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2015 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/

/*! jQuery middleclick plugin v1.0 | (c) 2015 Libre Informatique SCRL (c) 2015 Baptiste SIMON | GNU/GPL v2 or later */

jQuery.fn.extend({
  middleclick: function(data, fn){
    var elt;
    this.mousedown(function(e){
      if ( e.which == 2 )
        elt = jQuery(this);
    }).mouseleave(function(e){
      elt = undefined;
    }).mouseup(function(e){
      if (!( e.which == 2 && elt != undefined && jQuery(this).is(elt) ))
        return;
      jQuery(this).trigger('middleclick');
    });
    return arguments.length > 0  
      ? this.on('middleclick', null, data, fn) 
      : this.trigger('middleclick');
  }
});

