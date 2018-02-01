<?php
use \Model\Users;
use Firebase\JWT\JWT;

class Controller_Stories extends Controller_Base
{
  	public function post_create()
    {
    	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	if($arrayAuthenticated['authenticated'])
    	{
    		$decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
    		
	        try {
		        	if (!isset($_FILES['photo']) || empty($_FILES['photo'])) 
		            {
		            	$arrayData = array();
		            	$arrayData['files'] = $_FILES;
		            	$arrayData['post'] = $_POST; 
		                $json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'La photo esta vacia',
		                    'data' =>  $arrayData
		                ));
		                return $json;
		            }

		            if(!isset($_POST['comment']) || empty($_POST['comment']))
		            {
		            	$json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'El comment esta vacio',
		                    'data' => '' 
		                ));
		                return $json;
		            }

		            if( !isset($_POST['date']) || empty($_POST['date']))
		            {
		            	$json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'El date esta vacio',
		                    'data' => '' 
		                ));
		                return $json;
					}

	        	 	$config = array(
			            'path' => DOCROOT . 'assets/img',
			            'randomize' => true,
			            'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
			        );

			        Upload::process($config);
			        $photoToSave = "";
			        if (Upload::is_valid())
			        {
			            Upload::save();
			            foreach(Upload::get_files() as $file)
			            {
			            	// var_dump($_FILES['photo']['saved_as']);
			            	$photoToSave = 'http://localhost:8888/ARAPI/public/assets/img/'. $file['saved_as'];
			            	

			            	//'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/APRAPI/public/assets/img/'. $file['saved_as'];
			            	
			            }
			        }

			        foreach (Upload::get_errors() as $file)
			        {
			            return $this->response(array(
			                'code' => 500,
			                'message' => 'Error en el servidor',
			                'data' => '' 
			            ));
			        }
			    
		            $newStory = $this->newStory($_POST, $photoToSave, $decodedToken);
		           	$json = $this->saveStory($newStory);
		            return $json;
		        
	        }catch (Exception $e){
	            $json = $this->response(array(
	                'code' => 500,
	                'message' =>  "TRYCATCH ::: ".$e->getMessage(),
	                'data' => '' 
	            ));
	            return $json;
	        }      
    	 }else{
			$json = $this->response(array(
				                'code' => 401,
				                'message' =>  "No autenticado",
				                'data' => '' 
				            ));
			return $json;
     	}
	 }

  
	private function newStory($input, $photoToSave, $decodedToken)
    {
    	$story = new Model_Stories();
        $story->photo = $photoToSave;
        $story->comment = $input['comment'];
        $story->date = $input['date'];
        $story->id_user = $decodedToken->id;
        return $story;
    }

    private function saveStory($story)
    {
		$storyToSave = $story;
    	$storyToSave->save();
    	$arrayData = array();
    	$arrayData['storySaved'] = $arrayData;
    	$json = $this->response(array(
                'code' => 201,
                'message' => 'Recuerdo creado',
                'data' => $arrayData
            ));
    	return $json;
    }

	public function post_delete()
    {
    	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	
    	 if($arrayAuthenticated['authenticated']){
    		 $decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
    		 if(!empty($_POST['id'])){
	       		 $story = Model_Stories::find($_POST['id']);
	       		 if(isset($story)){
		       		 if($decodedToken->id == $story->id_user){
			       		 $story->delete(); 
					
			       		 $json = $this->response(array(
			       		     'code' => 200,
			       		     'message' => 'recuerdo borrado',
			       		    	'data' => ''
			       		 ));
			       		 return $json;
			       		}else{
			       			$json = $this->response(array(
			       		     'code' => 401,
			       		     'message' => 'No puede borrar un recuerdo que no es tuyo',
			       		    	'data' => ''
			       		 	));
			       		 	return $json;
		       		}
			       	}else{
			       		$json = $this->response(array(
			       		     'code' => 401,
			       		     'message' => 'Recuerdo no valido',
			       		    	'data' => ''
			       		 	));
			       		 	return $json;
			       		}
			       	}else{
			       		$json = $this->response(array(
			       		     'code' => 400,
			       		     'message' => 'El id no puede estar vacio',
			       		    	'data' => ''
			       		 	));
			       		 	return $json;
			       		}
	       	}else{
	       			$json = $this->response(array(
	       		     'code' => 400,
	       		     'message' => 'Falta el autorizacion',
	       		     'data' => ''
	       		 	));
	       		 	return $json;
	       		}
    	}

	public function get_show()
    {	
    	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	 $decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
    	 if($arrayAuthenticated['authenticated']){
	    		if(isset($_GET['idStory'])){
	    			$idStory = $_GET['idStory'];
	    			$story = Model_Stories::find('all',
	    											array('where' => array(
			            							array('id_user', '=', $decodedToken->id),
			            							array('id', '=', $idStory) 
			            							)
			            						)
			            					);
	    			if(!empty($story)){
	    				return $this->respuesta(200, 'mostrando el recuerdo', Arr::reindex($story));	    					
	    			}else{
	    					$json = $this->response(array(
				       		     'code' => 202,
				       		     'message' => 'Aun no tienes ningun recuerdo',
				       		    	'data' => ''
				       		 	));
				       		 	return $json;
	    			}
    		
	    		}else{
		    		$stories = Model_Stories::find('all', 
				            						array('where' => array(
				            							array('id_user', '=', $decodedToken->id), 
				            							)
				            						)
				            					);
		    		if(!empty($stories)){
		    			return $this->respuesta(200, 'mostrando lista de recuerdos del usuario', Arr::reindex($stories));	    					
		    		}else{
		    			
		    			$json = $this->response(array(
					       		     'code' => 202,
					       		     'message' => 'Aun no tienes ningun recuerdo',
					       		    	'data' => ''
					       		 	));
					       		 	return $json;
		    			}
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
}

