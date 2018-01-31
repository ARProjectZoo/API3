<?php
use \Model\Users;
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Base
{
    private  $idAdmin = 1;
    private  $idUser = 2;

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
    		$json = $this->response(array(
                    'code' => 201,
                    'message' => 'Usuario creado',
                    'data' => $user->userName
                ));
    		return $json;
    	}else{
    		$json = $this->response(array(
                    'code' => 204,
                    'message' => 'Usuario ya registrado',
                    'data' => ''
                ));
    		return $json;
    	}
    }

    public function post_register()
    {
        try {
            if ( !isset($_POST['userName']) || !isset($_POST['password']) || !isset($_POST['email'])) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Algun paramentro esta vacio',
                    'data' => ''
                )); 
                return $json;
            }if(isset($_POST['x']) || isset($_POST['y'])){
            		if(empty($_POST['x']) || empty($_POST['y'])){
	            		$json = $this->response(array(
	                    'code' => 400,
	                    'message' => 'Coordenadas vacias',
	                    'data' => ''
	                	));
	                	return $json;
	                }
            	}else{
            		$json = $this->response(array(
	                    'code' => 400,
	                    'message' => 'Coordenadas no definidas',
	                    'data' => ''
	                	));
	                	return $json;
            	}
            if(!empty($_POST['userName']) && !empty($_POST['password']) && !empty($_POST['email'])){
            	if(strlen($_POST['password']) < 5){
            		$json = $this->response(array(
                    'code' => 400,
                    'message' => 'La contraseña debe tener al menos 5 caracteres',
                    'data' => ''
                ));
                return $json;
            	}
				$input = $_POST;
	            $newUser = $this->newUser($input);
	           	$json = $this->saveUser($newUser);
	            return $json;
	        }else{
	        	$json = $this->response(array(
                    'code' => 400,
                    'message' => 'Algun campo vacio',
                     'data' => ''
                ));
                return $json;
	        }
        }catch (Exception $e){
            $json = $this->response(array(
                'code' => 500,
                'message' =>  $e->getMessage(),
                 'data' => ''
            ));
            return $json;
        }      
    }

    public function post_login()
    {	try{
	        if ( !isset($_POST['userName']) || !isset($_POST['password']) ) {
	            $json = $this->response(array(
	                    'code' => 400,
	                    'message' => 'alguno de los datos esta vacio',
	                     'data' => ''
	                ));
	                return $json;
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
	               	$arrayData['data'] = '';
	                $json = $this->response(array(
	                    'code' => 200,
	                    'message' => 'Log In correcto',
	                    'data' => $arrayData
	                    ));
	                return $json; 
	        	}else{
	        		$json = $this->response(array(
	                    'code' => 400,
	                    'message' => 'Algun dato erroneo',
	                     'data' => ''
	                ));
	                return $json;
	            	}
	        }else{
	        	$json = $this->response(array(
	                'code' => 400,
	                'message' => 'No se permiten cadenas de texto vacias',
	                 'data' => ''
	            ));
	            return $json;
          	}
	        	
	    }catch(Exception $e){
	    	$json = $this->response(array(
	            'code' => 500,
	            'message' =>  $e->getMessage(),
	             'data' => ''
            ));
            return $json;
	    }
	}
	
	public function post_forgotPassword()
	{
		try{
			$input = $_POST;
			if ( !isset($_POST['userName']) || !isset($_POST['email']) ) {
	            $json = $this->response(array(
	                    'code' => 400,
	                    'message' => 'alguno de los datos esta vacio',
	                     'data' => ''
	                ));
	                return $json;
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
	                $json = $this->response(array(
	                    'code' => 200,
	                    'message' => 'Log In correcto',
	                    'data' => $token
	                    ));
	                return $json; 
		    }else{
		    	 $json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'Usuario no encontrado.',
		                    'data' => $token
		                    ));
		                return $json;
		    	}
			}
		}catch(Exception $e){
		    		 $json = $this->response(array(
		                'code' => 500,
		                'message' =>  $e->getMessage(),
		                 'data' => ''
		            ));
		            return $json;
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
							$json = $this->response(array(
					                    'code' => 200,
					                    'message' => 'Contraseña modificada correctamente',
					                    'data' => $token
					                    ));
					                return $json;
					            }else{
					            	$json = $this->response(array(
					                    'code' => 200,
					                    'message' => 'Contraseña demasiado corta',
					                    'data' => ""
					                    ));
					                return $json;
					            }
				    }else{
				        $json = $this->response(array(
				            'code' => 400,
				            'message' => 'Contraseña vacia',
				             'data' => ''
				        ));
				        }
				}else{
					$json = $this->response(array(
				                    'code' => 400,
				                    'message' => 'Campos vacios',
				                    'data' => ""
				                    ));
				                return $json;
				}
			}else{
				$json = $this->response(array(
				                    'code' => 400,
				                    'message' => 'password vacia, por favor rellenela',
				                    'data' => ""
				                    ));
				                return $json;
			}
		}else{
			$json = $this->response(array(
				                    'code' => 400,
				                    'message' => 'NO AUTORIZADO',
				                    'data' => ""
				                    ));
				                return $json;
		}

	}
	public function get_show()
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
	    	
	    		if(!empty($user))
	    		{
	    			return $this->respuesta(200, 'info User', Arr::reindex($user));

	    		}else{
	    			
	    			$json = $this->response(array(
				       		     'code' => 202,
				       		     'message' => 'Usuario no encontrado',
				       		    	'data' => ''
				       		 	));
				       		 	return $json;
	    			}
    		}else{
    			
    			$json = $this->response(array(
			       		     'code' => 401,
			       		     'message' => 'NO AUTORIZACION',
			       		    	'data' => ''
			       		 	));
			       		 	return $json;
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
		                $json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'La photo esta vacia'
		                ));
		                return $json;
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
			            return $this->response(array(
			                'code' => 500,
			            ));
			        }
			    
		         //FALTA AQUI GUARDAR LOS CAMBIOS DEL PICTURE PROFILE DEL USER. Y EL MENSAJE 200
		        
	        }catch (Exception $e){
	            $json = $this->response(array(
	                'code' => 500,
	                'message' =>  $e->getMessage()
	            ));
	            return $json;
	        }      
    	 }else{
			$json = $this->response(array(
				                'code' => 401,
				                'message' =>  "No autenticado"
				            ));
			return $json;
     	}
	 }
}


