<?
require __DIR__ . '/Settings.inc.php';
require __DIR__ . '/../Init.inc.php';

define('DIR_SITE', DIR_SITES . 'blog/');

define('DIR_SITE_SYSTEM', DIR_SITE . 'system/');
define('DIR_SITE_CLASS', DIR_SITE_SYSTEM . 'class/');
define('DIR_SITE_INCLUDE', DIR_SITE_SYSTEM . 'include/');
define('DIR_SITE_RESOURCE', DIR_SITE_SYSTEM . 'resource/');

define('DIR_ELEMENT', DIR_SITE_SYSTEM . 'element/');

define('HEADER', DIR_ELEMENT . 'Header.inc.php');
define('FOOTER', DIR_ELEMENT . 'Footer.inc.php');

require DIR_SITE_INCLUDE . 'Functions.inc.php';

addIncludePath(DIR_SITE_CLASS);

setlocale(LC_ALL, 'sv_SE.UTF8');

session_start();

if( !isset($_SESSION['UserSettings']) )
	$_SESSION['UserSettings'] = SiteSettings::createDefault();

$UserSettings = $_SESSION['UserSettings'];


$page = max(1, (int)$_GET['page']);
$pageIndex = $page - 1;


$rssHref = 'Pomle.com RSS Feed';
$rssHref = '/index.php?showAs=rss';

$css = array();
$css[] = '/css/Shitfest.css';
$css[] = '/css/Pomle.css';

$js = array();
$js[] = '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';