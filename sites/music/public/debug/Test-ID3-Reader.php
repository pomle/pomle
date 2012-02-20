<?
require '_Debug.php';

if( $Track = \Music\Track::loadFromDB(90) )
{
	$ID3 = new ID3($Track->Audio->getFilePath());

	print_r($ID3->id3['tags']);

	var_dump( $ID3->getTitle(), $ID3->getArtist(), $ID3->getAlbum(), $ID3->getYear(), $ID3->getTrackNumber() );
}