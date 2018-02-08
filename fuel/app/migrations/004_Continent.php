<?php

namespace Fuel\Migrations;


class Continent
{

    function up()
    {
        \DBUtil::create_table('continent', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'type' => array('type' => 'varchar', 'constraint' => 100)
        ), array('id'));
    
        \DB::query("INSERT INTO continent(id, type)VALUES('1','Africa');")->execute();
        \DB::query("INSERT INTO continent(id, type)VALUES('2','Asia');")->execute();
        \DB::query("INSERT INTO continent(id, type)VALUES('3','America del Norte');")->execute();
        \DB::query("INSERT INTO continent(id, type)VALUES('4','America del Sur');")->execute();
        \DB::query("INSERT INTO continent(id, type)VALUES('5','America central y caribe');")->execute();
        \DB::query("INSERT INTO continent(id, type)VALUES('6','Europa');")->execute();
        \DB::query("INSERT INTO continent(id, type)VALUES('7','Oceania');")->execute();
    }
    function down()
    {
       \DBUtil::drop_table('continent');
    }
}