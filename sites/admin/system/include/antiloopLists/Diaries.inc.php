<?
namespace Element\Antiloop;

defaultSort($params, 'timeModified', true);

$Stmt = new \Query\Select("SELECT
		p.ID AS postID,
		IFNULL(p.timeModified, p.timeCreated) AS timeModified,
		p.timePublished,
		p.title,
		m.fileHash AS mediaHash
	FROM
		Posts p
		JOIN PostDiaries pd ON pd.postID = p.ID
		LEFT JOIN Media m ON m.ID = p.previewMediaID");

if( $search = $filter['search'] )
	$Stmt->addWhere("(p.title LIKE %S OR pd.content LIKE %S)", $search, $search);

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
		Field::time('timeModified', _('Ändringstid'), 'time'),
		Field::time('timePublished', _('Publiceringstid'), 'time'),
		Field::text('title', _('Titel'))
	);