<?
require '../Init.inc.php';

$css[] = '/css/Album.css';

if( isset($_GET['mediaHash']) )
	$Media = \Manager\Media::loadByHash($_GET['mediaHash']);

if( isset($_GET['mediaID']) )
	$Media = \Manager\Media::loadOneFromDB($_GET['mediaID']);

if( !$Media )
{
	$Media = \Manager\Media::integrateIntoLibrary(\Media\Image::createFromFile(DIR_SITE_RESOURCE . 'FileNotFound.jpg'));
	header('HTTP/1.0 404 Not Found');
}

if( isset($_GET['albumID']) )
{

}


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