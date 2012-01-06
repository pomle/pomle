<?
require '../../Init.inc.php';

header('HTTP/1.1 301 Moved Permanently');

if( $Post = reset(\Post::loadAutoTypedFromDB(array($_GET['postID']))) )
{
	header('HTTP/1.1 301 Moved Permanently');
	header('Location: ' . $Post->getURL());
	exit();
}

header('HTTP/1.0 404 Not Found');
exit();