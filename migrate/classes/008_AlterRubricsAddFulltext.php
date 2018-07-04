<?php

class AlterRubricsAddFulltext extends Doctrine_Migration_Base
{
   

    public function up()
    {
        Doctrine_Manager::connection()->exec("
            ALTER TABLE `rubrics` ADD FULLTEXT(`en`);
            ALTER TABLE `rubrics` ADD FULLTEXT(`pl`);
        ");

    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("
            ALTER TABLE `rubrics` DROP FULLTEXT(`en`);
            ALTER TABLE `rubrics` DROP FULLTEXT(`pl`);       
        ");

    }
}
