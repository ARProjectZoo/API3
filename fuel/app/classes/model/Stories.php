<?php

class Model_Stories extends Orm\Model 
{
    protected static $_table_name = 'Stories';
    protected static $_primary_key = array('id');
    protected static $_properties = array
    ('id' => array('data_type'=>'int'), // both validation & typing observers will ignore the PK
     'photo' => array(
            'data_type' => 'varchar',
            'validation' => array('required', 'max_length' => array(400))
        ),
     'comment' => array(
                'data_type' => 'varchar',
                'validation' => array('required', 'max_length' => array(300))   
         ),
     'date' => array(
                'data_type' => 'varchar',
                'validation' => array('required', 'max_length' => array(100))   
         ),
     'id_user' => array(
                'data_type' => 'int',
                'validation' => array('required', 'max_length' => array(100)))   
    );
    protected static $_belongs_to = array(
        'user' => array(
            'key_from' => 'id_user',
            'model_to' => 'Model_Users',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
}