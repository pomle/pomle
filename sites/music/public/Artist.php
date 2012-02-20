<?
require '../Init.inc.php';

if( !$Artist = \Music\Artist::loadFromDB($_GET['artistID']) )
	die(MESSAGE_ARTIST_MISSING);

require HEADER;

print_r($Artist);

require FOOTER;
