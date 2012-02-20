<?
require '../../../Init.inc.php';

$mediaURL = \Media\Producer\BrickTile::createFromHash($_GET['mediaHash'])->getTile();

echo json_encode($mediaURL);