<?php
use \Model\Users;

class Controller_Roles extends Controller_Base
{
	public function post_configRoles()
	{
		$roleAdmin = new Model_Roles();
		$roleAdmin->type = 'Admin';
		$roleUser = new Model_Roles();
		$roleUser->type = 'User';
		$roleAdmin->save();
		$roleUser->save();
		$json = $this->response(array(
                    'code' => 201,
                    'message' => 'Roles Creados'
                ));
		
	}
}