<?
require __DIR__ . '/../Init.inc.php';

define('DIR_SYSTEM', DIR_ROOT . 'system/');

addIncludePath(DIR_SYSTEM . 'class/');


### POMLE BRANCH ADDITIONS ###
define('DIR_INCLUDE', DIR_SYSTEM . 'include/');

require DIR_INCLUDE . 'Functions.inc.php';

define('POST_TYPE_ALBUM', 'album');
define('POST_TYPE_DIARY', 'diary');
define('POST_TYPE_TRACK', 'track');

define('LAST_FM_API_KEY', 'b934445a490b43b42fd02d2ae9407595');
define('LAST_FM_API_SECRET', 'aff70a07faee4e8590157256a9146b74');