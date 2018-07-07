<?php

class AlterTableRubricsAddDr extends Doctrine_Migration_Base
{
    private $_tableName = 'rubrics';
    protected $_columnName = ['dr'=>'Integer'];



    public function up()
    {
        foreach ($this->_columnName AS $prop=>$type)
            $this->addColumn($this->_tableName, $prop, $type, null, array('notnull' => false ));

    }
    

    public function down()
    {
        foreach ($this->_columnName AS $prop=>$type)
            $this->removeColumn($this->_tableName, $prop);
    }
}
