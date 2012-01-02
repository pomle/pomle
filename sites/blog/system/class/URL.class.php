<?
class URL
{
	public static function album(\Album $Album)
	{
		return sprintf('/AlbumView.php?albumID=%u', $Album->postID);
	}

	public static function albumImage($albumID, $mediaID)
	{
		return sprintf('/AlbumImage.php?albumID=%u&mediaID=%u', $albumID, $mediaID);
	}
}