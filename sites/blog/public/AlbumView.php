<?
require '../Init.inc.php';

$css[] = '/css/Album.css';

if( !$Album = \Album::loadOneFromDB($_GET['albumID']) )
	die('Not Found');

require HEADER;
?>
<div class="album clearfix">
	<?
	foreach($Album->media as $Media)
	{
		$mediaURL = \Media\Producer\Blog::createFromMedia($Media)->getAlbumThumb();
		printf('<a href="%s" class="image border" style="background-image: url(\'%s\');"><div class="overlay"></div></a>', \URL::albumImage($Album->postID, $Media->mediaID), $mediaURL);
	}
	?>
</div>
<?
require FOOTER;