<?
require '_Debug.inc.php';

$posts = \Album::loadFromDB(30155);

print_r($posts);