<?php

namespace Fuel\Migrations;


class Elements
{

    function up()
    {
        \DBUtil::create_table('elements', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'name' => array('type' => 'varchar', 'constraint' => 100),
                'description' => array('type' => 'varchar', 'constraint' => 100),
                'photo' => array('type' => 'varchar', 'constraint' => 100),
                'x' => array('type' => 'decimal', 'constraint' => 65),
                'y' => array('type' => 'decimal', 'constraint' => 65),
                'id_type' => array('type'=> 'int', 'constraint' => 100),
                'id_user' => array('type'=> 'int', 'constraint' => 100)

        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
    array(
        array(
            'constraint' => 'ForeingKeyElementsToType',
            'key' => 'id_type',
            'reference' => array(
                'table' => 'type',
                'column' => 'id',
            ),
            'on_update' => 'CASCADE',
            'on_delete' => 'RESTRICT'
            ),
        array(
            'constraint' => 'ForeingKeyElementsToUser',
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

        //RESTAURANTES
         \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Restaurante Kibanda', 'Restaurante relacionado  a Africa', 'admin','1','0','1','1');")->execute();
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Kiosco Australia', 'Fast Food australiana', 'admin','1','0','1','1');")->execute();
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Hamburgueseria Virunga', 'Restaurante al aire libre para disfrutar de la naturaleza', 'admin','1','0','1','1');")->execute();
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Restaurante Bagaray', 'Restaurante tematico de comida de temporada', 'admin','1','0','1','1');")->execute();
        
        //EXHIBICIONES
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Delfines', 'Exhibiciones unicas que emocionaran a grandes y pequeños', 'admin','1','0','2','1');")->execute();
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Leones Marinos', 'Exhibicion que nos enseñara curiosidades de esta especie y el cuidado medio ambiental', 'admin','1','0','2','1');")->execute();
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Aves Rapaces', 'Vuelos libres de aves rapaces', 'admin','1','0','2','1');")->execute();
        \DB::query("INSERT INTO elements(id, name, description, photo, x, y, id_type, id_user)VALUES(NULL,'Aves Exóticas', 'Vuelos libres de aves exoticas', 'admin','1','0','2','1');")->execute();
    }

    function down()
    {
       \DBUtil::drop_table('elements');
    }
}