<?php
/**
 * Copyright (C) 2013 Antoine Jackson
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 */
use Doctrine\Common\ClassLoader,
    Doctrine\ORM\Configuration,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\Cache\ApcCache;

try {

    $plugins_array = MuffinApplication::getPlugins();
    require_once ROOT . DS . "Lib" . DS . "Vendors" . DS . "vendor" . DS . "doctrine-common" . DS . "lib" . DS . "Doctrine" . DS . "Common" . DS . "ClassLoader.php";
    $location = ROOT . DS . "Lib/Vendors/";
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', $location);
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', $location . 'vendor/doctrine-dbal/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', $location . '/vendor/doctrine-common/lib');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader('Symfony', $location . '/vendor');
    $classLoader->register();
    $classLoader = new \Doctrine\Common\ClassLoader(null, ROOT . DS . 'App' . DS . "Models");
    $classLoader->register();

    foreach ($plugins_array as $plugin) {
        $classLoader = new \Doctrine\Common\ClassLoader($plugin, ROOT.DS."Plugins".DS.$plugin.DS."App".DS."Models");
        $classLoader->register();
    }


    $classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
    $classLoader->register();
    require_once(ROOT . DS . "Config" . DS . "database.php");
// Set up caches
    $config = new Configuration;

    $entity_driver_folders = array(ROOT . DS . "App".DS."Models");



    foreach ($plugins_array as $plugin) {
        $entity_driver_folders[] = ROOT.DS."Plugins".DS.$plugin.DS."App".DS."Models";
    }
    $driverImpl = $config->newDefaultAnnotationDriver($entity_driver_folders);
    $config->setMetadataDriverImpl($driverImpl);

// Proxy configuration
    $config->setProxyDir(ROOT . DS . "Tmp" . DS . 'Proxies');
    $config->setProxyNamespace('Proxies');

    if (!defined("CACHE_PREFIX")) {
        define("CACHE_PREFIX", "your_app_name");
    }

    if (extension_loaded("apc")) {
        $cache = new ApcCache();
        $cache->setNamespace(CACHE_PREFIX);
        $config->setQueryCacheImpl($cache);
        $config->setMetadataCacheImpl($cache);
    }


// Database connection information
    $dbc = new \DbConfig();
    if (ENV == 0) {
        $dbinfos = $dbc->dev;
    } else if (ENV == 1) {
        $dbinfos = $dbc->test;
    } else {
        $dbinfos = $dbc->prod;
    }
    $connectionOptions = array(
        'driver' => 'pdo_mysql',
        "dbname" => $dbinfos["database"],
        "user" => $dbinfos["user"],
        "host" => $dbinfos["host"],
        "password" => $dbinfos["password"],
        "charset" => "utf8"
    );
// Create EntityManager
    $em = EntityManager::create($connectionOptions, $config);
    $GLOBALS["em"] = EntityManager::create($connectionOptions, $config);
} catch (Exception $e) {
    echo $e->getMessage();
}