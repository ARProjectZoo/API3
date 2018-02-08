<?php

namespace Fuel\Migrations;


class Type
{

    function up()
    {
        \DBUtil::create_table('type', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'name' => array('type' => 'varchar', 'constraint' => 100)
        ), array('id'));
    
        \DB::query("INSERT INTO type(id, name)VALUES('1','Restaurantes');")->execute();
        \DB::query("INSERT INTO type(id, name)VALUES('2','Exhibiciones');")->execute();
    }
    function down()
    {
       \DBUtil::drop_table('type');
    }
}