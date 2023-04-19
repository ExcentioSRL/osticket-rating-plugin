<?php

class RatingPluginConfig extends PluginConfig {
    function getOptions() {
        return array();
    }


    function pre_save(&$config, &$errors) {
        global $msg;

        if($config['equipment_frontend_enable'] == 1){
                
                $errors['err'] = 'Validation failed, invalid setting: "Equipment Frontent"'; 
                return FALSE;
        }

        if (!$errors) {
            $msg = 'Configuration updated successfully'; 
        }

        return true;
     }


}