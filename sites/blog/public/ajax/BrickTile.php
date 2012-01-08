<?
require '../../Init.inc.php';

header("Content-type: text/plain");

if( isset($_GET['dump']) )
{
	print_r($UserSettings);
	exit();
}

if( isset($UserSettings->brickTileLayout) && ($UserSettings->brickTileLayout == 'matrix') )
	$UserSettings->brickTileLayout = 'list';
else
	$UserSettings->brickTileLayout = 'matrix';

echo json_encode($UserSettings->brickTileLayout);
