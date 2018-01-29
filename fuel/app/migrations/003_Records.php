<?php

namespace Fuel\Migrations;


class Records
{

    function up()
    {
        \DBUtil::create_table('Records', 
            array(
                'id' => array('type' => 'int', 'constraint' => 100,'auto_increment' => true),
                'photo' => array('type' => 'varchar', 'constraint' => 300),
                'comment' => array('type'=> 'varchar', 'constraint' => 300),
                'id_user' => array('type'=> 'int', 'constraint' => 100)
        ), array('id'), false, 'InnoDB', 'utf8_unicode_ci',
    array(
        array(
            'constraint' => 'ForeingKeyRecordsToUsers',
            'key' => 'id_user',
            'reference' => array(
                'table' => 'Users',
                'column' => 'id',
            ),
            'on_update' => 'CASCADE',
            'on_delete' => 'RESTRICT'))
        );    
    }

    function down()
    {
       \DBUtil::drop_table('Records');
    }
}