<?
require '../Init.inc.php';

$css[] = '/css/Album.css';

if( !$Media = \Manager\Media::loadOneFromDB($_GET['mediaID']) )
	die('Not Found');

$mediaURL = \Media\Producer\Blog::createFromMedia($Media)->getAlbumImage();

require HEADER;
?>
<div class="frame">
	<div class="image" style="background-image: url('<? echo $mediaURL; ?>');">
		<div class="overlay">

		</div>
	</div>
</div>
<?
require FOOTER;