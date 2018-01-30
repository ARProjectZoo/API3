<?php 

class Controller_Images extends Controller_Rest
{
    public function post_upload()
    {
        
        // Custom configuration for this upload
        $config = array(
            'path' => DOCROOT . 'assets/img',
            'randomize' => true,
            'ext_whitelist' => array('img', 'jpg', 'jpeg', 'gif', 'png'),
        );
        // process the uploaded files in $_FILES
        Upload::process($config);
        // if there are any valid files
        if (Upload::is_valid())
        {
            // save them according to the config
            Upload::save();
            foreach(Upload::get_files() as $file)
            {
                $image = new Model_Images();
                $image->title = $file;
                $image->save();
            }
        }
        // and process any errors
        foreach (Upload::get_errors() as $file)
        {
            return $this->response(array(
                'code' => 500,
            ));
        }
        return $this->response(array(
            'code' => 200,
        ));
    }
}