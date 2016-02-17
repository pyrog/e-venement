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
*    Copyright (c) 2015-2016 Marcos BEZERRA DE MENEZES <marcos.bezerra AT libre-informatique.fr>
*    Copyright (c) 2006-2016 Libre Informatique [http://www.libre-informatique.fr/]
*
***********************************************************************************/

/**
 * @param: uri            The uri to call
 * @param: directExecute  Can be undefined or a function(details) to execute as soon as the object is created
 */
function Connector(uri, directExecute) {
        // debug function
        this.console = function(msg){
          if ( window.location.hash == '#debug' )
            console.error(msg);
        }

        this.wsuri = uri;
        this.abSession = null;
        this.directExecute = directExecute;
        // the WAMP connection to the Router
        this.connection = new autobahn.Connection({
            url: this.wsuri,
            realm: "realm1"
        });

        this.connection.connector = this;

        // fired when connection is established and session attached
        this.connection.onopen = function(session, details){
            console.log('connector', this.connector);
          this.connector.console('Connector connected');
          this.connector.abSession = session;
          if ( typeof(this.connector.directExecute) == 'function' )
            this.connector.directExecute(details);
        };

        // start the session
        this.connection.open();

        // fired when connection was lost (or could not be established)
        this.connection.onclose = function (reason, details) {
          this.connector.console("Connection lost: " + reason);
        };

        // informs that 'this' is created
        this.console('Connector created with URI '+this.wsuri);
        
        /**
         * @function org.e-venement.listDevices
         * @param parameters: {
         *   type: usb,
         *   onSuccess: function(res),
         *   onError: function(res)
         * }
         *
         **/
        this.listDevices = function(parameters)
        {
          if ( parameters.type != 'usb' )
          {
            console.log('You must look for USB devices');
            return;
          }

          this.abSession.call('org.e-venement.listDevices', ['usb']).then(parameters.onSuccess, parameters.onError);
        };

        /**
         * @function org.e-venement.isDeviceAvailable
         * @param device: {
         *   type: usb|tcpip,
         *   params: { ... },
         *   onAvailable: function(res),
         *   onUnavailable: function(res)
         * }
         *
         **/
        this.isDeviceAvailable = function(parameters) {
          var device = {
            type: parameters.type,
            params: parameters.params
          };

          this.abSession.call('org.e-venement.isDeviceAvailable', [device]).then(parameters.onAvailable, parameters.onUnavailable);
        };

        /**
         * @function org.e-venement.areDevicesAvailable
         * @param query: {
         *     type: usb|tcpip,
         *     params: [{ ... }(, ...)],
         *     onComplete: function(parameters),
         *   }
         *
         **/
        this.areDevicesAvailable = function(query) {
          var targets = {
            type: query.type,
            params: query.params
          };
          
          this.abSession.call('org.e-venement.areDevicesAvailable', [targets]).then(query.onComplete);
        };

        /**
         * @function org.e-venement.sendData
         * @param device: {
         *     type: usb|tcpip,
         *     params: { ... },
         *   }
         * @param data Any data base64 encoded
         * @param onComplete callback
         *
         **/
        this.sendData = function(device, data, onComplete) {
          this.abSession.call('org.e-venement.sendData', [device, data]).then(onComplete);
        };
};

