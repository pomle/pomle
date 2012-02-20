<?
require '../../../Init.inc.php';

$mediaURL = \Media\Producer\Blog::createFromHash($_GET['mediaHash'])->getPagePreview();

if( !$mediaURL )
{
	header('HTTP/1.0 404 Not Found');
	exit("You're missing something");
}

header("Location: ". $mediaURL);