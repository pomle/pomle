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
		<li class="image"><? printf('<a href="%s"><img src="%s" alt=""></a>', \URL::albumImage($Album->postID, $Media->mediaID), $mediaURL); ?></li>
		<?
	}
	?>
</ul>
<?
require FOOTER;