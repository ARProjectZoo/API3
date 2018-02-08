<?php

namespace Fuel\Migrations;


class Animals
{

    function up()
    {
        \DBUtil::create_table('animals', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'name' => array('type' => 'varchar', 'constraint' => 100),
                'description' => array('type' => 'varchar', 'constraint' => 100),
                'photo' => array('type' => 'varchar', 'constraint' => 100),
                'x' => array('type' => 'decimal', 'constraint' => 65),
                'y' => array('type' => 'decimal', 'constraint' => 65),
                'id_continent' => array('type'=> 'int', 'constraint' => 100),
                'id_user' => array('type'=> 'int', 'constraint' => 100)

        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
    array(
        array(
            'constraint' => 'ForeingKeyAnimalsToContinent',
            'key' => 'id_continent',
            'reference' => array(
                'table' => 'continent',
                'column' => 'id',
            ),
            'on_update' => 'CASCADE',
            'on_delete' => 'RESTRICT'
            ),
        array(
            'constraint' => 'ForeingKeyAnimalsToUser',
            'key' => 'id_user',
            'reference' => array(
                'table' => 'Users',
                'column' => 'id',
            ),
            'on_update' => 'CASCADE',
            'on_delete' => 'RESTRICT'
            )
    
        )
    );

        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Dromedario', 'animal de africa', 'admin','1','0','1','1');")->execute();
        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Oso Panda', 'animal de Asia', 'admin','1','0','2','1');")->execute();
        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Buho Real', 'Animal de America del Norte', 'admin','1','0','3','1');")->execute();
        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Ñandu', 'Animal de America del Sur', 'admin','1','0','4','1');")->execute();
        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Oso Hormiguero', 'America Central y Caribe', 'admin','1','0','5','1');")->execute();
        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Lobo Iberico', 'Animal de Europa', 'admin','1','0','6','1');")->execute();
        \DB::query("INSERT INTO animals(id, name, description, photo, x, y, id_continent, id_user)VALUES(NULL,'Koala', 'animal de Oceania', 'admin','1','0','7','1');")->execute();
    }

    function down()
    {
       \DBUtil::drop_table('animals');
    }
}