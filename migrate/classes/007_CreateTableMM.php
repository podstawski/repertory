<?php

class CreateTableMM extends Doctrine_Migration_Base
{
   
    
    
    public function up()
    {
        Doctrine_Manager::connection()->exec("
            CREATE TABLE `mm` (
              `id` int NOT NULL AUTO_INCREMENT,
              `book` varchar(64),
              `en` text,
              `remedy` int(11),
              PRIMARY KEY (`id`),
              CONSTRAINT cst_remedy_mm
              FOREIGN KEY fk_remedy_mm (`remedy`)
              REFERENCES remedies(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE
               
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
    
        ");

    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("DROP TABLE `mm`");

    }
}
