<?
require __DIR__ . '/Settings.inc.php';
require __DIR__ . '/../Init.inc.php';

define('DIR_SITE', DIR_SITES . 'blog/');

define('DIR_SITE_SYSTEM', DIR_SITE . 'system/');
define('DIR_SITE_CLASS', DIR_SITE_SYSTEM . 'class/');
define('DIR_ELEMENT', DIR_SITE_SYSTEM . 'element/');

define('HEADER', DIR_ELEMENT . 'Header.inc.php');
define('FOOTER', DIR_ELEMENT . 'Footer.inc.php');

addIncludePath(DIR_SITE_CLASS);

$css = array();
$css[] = '/css/clearfix.css';
$css[] = '/css/style.css';

$js = array();
$js[] = '//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js';