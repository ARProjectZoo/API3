<?php

class Model_Animals extends Orm\Model 
{
    protected static $_table_name = 'animals';
    protected static $_primary_key = array('id');
    protected static $_properties = array
    ('id' => array('data_type'=>'int'), // both validation & typing observers will ignore the PK
     'name' => array(
            'data_type' => 'varchar',
            'validation' => array('required', 'max_length' => array(100))
        ),
     'description' => array(
                'data_type' => 'varchar',
                'validation' => array('required', 'max_length' => array(100))   
            ),
     'photo' => array(
                'data_type' => 'varchar',
                'validation' => array('required', 'max_length' => array(100))   
            ),
     'x' => array(
                'data_type' => 'varchar',
                'validation' => array('required', 'max_length' => array(65))
                ),
     'y' => array(
                'data_type' => 'varchar',
                'validation' => array('required', 'max_length' => array(65))
                ),
     'id_continent' => array(
                'data_type' => 'int',
                'validation' => array('required', 'max_length' => array(100))   
            ),
     'id_user' => array(
                'data_type' => 'int',
                'validation' => array('required', 'max_length' => array(100))   
            ),
    );
    protected static $_belongs_to = array(
        'role' => array(
            'key_from' => 'id_user',
            'model_to' => 'Model_Users',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    protected static $_belongs_to = array(
        'role' => array(
            'key_from' => 'id_continent',
            'model_to' => 'Model_Continent',
            'key_to' => 'id',
            'cascade_save' => true,
            'cascade_delete' => false,
        )
    );
    

}