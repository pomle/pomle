<?
require __DIR__ . '/../Init.inc.php';

require DIR_ASENINE_INCLUDE . 'Functions.Site.inc.php';

define('DIR_SYSTEM', DIR_ROOT . 'system/');

addIncludePath(DIR_SYSTEM . 'class/');


### POMLE BRANCH ADDITIONS ###
define('DIR_INCLUDE', DIR_SYSTEM . 'include/');

require DIR_INCLUDE . 'Functions.inc.php';

define('POST_TYPE_ALBUM', 'album');
define('POST_TYPE_DIARY', 'diary');
define('POST_TYPE_TRACK', 'track');
