<?
namespace Event;

class Album
{
	public static function createFromInfo($title, $year = null)
	{
		if( !$Album = \Music\Album::loadByTitle($title) )
		{
			$Album = new \Music\Album($title, mktime(0, 0, 0, 1, 1, (int)$year));
		}

		return $Album;
	}
}