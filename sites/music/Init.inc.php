<?
require __DIR__ . '/Settings.inc.php';
require __DIR__ . '/../Init.inc.php';

define('DIR_SITE', DIR_SITES . 'music/');

define('DIR_SITE_SYSTEM', DIR_SITE . 'system/');
define('DIR_SITE_CLASS', DIR_SITE_SYSTEM . 'class/');
define('DIR_SITE_INCLUDE', DIR_SITE_SYSTEM . 'include/');
define('DIR_SITE_RESOURCE', DIR_SITE_SYSTEM . 'resource/');

define('DIR_ELEMENT', DIR_SITE_SYSTEM . 'element/');
define('DIR_ELEMENT_PANEL', DIR_ELEMENT . 'panel/');

define('HEADER', DIR_ELEMENT . 'Header.inc.php');
define('FOOTER', DIR_ELEMENT . 'Footer.inc.php');

require DIR_SITE_INCLUDE . 'Functions.inc.php';

addIncludePath(DIR_SITE_CLASS);

session_start();

if( !isset($_SESSION['User']) || !$_SESSION['User'] instanceof \User || isset($_POST['login']) )
{
	$username = isset($_REQUEST['username']) ? $_REQUEST['username'] : null;
	$password = isset($_REQUEST['password']) ? $_REQUEST['password'] : null;
	$authtoken = isset($_COOKIE['authtoken']) ? $_COOKIE['authtoken'] : null;

	$_SESSION['User'] = \User::login($username, $password, $authtoken) ?: new \User();
}
$User = $_SESSION['User'];


$pageTitle = 'Cordless';

$css = array();
$css[] = '/css/Shitfest.css';
$css[] = '/css/Interface.css';
$css[] = '/css/Cordless.css';
$css[] = '/css/Playlist.css';
$css[] = '/css/Tracklist.css';

$js = array();
$js[] = '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';
$js[] = '/js/jquery/jquery.dropUpload.js';


$js[] = '/js/CordlessController.js';
$js[] = '/js/PlaylistController.js';
$js[] = '/js/PanelController.js';

$js[] = '/js/Interface.js';
$js[] = '/js/Main.js';
$js[] = '/js/Upload.js';

#$js[] = '/js/lib/fxb-last.fm-api/lastfm.api.md5.js';
#$js[] = '/js/lib/fxb-last.fm-api/lastfm.api.js';

header("Content-type: text/html; charset=utf-8");

if( !defined('NO_LOGIN') && $User->isLoggedIn() !== true )
	require DIR_ELEMENT . 'Login.inc.php';