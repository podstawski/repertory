<?php

class CreateTableCases extends Doctrine_Migration_Base
{
    private $_tableName = 'cases';

    public function up()
    {
        
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'user32' => array(
                'type' => 'character varying(32)',
                'notnull' => false,
            ),
            'lastActivity' => array(
                'type' => 'int',
                'notnull' => false,
            ),            
            'name' => array(
                'type' => 'character varying(350)',
                'notnull' => false,
            )
            

        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_user32_key',array('fields'=>array('user32')));
   
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
