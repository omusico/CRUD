<?php
/**
 * must define $_metadata and the ctor !!!
 *//**
 * Class Name
 *
 * @category  Crud class
 * @package   Crud
 * @author    elvis ciotti <elvis@phpntips.com>
 * @copyright 2010 Phpntips.com
 * @license   http://framework.zend.com/license/new-bsd  New BSD License
 * @version   Release: 1.0
 * @link      http://www.phpntips.com/crud
 */
class Crud_Helpers_Cache
{
    /**
     * @var boolean
     */
    static protected $_enabled = false;

    /**
     * @var Zend_Cache_Core
     */
    static protected $_cache;

    static function init($enabled, $lifetime=7200)
    {

        $optionsAll = Zend_Registry::get('config');
        $options = $optionsAll->app->cache;

        //check if cache directory exists
        //$dirExists = file_exists($options->backEndOptions->cache_dir);
        $dirExists = is_writable($options->backEndOptions->cache_dir);

        if ($enabled && !$dirExists) {
            $logger = Zend_Registry::get('log');
            $logger->INFO('cache folder missing or not writable');
        }

        //if dir does not exist set to false
        self::$_enabled = $enabled && $dirExists;

        if (self::$_enabled) {
            include_once 'Zend/Cache.php';

            self::$_cache = $cache = Zend_Cache::factory(
                $options->frontEnd,
                $options->backEnd,
                $options->frontEndOptions->toArray(),
                $options->backEndOptions->toArray()
            );
            return self::$_cache;
        }
    }

    static function getInstance()
    {
        if (self::$_enabled == false) {
            return false;
        }
        return self::$_cache;
    }

    static function load($keyName)
    {
        if (self::$_enabled == false) {
            return false;
        }
        return self::$_cache->load($keyName);
    }

    static function save($keyName, $dataToStore)
    {
        if (self::$_enabled == false) {
            return true;
        }

        return self::$_cache->save($keyName, $dataToStore);
    }

    static function test($keyName)
    {
        if (self::$_enabled == false) {
            return false;
        }
        return self::$_cache->test($keyName);
    }

    static function clean()
    {
        if (self::$_enabled == false) {
            return;
        }
        self::$_cache->clean();
    }
}

