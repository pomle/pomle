<?
require '../../Init.inc.php';

if( !isset($_GET['token']) ) die('Token missing');


$User->lastfm_token = $_GET['token'];


echo "Token Set";