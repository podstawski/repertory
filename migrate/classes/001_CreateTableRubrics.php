<?php

class CreateTableRubrics extends Doctrine_Migration_Base
{
    private $_tableName = 'rubrics';

    public function up()
    {
        
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'notnull' => true,
                'primary' => true,
                'autoincrement' => true,
            ),
            'id32' => array(
                'type' => 'character varying(32)',
                'notnull' => false,
            ),
            'pl' => array(
                'type' => 'character varying(350)',
                'notnull' => false,
            ),            
            'en' => array(
                'type' => 'character varying(350)',
                'notnull' => false,
            )
            

        ), array('charset'=>'utf8'));
        
        $this->addIndex($this->_tableName,$this->_tableName.'_id32_key',array('fields'=>array('id32')));
   
    }

    public function down()
    {
        $this->dropTable($this->_tableName);
    }
}
