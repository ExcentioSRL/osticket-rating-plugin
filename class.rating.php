<?php
require_once INCLUDE_DIR . 'class.export.php';

class Rating {
    static $id = "auth.agent";
    static $name = "Rating";

    static $desc = 'Rating';

    protected function getSetupOptions() {
        global $thisstaff;
        return array(
            '' =>  array(
                'uploadpath' =>  new ChoiceField(array(
                    'label' => $__('Rating'),
                    'default' => "enabled",
                    'choices' => array(
                        '-' => $__('-'),
                        '1' => $__('1'),
                        '2' => $__('2'),
                        '3' => $__('3'),
                    ),
                )),
    
               
            ),
        );
    }


}
