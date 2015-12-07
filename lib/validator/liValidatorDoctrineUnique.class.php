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

class liValidatorDoctrineUnique extends sfValidatorDoctrineUnique
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
   *  * comparator:         The comparator that needs to be used to retrieve the uniqueness of the values ('=' by default)
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('original_object');
    $this->addOption('comparator', '=');
    parent::configure($options, $messages);
  }

  protected function doClean($values)
  {
    $originalValues = $values;
    $table = Doctrine_Core::getTable($this->getOption('model'));
    if (!is_array($this->getOption('column')))
    {
      $this->setOption('column', array($this->getOption('column')));
    }

    //if $values isn't an array, make it one
    if (!is_array($values))
    {
      //use first column for key
      $columns = $this->getOption('column');
      $values = array($columns[0] => $values);
    }

    $q = Doctrine_Core::getTable($this->getOption('model'))->createQuery('a');
    foreach ($this->getOption('column') as $column)
    {
      $colName = $table->getColumnName($column);
      if (!array_key_exists($column, $values))
      {
        // one of the column has be removed from the form
        return $originalValues;
      }

      $q->addWhere('a.' . $colName . ' '.$this->getOption('comparator').' ?', $values[$column]);
    }
    
    // do not consider the current object itself
    foreach ( $this->getPrimaryKeys() as $key )
    {
      $q->andWhere('a.'.$key.' != ?', $this->getOption('original_object')->$key);
    }

    $object = $q->fetchOne();

    // if no object or if we're updating the object, it's ok
    if (!$object || $this->isUpdate($object, $values))
    {
      return $originalValues;
    }

    $error = new sfValidatorError($this, 'invalid', array('column' => implode(', ', $this->getOption('column'))));

    if ($this->getOption('throw_global_error'))
    {
      throw $error;
    }

    $columns = $this->getOption('column');

    throw new sfValidatorErrorSchema($this, array($columns[0] => $error));
  }
}
