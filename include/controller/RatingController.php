<?php
namespace controller;


global $cfg;


class RatingController
{

    public function defaultAction($request_path)
    {        

        $file =  RATING_ASSET_DIR.$request_path;
        //echo $file;
        if (file_exists($file)) {

            if ($this->endsWith($file, '.js')) {
                header('Content-type: text/javascript');
                echo file_get_contents($file);
            } else if ($this->endsWith($file, '.css')) {
                header('Content-type: text/css');
                echo file_get_contents($file);
            } else if ($this->endsWith($file, '.png')) {
                header('Content-type: image/png');
                echo file_get_contents($file);
            }else if($this->endsWith($file, '.php')){
                require_once($file);
            }
        }
         else {
            echo 'File does not exist';
        }
    }

    function ratingPage() {
        require_once RATING_ASSET_DIR.'table.php';
    }

    public function redirectAction($request_path) {
        header("Location: " . OST_WEB_ROOT . $request_path);
        die();
    }

    public function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public static function access(){
       
        return true;
    }
}


?>