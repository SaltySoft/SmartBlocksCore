<?php
require_once("../config.php");
require_once(ROOT . DS . "Lib/shared.php");
require_once ROOT.DS."Lib".DS."Vendors".DS."vendor".DS."doctrine-common".DS."lib".DS."Doctrine".DS."Common".DS."ClassLoader.php";

$location = "../../Lib/Vendors/";
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\ORM', ROOT.DS."Lib".DS."Vendors");
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\DBAL', $location.'vendor/doctrine-dbal/lib');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Doctrine\Common', $location.'/vendor/doctrine-common/lib');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader('Symfony',ROOT.DS.'Lib'.DS.'Vendors'.DS.'vendor');
$classLoader->register();
$classLoader = new \Doctrine\Common\ClassLoader(null, ROOT.DS.'App'.DS."Models");
$classLoader->register();

$plugins_array = MuffinApplication::getPlugins();
foreach ($plugins_array as $plugin) {
    $classLoader = new \Doctrine\Common\ClassLoader($plugin, ROOT.DS."Plugins".DS.$plugin.DS."App".DS."Models");
    $classLoader->register();
}
$classLoader = new \Doctrine\Common\ClassLoader('Proxies', __DIR__);
$classLoader->register();
require_once(".." . DS . "database.php");

$config = new \Doctrine\ORM\Configuration();
$config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache);
//$driverImpl = new \Doctrine\ORM\Mapping\Driver\YamlDriver(ROOT.DS."App".DS."Drivers");

$entity_driver_folders = array(ROOT . DS . "App".DS."Models");



foreach ($plugins_array as $plugin) {
    $entity_driver_folders[] = ROOT.DS."Plugins".DS.$plugin.DS."App".DS."Models";
}

$driverImpl = $config->newDefaultAnnotationDriver($entity_driver_folders);
$config->setMetadataDriverImpl($driverImpl);

$config->setProxyDir(__DIR__ . '../../Tmp/Proxies');
$config->setProxyNamespace('Proxies');

// Database connection information

$dbc = new \DbConfig();
if (ENV == 0)
{
    $dbinfos = $dbc->dev;
}
else if (ENV == 1)
{
    $dbinfos = $dbc->test;
}
else
{
    $dbinfos = $dbc->prod;
}
$connectionOptions = array(
    'driver' => 'pdo_mysql',
    "dbname" => $dbinfos["database"],
    "user" => $dbinfos["user"],
    "host" => $dbinfos["host"],
    "password" => $dbinfos["password"]
);

$em = \Doctrine\ORM\EntityManager::create($connectionOptions, $config);

$helpers = array(
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
    'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
);