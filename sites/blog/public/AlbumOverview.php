<?
require '../Init.inc.php';

if( !$Album = \Album::loadOneFromDB($_GET['albumID']) )
	die('Not Found');

require HEADER;
?>
<ul class="album">
	<?
	foreach($Album->media as $Media)
	{
		$mediaURL = \Media\Producer\BrickTile::createFromMedia($Media)->getAlbumImage();
		?>
		<li class="image">
			<? printf('<img src="%s" alt="">', $mediaURL); ?>
		</li>
		<?
	}
	?>
</ul>
<?
require FOOTER;