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

class liValidatorDoctrineUniqueCaseInsensitive extends liValidatorDoctrineUnique
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * model:              The model class (required)
   *  * column:             The unique column name in Doctrine field name format (required)
   *                        If the uniquess is for several columns, you can pass an array of field names
   *  * original_object:    The original checked object (to know whether or not we need to exclude it
   *                        from our UNIQUE search) (required)
   *  * primary_key:        The primary key column name in Doctrine field name format (optional, will be introspected if not provided)
   *                        You can also pass an array if the table has several primary keys
   *  * connection:         The Doctrine connection to use (null by default)
   *  * throw_global_error: Whether to throw a global error (false by default) or an error tied to the first field related to the column option array
   *
   * @see sfValidatorBase
   */
  public function __construct($options = array(), $messages = array())
  {
    $options['comparator'] = 'ILIKE';
    parent::__construct($options, $messages);
  }
}
