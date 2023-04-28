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


define ( 'OST_WEB_ROOT', osTicket::get_root_path ( __DIR__ ) );
const RATINGS_WEB_ROOT = OST_WEB_ROOT . 'scp/dispatcher.php/rating/';
const RATING_PLUGIN_ROOT = __DIR__ . '/';
const RATING_ASSET_DIR = RATING_PLUGIN_ROOT. 'assets/';
const RATING_TABLE = TABLE_PREFIX . 'ost_ratings';
const RATING_MODEL_DIR = INVENTORY_INCLUDE_DIR . 'model/';

spl_autoload_register(array(
    'RatingPlugin',
    'autoload'
));



class RatingPlugin extends Plugin
{

    var $config_class = 'RatingPluginConfig';

    static $result_url;

    public static function autoload($className) {
        $className = ltrim ( $className, '\\' );
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos ( $className, '\\' )) {
            $namespace = substr ( $className, 0, $lastNsPos );
            $className = substr ( $className, $lastNsPos + 1 );
            $fileName = str_replace ( '\\', DIRECTORY_SEPARATOR, $namespace ) . DIRECTORY_SEPARATOR;
        }
        $fileName .= str_replace ( '_', DIRECTORY_SEPARATOR, $className ) . '.php';
        $fileName = 'include/' . $fileName;
        if (file_exists ( RATING_PLUGIN_ROOT . $fileName )) {
            require $fileName;
        }

    }

    function bootstrap()
    {
        $config = $this->getConfig();
        if ($config->get('result_url'))
            RatingPlugin::$result_url = $config->get('result_url');

        if($this->firstRun()) {
            if(!$this->configureFirstRun()) {
                return false;
            }
            createStaffMenu();
        }
       
   
        Signal::connect( 'apps.scp', array(
            'RatingPlugin',
            'callbackDispatch'
        ));
    }

    static public function callbackDispatch($object, $data) {
        $page_url = url ( '^/rating/',
            patterns ( 'controller\RatingController',
                url ( '^(?P<url>.*)$', 'defaultAction' ),
            )
        );

        $object->append ($page_url);
    
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
        return true;
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


function createStaffMenu() {
    $app = new Application();
    $app->registerStaffApp('Ratings', RATINGS_WEB_ROOT."table.php");
}

function firstRun() {
    $sql = 'SHOW TABLES LIKE \'' . RATING_TABLE . '\'';
    $res = db_query($sql);
    return (db_num_rows($res) == 0);
}

function configureFirstRun() {
    if(!createDBTables()) {
        echo "First run configuration error. " . "Unable to create database tables!";
        return false;
    }
    return true;
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
    else
        return true;
}



function initResultFile()
{
}
