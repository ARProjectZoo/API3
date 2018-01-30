<?php 

class Model_Images extends Orm\Model
{
    protected static $_table_name = 'images';
    protected static $_properties = array(
        'id', // both validation & typing observers will ignore the PK
        'title' => array(
            'data_type' => 'varchar',
        ),
    );
}