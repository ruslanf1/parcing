<?php

define("SERVER", "localhost");
define("DB", "apmarev_ruslan");
define("PASSWORD", "root");
define("USER", "root");

function db_connect()
{
    $db = new mysqli(SERVER, USER, PASSWORD, DB);
    $db->set_charset("utf8");
    if (!$db) {
        return false;
    }
    return $db;
}
