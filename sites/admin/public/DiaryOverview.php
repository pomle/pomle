<?
#MENUPATH:Innehåll/Dagbok
#URLPATH:DiaryOverview.php
define('ACCESS_POLICY', 'AllowViewDiary');

require '../Init.inc.php';

$pageTitle = _('Dagbok');

$List = \Element\Antiloop::getAsDomObject('Diaries');

require HEADER;

echo $List;

require FOOTER;