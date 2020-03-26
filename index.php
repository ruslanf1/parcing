<?php
require_once('include/include.php');

$db = db_connect();
$sql = "SELECT * FROM news";
$result = $db->query($sql);
$db->close();

$element = array();
for ($i = 0; $i < $result->num_rows; $i++) {
    $element[] = $result->fetch_assoc();
}

$arr = ['news' => $element];

echo $twig->render('index.html', $arr);

