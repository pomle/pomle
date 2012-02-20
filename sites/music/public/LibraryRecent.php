<?
require '../Init.inc.php';

$Fetch = new \Fetch\UserTrack($User);
$userTracks = $Fetch->getRecent();

require HEADER;

$UserTrackList = new \Element\UserTrackList();
$UserTrackList->addUserTracks($userTracks);

echo $UserTrackList;

require FOOTER;
