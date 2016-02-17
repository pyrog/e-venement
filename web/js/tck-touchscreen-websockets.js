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
*    Copyright (c) 2006-2016 Baptiste SIMON <baptiste.simon AT e-glop.net>
*    Copyright (c) 2006-2016 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/
$(document).ready(function(){
  // *T* here we are after the page is loaded
  
  var connector = new Connector('wss://cube.office.libre-informatique.fr:8164/ws', function(){
  //var connector = new Connector('wss://localhost:8164/ws', function(){
    // *T* here we are after the websocket first connection is established
    
    connector.console('Scanning devices (direct call) ...');
    connector.console(LI.usb.printers);
    var devices = [];
    $.each(LI.usb.printers, function(type, devs){
      $.each(devs, function(i, ids) { devices.push(ids); });
    });
    connector.areDevicesAvailable({ type: 'usb', params: devices, onComplete: function(data){
      // *T* here we are when the list of USB devices is received
      
      if (!( data.params && data.params.length > 0 ))
      {
        connector.console('No '+data.type+' device found within your search.');
        return;
      }
      var myDevice = data.params.shift();
      
      $('#li_transaction_museum .print, #li_transaction_manifestations .print')
        .each(function(){
          $(this).prop('action', $(this).prop('action')+'?direct='+JSON.stringify(myDevice))
            .prop('title', $('#li_transaction_field_close .print .direct-printing-info').text());
        })
        .attr('onsubmit', null)
        .submit(function(){
          // *T* here we are when the print form is submitted
          connector.console('Submitting the form...');
          if ( !LI.printTickets(this,false) )
            return false;
          
          $.ajax({
            method: 'get',
            url: $(this).prop('action'),
            success: function(data){
              // *T* here we are when we have got the base64 data representing tickets ready to be printed
              if ( !data )
              {
                connector.console('Empty data, nothing to send');
                return;
              }
              
              // sends data to the printer through the connector 
              connector.console('Sending data...');
              connector.sendData({ type: 'usb', params: myDevice }, data, function(){
                connector.console('Data sent!');
              });
            }
          });
          
          return false;
        });
    }});
  });
});
