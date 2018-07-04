<?php

class CreateTableRR extends Doctrine_Migration_Base
{
   
    
    
    public function up()
    {
        Doctrine_Manager::connection()->exec("
            CREATE TABLE `rr` (
              `id` int NOT NULL AUTO_INCREMENT,
              `rubric` int(11),
              `remedy` int(11),
              `weight` int(2),
              PRIMARY KEY (`id`),
              CONSTRAINT cst_rubric_rr
              FOREIGN KEY fk_rubric_rr (`rubric`)
              REFERENCES rubrics(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE,
              CONSTRAINT cst_remedy_rr
              FOREIGN KEY fk_remedy_rr (`remedy`)
              REFERENCES remedies(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
    
        ");

    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("DROP TABLE `rr`");

    }
}
