<?php

class RatingPluginConfig extends PluginConfig {
    function getOptions() {
        return array(
            'result_url' => new TextboxField(array(
                'label' => __('Result Page'),
                'required' => true,
                'configuration' => array('size'=>40),
                'hint' => __('URL for the internal result page'),
            )),
        );
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