<h1><? echo htmlspecialchars(_('Recent Additions')); ?></h1>
<?

try
{
	$Fetch = new \Fetch\UserTrack($User);

	$userTracks = $Fetch->getRecent();

	echo \Element\UserTrackList::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo \Element\Message::error($e->getMessage());
}