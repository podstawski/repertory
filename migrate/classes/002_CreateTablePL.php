<?php

class CreateTablePL extends Doctrine_Migration_Base
{
   
    
    
    public function up()
    {
        Doctrine_Manager::connection()->exec("
            CREATE TABLE `pl` (
              `id` int NOT NULL AUTO_INCREMENT,
              `rubric` int(11),
              `word` varchar(128) NOT NULL,
              seqno int,
              PRIMARY KEY (`id`),
              CONSTRAINT cst_rubric_pl
              FOREIGN KEY fk_rubric_pl (rubric)
              REFERENCES rubrics(id)
              ON DELETE CASCADE 
              ON UPDATE CASCADE 
            ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8;
            ALTER TABLE `pl` ADD FULLTEXT(`word`);
        ");

    }

    public function down()
    {

        Doctrine_Manager::connection()->exec("DROP TABLE `pl`");

    }
}
