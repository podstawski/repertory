<?php

class CreateTableCaseR extends Doctrine_Migration_Base
{
   
    
    
    public function up()
    {
        Doctrine_Manager::connection()->exec("
            CREATE TABLE `caserub` (
              `id` int NOT NULL AUTO_INCREMENT,
              `case` int(11),
              `rubric` int(11),
              `weight` int(2),
              PRIMARY KEY (`id`),
              CONSTRAINT cst_case_r
              FOREIGN KEY fk_case_r (`case`)
              REFERENCES cases(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE,
              CONSTRAINT cst_rubric_c
              FOREIGN KEY fk_rubric_c (`rubric`)
              REFERENCES rubrics(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
    
        ");

    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("DROP TABLE `caserub`");

    }
}
