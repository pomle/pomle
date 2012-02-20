<?
$Fetch = new \Fetch\UserTrack($User);

$userTracks = $Fetch->getRecent();

$Playlist = \Element\Playlist::createFromUserTracks($userTracks);

echo $Playlist;