<?
function niceurl($url)
{
	$spaceSubstitute = '-';

	$_ = $spaceSubstitute;

	$from = array('Å', 'Ä', 'Æ', 'Ö', 'Ø', 'Ü', 'å', 'ä', 'æ', 'ö', 'ø', 'ü', 'é', 'ć', 'ß', ' ', '.', ':');
	$to   = array('A', 'A', 'A', 'O', 'O', 'U', 'a', 'a', 'a', 'o', 'o', 'u', 'e', 'c', 'ss', $_, $_, '-');

	$url = str_replace($from, $to, $url);
	$url = preg_replace('/[^a-zA-Z0-9\-_]/', '', $url);

	$doubleSpaceSubstitute = str_repeat($spaceSubstitue, 2);
	$__ = $doubleSpaceSubstitue;

	while(strpos($url, $__)) { $url = str_replace($__, $_, $url); }
	$url = trim($url, ' ' . $_);

	$url = preg_replace('/\-+/', '-', $url);

	return $url;
}
