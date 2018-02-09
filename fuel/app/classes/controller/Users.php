<?php
use \Model\Users;
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Base
{
    private  $idAdmin = 1;
    private  $idUser = 2;

    public function post_register()
    {
        try {
            if ( !isset($_POST['userName']) || !isset($_POST['password']) || !isset($_POST['email'])) 
            {
            	return $this->respuesta(400, 'Algun paramentro esta vacio', '');
            }if(isset($_POST['x']) || isset($_POST['y'])){
            		if(empty($_POST['x']) || empty($_POST['y'])){
	            		return $this->respuesta(400, 'Coordenadas vacias', '');
	            	}
            	}else{
            		return $this->respuesta(400, 'Coordenadas no definidas', '');
            	}
            if(!empty($_POST['userName']) && !empty($_POST['password']) && !empty($_POST['email'])){
            	if(strlen($_POST['password']) < 5){
            		return $this->respuesta(400, 'La contraseña debe tener al menos 5 caracteres', '');
            	}
				$input = $_POST;
	            $newUser = $this->newUser($input);
	           	$json = $this->saveUser($newUser);
	        }else{
	        	return $this->respuesta(400, 'Algun campo vacio', '');
	        }
        }catch (Exception $e){
        	return $this->respuesta(500, $e->getMessage(), '');
        }      
    }
    private function newUser($input)
    {
    		$user = new Model_Users();
            $user->userName = $input['userName'];
            $user->password = $this->encode($input['password']);
            $user->email = $input['email'];
            $user->id_device = $input['id_device'];
            $user->id_role = $this->idUser;
            $user->x = $input['x'];
            $user->y = $input['y'];
            return $user;
    }

    private function saveUser($user)
    {
    	$userExists = Model_Users::find('all', 
    								array('where' => array(
    													array('email', '=', $user->email),
    														)
    									)
    							);
    	if(empty($userExists)){
    		$userToSave = $user;
    		$userToSave->save();
    		$arrayData = array();
    		$arrayData['userName'] = $user->userName;
    		return $this->respuesta(201, 'Usuario creado', $arrayData);
    	}else{
    		return $this->respuesta(204, 'Usuario ya registrado', '');
    	}
    }

    public function post_login()
    {	try{
	        if ( !isset($_POST['userName']) || !isset($_POST['password']) ) {
	        	return $this->respuesta(400, 'alguno de los datos esta vacio', '');
	        }else if( !empty($_POST['userName']) && !empty($_POST['password'])){
	            $input = $_POST;
	            $user = Model_Users::find('all', 
		            						array('where' => array(
		            							array('userName', '=', $input['userName']), 
		            							array('password', '=', $this->encode($input['password']))
		            							)
		            						)
		            					);
	            if(!empty($user))
	            {
	            	$user = reset($user);
	            	$userName = $user->userName;
	            	$password = $user->password;
	            	$id = $user->id;
	            	$email = $user->email;
	            	$id_role = $user->id_role;
	                $token = $this->encodeToken($userName, $password, $id, $email, $id_role);
	                $arrayData = array();
	               	$arrayData['token'] = $token;
	               	return $this->respuesta(200, 'Log In correcto', $arrayData);
	        	}else{
	        		return $this->respuesta(400, 'algun dato erroneo', '');
	       		 }
	     
	        }else{
	        	return $this->respuesta(400, 'No se permiten cadenas de texto vacias', '');
	        }
	        	
	    }catch(Exception $e){
	    	return $this->respuesta(500, $e->getMessage(), '');
	    }
	}
	
	public function post_forgotPassword()
	{
		try{
			$input = $_POST;
			if ( !isset($_POST['userName']) || !isset($_POST['email']) ) {
				return $this->respuesta(400, 'alguno de los datos esta vacio', '');
	        }else if( !empty($_POST['userName']) && !empty($_POST['email'])){
		    	$user = Model_Users::find('all', 
		           					array('where' => array(
		           							array('userName', '=', $input['userName']), 
		           							array('email', '=', $input['email'])
		           							)
		           						)
		           					);
			    if($user != null){
			   		   	$user = reset($user);
		            	$userName = $user->userName;
		            	$password = $user->password;
		            	$id = $user->id;
		            	$email = $user->email;
		            	$id_role = $user->id_role;
		                $token = $this->encodeToken($userName, $password, $id, $email, $id_role);
		                $arrayData = array();
		               	$arrayData['token'] = $token;
		               	return $this->respuesta(200, 'forgot correcto', $arrayData);
			    }else{
			    	return $this->respuesta(400, 'Usuario no encontrado.', '');
			    }
			}
		}catch(Exception $e){
			return $this->respuesta(500, $e->getMessage(), '');
		}
	}

	public function post_changePassword()
	{
		$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	
    	 if($arrayAuthenticated['authenticated']){
			$newPassword = $_POST['newPassword'];
			if( isset($newPassword)) {
				$decodedToken = $this->decodeToken();
				$user = Model_Users::find('all', 
				            					array('where' => array(
				            							array('userName', '=', $decodedToken->userName), 
				            							array('password', '=', $decodedToken->email)
				            							)
				            						)
				            					);
				if(isset($newPassword)){
					if(!empty($newPassword)){
						if(strlen($newPassword) >= 5){
							$userTochange = Model_Users::find($decodedToken->id);
							$userTochange ->password = $this->encode($newPassword);
							$userTochange -> save();

								$userName = $userTochange->userName;
				            	$password = $userTochange->password;
				            	$id = $userTochange->id;
				            	$email = $userTochange->email;
				            	$id_role = $userTochange->id_role;

							$token = $this->encodeToken($userName, $password, $id, $email, $id_role);
							$arrayData = array();
			               	$arrayData['token'] = $token;
			               	return $this->respuesta(200, 'Contraseña modificada correctamente', $arrayData);
					    }else{
					    	return $this->respuesta(204, 'Contraseña demasiado corta', "");
					    }
				    }else{
				    	return $this->respuesta(400, 'Contraseña vacios', "");
				        }
				}else{
					return $this->respuesta(400, 'Campos vacios', "");
				}
			}else{
				return $this->respuesta(400, 'parametro no definido', "");
			}
		}else{
			return $this->respuesta(400, 'NO AUTORIZADO', "");
		}

	}
	public function get_show()
	{
		$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	
    	 if($arrayAuthenticated['authenticated']){
	    		$decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
	    		//Model_Users::find($decodedToken->id);
	    		
	    		// if(!empty($user))
	    		// {
	    			//reset($user);
	    			$arrayData = array();
	    			$arrayData['userName'] = $decodedToken->userName;
	    			//$arrayData['userName'] = $decodedToken->userName;
	    			//$arrayData['']
	    			return $this->respuesta(200, 'info User', $arrayData);
				// }else{
	   //  			return $this->respuesta(202, 'Usuario no encontrado','');
    	//	}
    	}else{
    			return $this->respuesta(401, 'NO AUTORIZACION','');
    		}
    }
    public function post_changeImage()
    {
    	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    
    	 if($arrayAuthenticated['authenticated']){
	    		$decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
	    		$user = Model_Users::find('all', 	array('where' => array(
			            							array('id', '=', $decodedToken->id), 
			            							)
			            						)
			            					);  		
	        try {
		        	if (!isset($_FILES['profilePicture']) || empty($_FILES['profilePicture'])) 
		            {
		            	return $this->respuesta(401, 'La photo esta vacia','');
		            }
	        	 	$config = array(
			            'path' => DOCROOT . 'assets/img',
			            'randomize' => true,
			            'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
			        );

			        Upload::process($config);

			        if (Upload::is_valid())
			        {
			            Upload::save();
			            foreach(Upload::get_files() as $file)
			            {
			            	$_POST['profilePicture'] = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/APIZoo/fuelphp/public/assets/img/'
			            	. $file['saved_as'];
			            }
			        }

			        foreach (Upload::get_errors() as $file)
			        {
			            return $this->respuesta(500, 'error en la subida','');
			        }
			    
		         //FALTA AQUI GUARDAR LOS CAMBIOS DEL PICTURE PROFILE DEL USER. Y EL MENSAJE 200
			        $newProfilePhoto = $_POST['newPhoto'];
					if(isset($newProfilePhoto)){
						if(!empty($newProfilePhoto)){
								$userTochange = Model_Users::find($decodedToken->id);
								$userTochange ->profilePicture = $this->encode($newProfilePhoto);
								$userTochange -> save();

									$newProfilePhoto = $userTochange->profilePicture;
				            		$password = $userTochange->password;
				            		$id = $userTochange->id;
				            		$email = $userTochange->email;
				            		$id_role = $userTochange->id_role;

								$token = $this->encodeToken($userName, $password, $id, $email, $id_role);
								$arrayData = array();
			               		$arrayData['token'] = $token;
			               		return $this->respuesta(200, 'Foto modificada correctamente', $arrayData);
					    	
				    	}else{
				    		return $this->respuesta(400, 'Foto vacia', "");
				        }
					}else{
					return $this->respuesta(400, 'Campos vacios', "");
				}
		        
	        }catch (Exception $e){
	        	return $this->respuesta(500, $e->getMessage(),'');
		  }      
    	 }else{
    	 	return $this->respuesta(401, 'No autenticado','');
     	}
	 }
}


