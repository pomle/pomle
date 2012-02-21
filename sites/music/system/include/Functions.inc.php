<?
function getUserTrackItemImageURL($mediaHash)
{
	return \Media\Producer\CrossSite::createFromHash($mediaHash)->getCustom(100, 100, true);
}

function libraryLink($text, $panel, $qs = '')
{
	return sprintf('<a class="panelLibrary" href="/ajax/Panel.php?type=Library&amp;name=%s&%s">%s</a>', htmlspecialchars($panel), htmlspecialchars($qs), $text);
}