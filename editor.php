<?php
require_once('include/include.php');

if (isset($_POST) && $_POST['id'] > 0) {
    $db = db_connect();
    $sql = "UPDATE ruslan SET title='".$_POST['title']."', url='".$_POST['url']."', status='".$_POST['status']."', text='".$_POST['text']."' WHERE id = '".$_POST['id']."'";
    $db->query($sql);
    $db->close();
    $id = $_POST['id'];
} elseif (isset($_GET) && $_GET['id'] != '') {
    $id = $_GET['id'];
    $id = $id + 0;
}
$db = db_connect();
$sql = "SELECT * FROM ruslan WHERE id = '".$id."'";
$result = $db->query($sql);
$db->close();
$item = $result->fetch_assoc();

$arr = ['element' => $item];

echo $twig->render('editor.html', $arr);



