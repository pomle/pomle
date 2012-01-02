<?
#MENUPATH:Innehåll/Album
#URLPATH:AlbumOverview.php
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

$pageTitle = _('Album');

$List = \Element\Antiloop::getAsDomObject('Albums.Edit');

require HEADER;

echo $List;

require FOOTER;