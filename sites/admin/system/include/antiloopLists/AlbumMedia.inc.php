<?
namespace Element\Antiloop;

defaultSort($params, 'sortOrder', true);

$Stmt = new \Query\Select("SELECT
		pam.ID as postAlbumMediaID,
		m.fileHash AS mediaHash,
		pam.isVisible,
		pam.comment,
		pam.tags,
		pam.sortOrder
	FROM
		PostAlbumMedia pam
		JOIN Media m ON m.ID = pam.mediaID");

if( $filter['postID'] )
	$Stmt->addWhere("pam.postID = %u", $filter['postID']);

$Antiloop
	->setDataset($Stmt)
	->addFilters
	(
		#Filter\Search::text(),
		Filter\Slice::pagination(true),
		new Filter\Hidden('postID')
	)
	->addFields
	(
		Field::id('postAlbumMediaID'),
		new Field\Media(null, null, null, 100, 100, false),
		Field::enabled('isVisible'),
		Field::count('sortOrder'),
		Field::text('comment', _('Kommentar'), 'comment'),
		Field::ajaxLoad('AlbumMedia', array('postAlbumMediaID'))
	);