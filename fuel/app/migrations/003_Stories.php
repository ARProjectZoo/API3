<?php

namespace Fuel\Migrations;


class Stories
{

    function up()
    {
        \DBUtil::create_table('Stories', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'photo' => array('type' => 'varchar', 'constraint' => 100),
                'comment' => array('type' => 'varchar', 'constraint' => 300),
                'date' => array('type' => 'varchar', 'constraint' => 300),
                'id_user' => array('type'=> 'int', 'constraint' => 100)

        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
    array(
        array(
            'constraint' => 'ForeingKeyStoryToUser',
            'key' => 'id_user',
            'reference' => array(
                'table' => 'Users',
                'column' => 'id',
            ),
            'on_update' => 'CASCADE',
            'on_delete' => 'RESTRICT'
            ))
        );    
    }

    function down()
    {
       \DBUtil::drop_table('Stories');
    }
}