<?php

class CreateTableRemedies extends Doctrine_Migration_Base
{
    private $_tableName = 'remedies';

    public function up()
    {
        
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'short' => array(
                'type' => 'character varying(32)',
                'notnull' => true,
            ),
            'name' => array(
                'type' => 'character varying(255)',
                'notnull' => false,
            )
            

        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_short_key',array('fields'=>array('short')));
   
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
