<?
require '_Debug.php';

$query = "SELECT ID FROM Music_Tracks";
$trackIDs = \DB::queryAndFetchArray($query);

$tracks = \Music\Track::loadFromDB($trackIDs);

print_r($tracks);