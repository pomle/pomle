<?
require __DIR__ . '/../Init.inc.php';

define('DIR_SYSTEM', DIR_ROOT . 'system/');

addIncludePath(DIR_SYSTEM . 'class/');


### POMLE BRANCH ADDITIONS ###
define('POST_TYPE_DIARY', 'diary');
define('POST_TYPE_ALBUM', 'album');