<?php
require_once('db.php');

// Получение массива Имя => Ссылка
function getSources()
{
    $db = db_connect();
    $sqlName = "SELECT name FROM sources";
    $sqlUrl = "SELECT url FROM sources";
    $resultName = $db->query($sqlName);
    $resultUrl = $db->query($sqlUrl);
    $db->close();
    $name = [];
    for ($i = 0; $i < $resultName->num_rows; $i++) {
        $name = $resultName->fetch_assoc();
    }
    $url = [];
    for ($i = 0; $i < $resultUrl->num_rows; $i++) {
        $url = $resultUrl->fetch_assoc();
    }
    return array_combine($name, $url);
}

// Получение HTML
function getSourceHtml()
{
    $html = file_get_contents('include/rbc.html');
    return $html;
}

// Получение заголовков
function getContentTitle($html, $sourceName)
{
    $db = db_connect();
    $sql = "SELECT title_selector FROM sources WHERE `name` = '" . $sourceName . "'";
    $result = $db->query($sql);
    $resultDb = $result->fetch_assoc();
    $db->close();
    preg_match_all($resultDb['title_selector'], $html, $matchesTitle);
    $outputTitle = array_shift($matchesTitle);
    $trash = ['&laquo;', '&raquo;', '&ndash;', '&quot;', '&nbsp;'];
    $titleResult = [];
    foreach ($outputTitle as $value) {
        $titleResult[] = strip_tags(str_replace($trash, "", $value));
    }
    return $titleResult;
}

// Получение ссылок
function getContentUrl($html, $sourceName)
{
    $db = db_connect();
    $sql = "SELECT link_selector FROM sources WHERE `name` = '" . $sourceName . "'";
    $result = $db->query($sql);
    $resultDb = $result->fetch_assoc();
    $db->close();
    preg_match_all($resultDb['link_selector'], $html, $matchesUrl);
    unset($matchesUrl[0]);
    $urlResult = array_shift($matchesUrl);
    return $urlResult;
}

// Получение текстов
function getContentText($url, $sourceName)
{
    $db = db_connect();
    $sql = "SELECT content_selector_start, content_selector_end FROM sources WHERE `name` = '" . $sourceName . "'";
    $result = $db->query($sql);
    $resultDb = $result->fetch_assoc();
    $db->close();
    $htmlText = file_get_contents($url);
    preg_match_all('/(.*)/', $htmlText, $matchesText);
    $isOpenNews = false;
    $result = '';
    foreach ($matchesText[0] as $text) {
        if (substr_count($text, $resultDb['content_selector_start']) == 1) {
            $isOpenNews = true;
        }
        if ($isOpenNews == true) {
            $result .= $text;
        }
        if (substr_count($text, $resultDb['content_selector_end']) == 1) {
            break;
        }
    }
    $trash = ['&laquo;', '&raquo;', '&ndash;', '&quot;', '&nbsp;'];
    $textResult = strip_tags(str_replace($trash, "", $result));
    return $textResult;
}

// Задаем уникальный ID
function getNewsId($title, $url)
{
    $titleId = mb_strtolower(mb_eregi_replace("[^a-zа-яё0-9]", '', $title));
    $news_id = md5($titleId . $url);
    return $news_id;
}

// Проверяем на уникальность и сохраняем в БД
function getSourceItem($title, $url, $newsId, $sourceName, $text)
{
    $db = db_connect();
    $exist = "SELECT `news_id` FROM `news` WHERE `news_id` = '" . $newsId . "'";
    $count = mysqli_num_rows($db->query($exist));
    if ($count > 0) {
        exit;
    } else {
        $sql = "INSERT INTO news (`title`, `url`, `news_id`, `source_name`, `text`) VALUES ('" . $title . "', '" . $url . "', '" . $newsId . "','" . $sourceName . "','" . $text . "')";
        $db->query($sql);
        $db->close();
    }
}