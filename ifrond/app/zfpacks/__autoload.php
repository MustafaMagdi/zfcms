<?php
/**
 * __autoload() function with caching
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 * See http://www.gnu.org/copyleft/lesser.html
 *
 * @copyright  2008 Anton Makarenko
 * @license    http://www.gnu.org/copyleft/lesser.html LGPL
 * @version    0.2 $Id:$
 * @link       http://anton.makarenko.name/
 * @since      0.1
 *
 */

/**
 * Load classes automatically and assemble them in one cache file (optional)
 *
 * Usage without caching:
 * 1) just include this function to your script
 *
 * It is possible to define __AUTOLOAD_INCLUDE constant,
 * to use include instead of require
 *
 * Usage with caching:
 * 1) define constant __AUTOLOAD_CACHE_DIR - directory to save autoloaded cache
 * 2) define __AUTOLOAD_MUTEX_FILE - file, that will serve as cache on/off flag
 * 3) include this function to your script
 * 4) register_shutdown_function('__autoload');
 *
 * Classes names and locations you wish to autoload,
 * must inviolately conform Zend Framework coding standard.
 * See Appendix B.3.1. of ZF manual: http://framework.zend.com/manual/
 *
 * It is *your* task to setup include path,
 * so files `require` priority and locations will be defined by *you*.
 *
 * __AUTOLOAD_CACHE_DIR will be created automatically, if not exists
 * If the __AUTOLOAD_MUTEX_FILE not exist, then caching will be disabled
 * If the __AUTOLOAD_MUTEX_FILE was modified (last modified time changed),
 * then cache will be reloaded
 *
 * __AUTOLOAD_DIR_MODE and __AUTOLOAD_FILE_MODE - modes for chmod() - will be
 * applied when creating cache dir and file.
 * By default, they are 0755 and 0644 respectively
 *
 * In a few words, when caching is turned on, next things happen on every launch:
 * 1) Autoload dir will be initiated and checked, is it writable (once)
 * 2) Cache file will be initiated (once)
 * 3) Cache file will be required (once)
 * 4) Every not found class will be required by autoload
 *    and registered in a static array (N times for N not found classes)
 * 5) Just before ending, new files will be appended to cache (once, if applicable)
 *
 * @param string $class_name
 */
function __autoload($className = null)
{
    static $isCache     = -1;
    static $cacheDir    = false;
    static $cacheFile   = false;
    static $loadedFiles = array();

    // determine, if cache is enabled
    if (-1 === $isCache) {
        $isCache = (
            defined('__AUTOLOAD_CACHE_DIR')
            && defined('__AUTOLOAD_MUTEX_FILE')
            && file_exists(__AUTOLOAD_MUTEX_FILE)
        );
    }

    if ($isCache) {
        // lookup/create cache directory
        if (false === $cacheDir) {
            clearstatcache();
            $mode = defined('__AUTOLOAD_DIR_MODE') ? __AUTOLOAD_DIR_MODE : 0755;
            $cacheDir = __AUTOLOAD_CACHE_DIR;
            if ((!is_dir($cacheDir))) {
                if (!mkdir($cacheDir, $mode, true)) {
                    trigger_error('Failed to create directory ' . $cacheDir, E_USER_ERROR);
                }
            }
            elseif (!is_writable($cacheDir)) {
                trigger_error('Directory ' . $cacheDir . ' is not writable.', E_USER_ERROR);
            }

            // touch cache file
            $cacheFile = $cacheDir . '/__autoload_' . filemtime(__AUTOLOAD_MUTEX_FILE) . '.php';
            $mode = defined('__AUTOLOAD_FILE_MODE') ? __AUTOLOAD_FILE_MODE : 0644;
            if (!file_exists($cacheFile)) {
                if ((!touch($cacheFile))
                    || (!chmod($cacheFile, $mode))
                    || @(!file_put_contents($cacheFile, '<?php' . "\n"))
                    ) {
                    trigger_error('Failed to initialize file ' . $cacheFile, E_USER_ERROR);
                }
            }
            elseif (!is_writable($cacheFile)) {
                trigger_error('File ' . $cacheFile . ' is not writable.', E_USER_ERROR);
            }
            $cacheFile = realpath($cacheFile);

            // load the cache file
            require $cacheFile;

            // return happy, if our class already loaded
            if (class_exists($className, false)) {
                return;
            }
        }

        // save cache, if we are called in shutdown function
        if (empty($className) && (!empty($loadedFiles)) && (false !== $cacheFile)) {
            // load new files contents into memory
            $contents = array(-1 => "\n\n// autoloaded at " . time() . "\n\n");
            foreach ($loadedFiles as $key => $file) {
            	
            	$paths = explode(PATH_SEPARATOR, get_include_path());
                foreach ($paths as $path) {
                        if (substr($path, -1) == DIRECTORY_SEPARATOR) {
                                $fullpath = $path.$file;
                        } else {
                                $fullpath = $path.DIRECTORY_SEPARATOR.$file;
                        }
                        if (file_exists($fullpath)) {
                                $file = $fullpath;
                        }
                } // получаем настоящий путь
            	
                $contents[$key] = @trim(file_get_contents($file, true));
                if (empty($contents[$key])) {
                    trigger_error('Failed to load contents from file ' . $file, E_USER_ERROR);
                }
                // cut opening and closing php-tags
                $contents[$key] = substr($contents[$key], 5);
                if ('?>' === substr($contents[$key], -2)) {
                    $contents[$key] = substr_replace($contents[$key], "\n", -2);
                }
            }

            // append to cache file
            if (!@file_put_contents($cacheFile, $contents, FILE_APPEND)) {
                trigger_error('Failed to put contents to file ' . $cacheFile, E_USER_ERROR);
            }

            // die happy
            return;
        }
    }

    // require the class file
    if (!empty($className)) {
        $path = explode('_', $className);
        $filename = array_pop($path);
        $file = (empty($path) ? '' : implode('/', $path) . '/') . $filename . '.php';
        
        if (!@include($file)) return;
        /*
        if (!defined('__AUTOLOAD_INCLUDE')) {
            require $file;
        }
        else {
            @include $file;
        }
		*/
        // update loaded files list
        if ($isCache) {
            $loadedFiles[] = $file;
        }
    }
}
