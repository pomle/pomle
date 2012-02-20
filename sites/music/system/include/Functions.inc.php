<?
function getUserTrackItemImageURL($mediaHash)
{
	return \Media\Producer\CrossSite::createFromHash($mediaHash)->getCustom(100, 100, true);
}