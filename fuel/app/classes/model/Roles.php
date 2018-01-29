<?php

class Model_Roles extends Orm\Model 
{
    protected static $_table_name = 'Roles';
    protected static $_primary_key = array('id');
    protected static $_properties = array
    ('id' => array('data_type'=>'int'), // both validation & typing observers will ignore the PK
     'type' => array(
            'data_type' => 'varchar',
            'validation' => array('required', 'max_length' => array(100))
        )
    );
    protected static $_has_many = array(
    'comments' => array(
        'key_from' => 'id',
        'model_to' => 'Model_Users',
        'key_to' => 'id_role',
        'cascade_save' => true,
        'cascade_delete' => false,
    )
);
    

}