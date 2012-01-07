<?
namespace Element\Antiloop;

require DIR_ANTILOOP_LISTS . 'Diaries.inc.php';

$Antiloop->addField(
	Field::creator('/DiaryEdit.php', array('postID'))
);