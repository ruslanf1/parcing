<?php
require_once('db_config.php');

$html = file_get_contents('rbk.html');
$trash = ['&laquo;', '&raquo;', '&ndash;', '&quot;', '&nbsp;'];

// Парсинг ссылок

    preg_match_all('/<a href="(.*)" class="search-item__link">/im', $html, $matchesUrl);
    unset($matchesUrl[0]);
    $urlResult = array_shift($matchesUrl);

// Парсинг заголовков

    preg_match_all('/<span class="search-item__title">(.*)/im', $html, $matchesTitle);
    $outputTitle = array_shift($matchesTitle);
    $titleResult = [];
    foreach ($outputTitle as $value) {
        $titleResult[] = strip_tags(str_replace($trash, "", $value));
    }

// Создаем массив Заголовки => Ссылки

$output = array_combine($titleResult, $urlResult);

// Парсинг полных текстов

foreach ($output as $index => $value) {
    $htmlText = file_get_contents($value);
    preg_match_all('/(.*)/', $htmlText, $matchesText);
    $isOpenNews = false;
    $result = '';
    foreach ($matchesText[0] as $text) {
        if (substr_count($text, 'article__header__title') == 1) {
            $isOpenNews = true;
        }
        if ($isOpenNews == true) {
            $result .= $text;
        }
        if (substr_count($text, 'article__authors') == 1) {
            break;
        }
    }
    $textResult = strip_tags(str_replace($trash, "", $result));

// Проверка на уникальность, сохранение в БД

    $title = mb_strtolower(mb_eregi_replace("[^a-zа-яё0-9]", '', $index));
    $art_id = md5($title . $value);

    $db = db_connect();
    $exist = "SELECT `art_id` FROM `ruslan` WHERE `art_id` = '".$art_id."'";
    $count = mysqli_num_rows($db->query($exist));
    if ($count>0) {
        break;
    } else {
        $sql = "INSERT INTO ruslan (`title`, `url`, `art_id`, `text`) VALUES ('".$index."', '".$value."', '".$art_id."','".$textResult."')";
        $db->query($sql);
        $db->close();
    }
}
