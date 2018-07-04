<?php

class AlterRubricsAddFulltext2 extends Doctrine_Migration_Base
{
   

    public function up()
    {
        Doctrine_Manager::connection()->exec("
            ALTER TABLE `rubrics` ADD FULLTEXT(`en`,`pl`);
        ");

    }

    public function down()
    {
        Doctrine_Manager::connection()->exec("
            ALTER TABLE `rubrics` DROP FULLTEXT(`en`,`pl`);     
        ");

    }
}
