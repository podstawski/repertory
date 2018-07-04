<?php

class CreateTableDR extends Doctrine_Migration_Base
{
   
    
    
    public function up()
    {
        Doctrine_Manager::connection()->exec("
            CREATE TABLE `dr` (
              `id` int NOT NULL AUTO_INCREMENT,
              `from` int(11),
              `to` int(11),
              PRIMARY KEY (`id`),
              CONSTRAINT cst_from_dr
              FOREIGN KEY fk_from_dr (`from`)
              REFERENCES rubrics(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE,
              CONSTRAINT cst_to_dr
              FOREIGN KEY fk_to_dr (`to`)
              REFERENCES rubrics(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE
               
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
    
        ");

    }

    public function down()
    {

        Doctrine_Manager::connection()->exec("DROP TABLE `dr`");

    }
}
