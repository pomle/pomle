<?
#MENUPATH:Innehåll/Hits
define('ACCESS_POLICY', 'AllowViewTrack');

require '../Init.inc.php';

$pageTitle = _('Hits');

$List = \Element\Antiloop::getAsDomObject('Tracks.Edit');

require HEADER;

echo $List;

require FOOTER;