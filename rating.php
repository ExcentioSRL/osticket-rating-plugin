<?php

/**
 * Rating plugin
 * 
 */
require_once INCLUDE_DIR . 'class.plugin.php';
require_once INCLUDE_DIR . 'class.forms.php';
require_once INCLUDE_DIR . 'class.ajax.php';
require_once('config.php');
require_once INCLUDE_DIR . 'class.signal.php';

require_once(INCLUDE_DIR . 'class.app.php');
require_once(INCLUDE_DIR . 'class.dispatcher.php');
require_once(INCLUDE_DIR . 'class.osticket.php');
require_once(INCLUDE_DIR . 'class.import.php');




define('OST_WEB_ROOT', osTicket::get_root_path(__DIR__));
const RATINGS_WEB_ROOT = OST_WEB_ROOT . 'scp/dispatcher.php/rating/';
const RATING_PLUGIN_ROOT = __DIR__ . '/';
const RATING_ASSET_DIR = RATING_PLUGIN_ROOT . 'assets/';
const RATING_TABLE = TABLE_PREFIX . 'ost_ratings';
const RATING_MODEL_DIR = RATING_PLUGIN_ROOT . 'model/';

spl_autoload_register(array(
    'RatingPlugin',
    'autoload'
));



class RatingPlugin extends Plugin
{
    
    var $config_class = 'RatingPluginConfig';

    static $result_url;
    static $error_staff_url;
    static $error_no_user_url;
    static $generic_error_url;
    static $custom_form;

    public static function autoload($className)
    {
        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';
        $fileName = 'include/' . $fileName;
        if (file_exists(RATING_PLUGIN_ROOT . $fileName)) {
            require $fileName;
        }
    }

    function bootstrap()
    {
        $config = $this->getConfig();
        if ($config->get('result_url'))
            RatingPlugin::$result_url = $config->get('result_url');
        if ($config->get('error_staff_url'))
            RatingPlugin::$error_staff_url = $config->get('error_staff_url');
        if ($config->get('error_no_user_url'))
            RatingPlugin::$error_no_user_url = $config->get('error_no_user_url');
        if ($config->get('generic_error_url'))
            RatingPlugin::$generic_error_url = $config->get('generic_error_url');
        if ($config->get('custom_form'))
            RatingPlugin::$custom_form = $config->get('custom_form');

        if($this->firstRun()) {
            if(!$this->configureFirstRun()) {
                return false;
            }
        } else {
            updateDBTable();
        }

        createStaffMenu();
        Signal::connect('apps.scp', array(
            'RatingPlugin',
            'callbackDispatch'
        ));

        Signal::connect('object.created', array(
            'RatingPlugin',
            'modifyResponse'
        ));
    }

    static public function callbackDispatch($object, $data) {
        $page_url = url ( '^/rating/',
            patterns ( 'controller\RatingController',
                url ( '^(?P<url>.*)$', 'defaultAction' ),
            )
        );

        $object->append($page_url);
    }

    static public function modifyResponse($object, $data)
    {

        if (is_a($object, "Ticket")) {
            $lastCreated = $object->ht["thread"]->ht["lastresponse"];
            $threadId = $object->ht["thread"]->ht["id"];

            $ticketId = $object->ht["ticket_id"];

            $staff_id = $object->ht["staff_id"];
            $topic_id = $object->ht["topic_id"];
            $number = $object->ht["number"];

            $lastMsgQuery = "SELECT *
                FROM `ost_thread_entry` 
                WHERE `created`=\"" . $lastCreated . "\" AND `thread_id` = " . $threadId;

            $lastMesgRes = db_query($lastMsgQuery);
            $lastMsg = db_assoc_array($lastMesgRes)[0]["body"];

            if (str_contains($lastMsg, '%{feedback.form}')) {

                $formatForm = str_replace('"', "'", RatingPlugin::$custom_form);
                $lastMsg = str_replace('"', "'", $lastMsg);
                $newBody = str_replace('%{feedback.form}', $formatForm, $lastMsg);
                $newBody = str_replace('display:none', 'display:block', $newBody);
                $newBody = str_replace('"display :none', 'display:block', $newBody);

                $newBody = str_replace('%{ticket.id}', $ticketId, $newBody);
                $newBody = str_replace('%{ticket.number}', $number, $newBody);
                $newBody = str_replace('%{ticket.topic}', $topic_id, $newBody);
                $newBody = str_replace('%{ticket.staff}', $staff_id, $newBody);

                $query = "UPDATE `ost_thread_entry` 
                SET `body` = \"" . $newBody . "\"
                WHERE `created`=\"" . $lastCreated . "\" AND `thread_id` = " . $threadId;

                db_query($query);
            }
        }
    }

    function enable()
    {
        createDBTables();
        return parent::enable();
    }

    function uninstall(&$errors)
    {
        $errors = array();
        echo '<script>alert("Uninstall")</script>';

        self::disable();

        return parent::uninstall($errors);
    }

    function disable()
    {
        echo '<script>alert("Disabled")</script>';

        //return parent::disable();
    }


    /**
     * Checks if this is the first run of our plugin.
     *
     * @return boolean
     */
    function firstRun()
    {
        $sql = 'SHOW TABLES LIKE \'ost_ratings\'';
        $res = db_query($sql);
        return (db_num_rows($res) == 0);
    }

    function needUpgrade()
    {
        return false;
    }

    function configureUpgrade()
    {
    }

    /**
     * Necessary functionality to configure first run of the application
     */
    function configureFirstRun()
    {
        if (!createDBTables()) {
            echo "First run configuration error. " . "Unable to create database tables!";
            return false;
        }
        return true;
    }

    /**
     * Kicks off database installation scripts
     *
     * @return boolean
     */


    function deleteDBTables()
    {
        $query = "DROP TABLE IF EXISTS ost_ratings";

        if (!db_query($query))
            return false;
        else
            return true;
    }


    /**
     * Uninstall hook.
     *
     * @param type $errors        	
     * @return boolean
     */
    function pre_uninstall(&$errors)
    {
        echo '<script>alert("pre_uninstall")</script>';
    }
}


function createStaffMenu()
{
    $app = new Application();
    $app->registerStaffApp('Ratings', RATINGS_WEB_ROOT . "table.php");
}



function createDBTables()
{
    $query = "CREATE TABLE `ost_ratings` (
            `rating_id` int(11) PRIMARY KEY AUTO_INCREMENT,
            `rating` int(1) NOT NULL DEFAULT '0',
            `ticket_id` int(11) UNSIGNED NOT NULL,
            `topic_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
            `staff_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
            `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `user_id` varchar(16) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
            `user_ip` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
            `number` varchar(20) DEFAULT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    if (!db_query($query))
        return false;
    else{
        updateDBTable(); // da testare
        return true;
    }
}

function updateDBTable()
{

    $config = include 'db_config.php';

    if (json_encode($config) != "false") {

        $res = true;
        $int_config = " int(1) NOT NULL DEFAULT '0'";
        $string_config = "  varchar(20) COLLATE utf8_unicode_ci NOT NULL";
        foreach ($config as $dbItem) {

            if ( array_key_exists("name",$dbItem) && array_key_exists("type", $dbItem) && ( $dbItem["type"] == "int" || $dbItem["type"] == "string") ){
                $query = "ALTER TABLE `ost_ratings` ADD `". $dbItem["name"]."`".($dbItem["type"] == "string" ? $string_config : $int_config) ;
                $res = $res && db_query($query);
            } 
            else {
                $res = false;
            }
        }
        return $res;
    } else {
        return true;
    }

}


function initResultFile()
{
}
