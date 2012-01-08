<?
namespace Element\Antiloop;

require DIR_ANTILOOP_LISTS . 'Tracks.inc.php';

$Antiloop->addField(
	Field::creator('/TrackEdit.php', array('postID'))
);