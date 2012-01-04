<?
require '../Init.inc.php';

$css[] = '/css/Album.css';

if( !$Album = \Album::loadOneFromDB($_GET['albumID']) )
	die('Not Found');

require HEADER;
?>
<ul class="album clearfix">
	<?
	foreach($Album->media as $Media)
	{
		$mediaURL = \Media\Producer\BrickTile::createFromMedia($Media)->getAlbumImage();
		?>
		<li class="container"><? printf('<a href="%s" class="image" style="background-image: url(\'%s\');"><div class="overlay"></div></a>', \URL::albumImage($Album->postID, $Media->mediaID), $mediaURL); ?></li>
		<?
	}
	?>
</ul>
<?
require FOOTER;