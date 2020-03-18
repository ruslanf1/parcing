<?php
require_once 'vendor/autoload.php';
require_once 'vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem($_SERVER["DOCUMENT_ROOT"].'/templates');
$twig = new Twig_Environment($loader);

function db_connect()
{
    $db = new mysqli(localhost, root, root, db);
    $db->set_charset("utf8");
    if (!$db) {
        return false;
    }
    return $db;
}