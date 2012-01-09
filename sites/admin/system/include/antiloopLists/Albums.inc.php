<?
namespace Element\Antiloop;

defaultSort($params, 'timeModified', true);

$Stmt = new \Query\Select("SELECT
		p.ID AS postID,
		p.isPublished,
		IFNULL(p.timeModified, p.timeCreated) AS timeModified,
		p.timePublished,
		p.title,
		m.fileHash AS mediaHash
	FROM
		Posts p
		JOIN PostAlbums pa ON pa.postID = p.ID
		LEFT JOIN Media m ON m.ID = p.previewMediaID");

if( $search = $filter['search'] )
	$Stmt->addWhere("(p.title LIKE %S OR pa.description LIKE %S)", $search, $search);

$Antiloop
	->setDataset($Stmt)
	->addFilters
	(
		Filter\Search::text(),
		Filter\Slice::pagination()
	)
	->addFields
	(
		Field::id('postID'),
		Field::thumb(),
		Field::enabled('isPublished'),
		Field::text('title', _('Titel')),
		Field::time('timeModified', _('Ändringstid'), 'time'),
		Field::time('timePublished', _('Publiceringstid'), 'time')
	);