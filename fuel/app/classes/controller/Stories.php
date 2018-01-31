<?php
use \Model\Users;
use Firebase\JWT\JWT;

class Controller_Stories extends Controller_Base
{
  	public function post_create()
    {
    	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	 if($arrayAuthenticated['authenticated']){
    		 $decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
    		
	        try {
		        	if (!isset($_POST['photo']) || empty($_POST['photo'])) 
		            {
		                $json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'La photo esta vacia'
		                ));
		                return $json;
		            }
		            if(!isset($_POST['comment']) || empty($_POST['comment'])){
		            	$json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'El comment esta vacio'
		                ));
		                return $json;
		            }
		            if( !isset($_POST['date']) || empty($_POST['date'])){
		            	$json = $this->response(array(
		                    'code' => 400,
		                    'message' => 'El date esta vacio'
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
			            	$story->photo = 'http://' . $_SERVER['SERVER_NAME'] . '/APIZoo/public/assets/img/'
			            	. $file['saved_as'];
			            }
			        }
			        foreach (Upload::get_errors() as $file)
			        {
			            return $this->response(array(
			                'code' => 500,
			            ));
			        }
			        return $this->response(array(
			            'code' => 200,
			        ));
			    
	            	$input = $_POST;
		            $newStory = $this->newStory($input, $decodedToken);
		           	$json = $this->saveStory($newStory);
		            return $json;
		        
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
    

	private function newStory($input, $decodedToken)
    {
    	$story = new Model_Stories();
        $story->photo = $input['photo'];
        $story->comment = $input['comment'];
        $story->date = $input['date'];
        $story->id_user = $decodedToken->id;
        return $story;
    }

    private function saveStory($story)
    {
		$storyToSave = $story;
    	$storyToSave->save();
    	$json = $this->response(array(
                'code' => 201,
                'message' => 'Recuerdo creado',
                'date' => $story->date 
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

	public function post_show()
    {	
    	$authenticated = $this->authenticate();
    	$arrayAuthenticated = json_decode($authenticated, true);
    	 $decodedToken = JWT::decode($arrayAuthenticated["data"], MY_KEY, array('HS256'));
    	 if($arrayAuthenticated['authenticated']){
	    		if(isset($_POST['idStory'])){
	    			$idStory = $_POST['idStory'];
	    			$story = Model_Stories::find('all',
	    											array('where' => array(
			            							array('id_user', '=', $decodedToken->id),
			            							array('id', '=', $idStory) 
			            							)
			            						)
			            					);
	    			if(!empty($story)){
	    				return $this->respuesta(200, 'una historia', Arr::reindex($story));	    					
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

     
	// public function post_modify()
	// {

	// 	try{
	// 		$input = $_POST;
	// 		if ( !isset($_POST['photo']) || !isset($_POST['comment']) ) {
	//             $json = $this->response(array(
	//                     'code' => 400,
	//                     'message' => 'alguno de los datos esta vacio'
	//                 ));
	//                 return $json;
	//         }else if( !empty($_POST['photo']) && !empty($_POST['comment'])){
	// 	    	$story = Model_Stories::find('all', 
	// 	           					array('where' => array(
	// 	           							array('photo', '=', $input['photo']), 
	// 	           							array('comment', '=', $input['comment'])
	// 	           							)
	// 	           						)
	// 	           					);
	// 	    if($story != null){
	// 	    	$story = reset($story);
	// 	    	$photo = $story->photo;
	//             $comment = $story->comment;
	//             $id = $story->id; ///////
	//             $id_user = $story->id_user;///////////
	// 	    	$token = $this->encodeToken($photo, $comment, $id, $id_user);
	// 	        $json = $this->response(array(
	// 	                    'code' => 200,
	// 	                    'message' => 'Historia encontrada, se puede cambiar',
	// 	                    'token' => $token
	// 	                    ));
	// 	                return $json;
	// 	    }else{
	// 	    	 $json = $this->response(array(
	// 	                    'code' => 400,
	// 	                    'message' => 'Historia no encontrada.',
	// 	                    'data' => $token
	// 	                    ));
	// 	                return $json;
	// 	    	}
	// 		}
	// 	}catch(Exception $e){
	// 	    		 $json = $this->response(array(
	// 	                'code' => 500,
	// 	                'message' =>  $e->getMessage()
	// 	            ));
	// 	            return $json;
	// 	    	}
	// }

	// public function post_saveModify()
	// {
	// 	$newPhoto = $_POST['newPhoto'];
	// 	if( isset($newPhoto)) {
	// 		$decodeToken = $this->decodeToken();
	// 		$story = Model_stories::find('all', 
	// 		            					array('where' => array(
	// 		            							array('id_story', '=', $decodeToken->id_story), 
	// 		            							array('photo', '=', $decodeToken->photo)
	// 		            							)
	// 		            						)
	// 		            					);
	// 		if(isset($newPhoto)){
	// 			$story = reset($story);
	// 			$query = DB::update($story);
	// 			$query -> value('photo', $newPhoto);
	// 			$query -> execute();
	// 			$json = $this->response(array(
	// 		                    'code' => 200,
	// 		                    'message' => 'Foto modificada correctamente',
	// 		                    'token' => $token
	// 		                    ));
	// 		                return $json;
	// 		}else{
	// 			$json = $this->response(array(
	// 		                    'code' => 400,
	// 		                    'message' => 'Campos vacios',
	// 		                    'data' => ""
	// 		                    ));
	// 		                return $json;
	// 		}
	// 	}else{
	// 		$json = $this->response(array(
	// 		                    'code' => 400,
	// 		                    'message' => 'Foto vacia, por favor rellenela',
	// 		                    'data' => ""
	// 		                    ));
	// 		                return $json;
	// 	}
	// 	$newComment = $_POST['newComment'];
	// 	if( isset($newComment)) {
	// 		$decodeToken = $this->decodeToken();
	// 		$story = Model_stories::find('all', 
	// 		            					array('where' => array(
	// 		            							array('id_story', '=', $decodeToken->id_story), 
	// 		            							array('comment', '=', $decodeToken->comment)
	// 		            							)
	// 		            						)
	// 		            					);
	// 		if(isset($newComment)){
	// 			$story = reset($story);
	// 			$query = DB::update($story);
	// 			$query -> value('comment', $newComment);
	// 			$query -> execute();
	// 			$json = $this->response(array(
	// 		                    'code' => 200,
	// 		                    'message' => 'Comentario modificado correctamente',
	// 		                    'token' => $token
	// 		                    ));
	// 		                return $json;
	// 		}else{
	// 			$json = $this->response(array(
	// 		                    'code' => 400,
	// 		                    'message' => 'Campos vacios',
	// 		                    'data' => ""
	// 		                    ));
	// 		                return $json;
	// 		}
	// 	}else{
	// 		$json = $this->response(array(
	// 		                    'code' => 400,
	// 		                    'message' => 'Comentario vacio, por favor rellenelo',
	// 		                    'data' => ""
	// 		                    ));
	// 		                return $json;
	// 	}

	// }
	}

