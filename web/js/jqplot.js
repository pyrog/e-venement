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

if ( LI == undefined )
  var LI = {};
if ( LI.series == undefined )
  LI.series = {};
if ( LI.csvData == undefined )
  LI.csvData = {};

$(document).ready(function(){
  // record to CSV
  $('.jqplot .actions .record').click(function(){
    var data = LI.csvData[$(this).closest('.jqplot').find('[data-series-name]').attr('data-series-name')];
    var url = URL.createObjectURL(new Blob([data.join("\n")], { type: "text/csv" }));
    $(this).prop('download', LI.slugify(data[0][1]+' '+data[0][0])+'.csv')
      .prop('href', url);
  });
});
