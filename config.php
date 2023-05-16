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
            'error_staff_url' => new TextboxField(array(
                'label' => __('Error page for staff vote'),
                'required' => true,
                'configuration' => array('size'=>40),
                'hint' => __('Error URL for the internal page if staff'),
            )),
            'error_no_user_url' => new TextboxField(array(
                'label' => __('Error page for no user vote'),
                'required' => true,
                'configuration' => array('size'=>40),
                'hint' => __('Error URL for the internal page if no user'),
            )),
            'generic_error_url' => new TextboxField(array(
                'label' => __('For generic error'),
                'required' => true,
                'configuration' => array('size'=>40),
                'hint' => __('Error URL for generic error'),
            ))

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