<?
require '../Init.inc.php';

$css[] = '/css/Album.css';

if( !$Album = \Post\Album::loadOneFromDB($_GET['albumID']) )
	die('Not Found');

$pageTitle = $Album->title;

require HEADER;
?>
<div class="album clearfix">
	<?
	foreach($Album->media as $Media)
	{
		$mediaURL = \Media\Producer\Blog::createFromMedia($Media)->getAlbumThumb();
		printf('<a href="%s" class="image border" style="background-image: url(\'%s\');"><div class="overlay"></div></a>', sprintf('/Media.php?mediaID=%u&albumID=%u', $Media->mediaID, $Album->postID), $mediaURL);
	}
	?>
</div>
<?
require FOOTER;