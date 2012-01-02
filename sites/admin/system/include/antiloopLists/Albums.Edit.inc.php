<?
namespace Element\Antiloop;

require DIR_ANTILOOP_LISTS . 'Albums.inc.php';

$Antiloop->addField(
	Field::creator('/AlbumEdit.php', array('postID'))
);