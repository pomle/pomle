<?
class Diary extends Post
{
	const TYPE = POST_TYPE_DIARY;

	public
		$content;


	public static function addToDB()
	{
		$Post = parent::addToDB();

		$query = \DB::prepareQuery("INSERT INTO PostDiaries (postID) VALUES(%u)", $Post->postID);
		\DB::query($query);

		$query = \DB::prepareQuery("UPDATE Posts SET type = %s WHERE ID = %u", self::TYPE, $Post->postID);
		\DB::query($query);

		return $Post;
	}

	public static function loadFromDB($postIDs, $skipContent = false)
	{
		$diaries = parent::loadFromDB($postIDs);

		$diaryIDs = array_keys($diaries);

		if( $skipContent === false )
		{
			$query = \DB::prepareQuery("SELECT
					postID,
					content
				FROM
					PostDiaries
				WHERE
					postID IN %a",
				$diaryIDs);

			$result = \DB::queryAndFetchResult($query);

			$mediaIDs = array();

			while($row = \DB::assoc($result))
			{
				$postID = (int)$row['postID'];

				$Post = $diaries[$postID];

				$Post->content = $row['content'];

				$tags = null;
				/*if( preg_match_all('%<(flipblog)?:media.+(hash="(.+)").+/?>%U', $Post->content, $tags) )
				{
					print_r($tags);
				}*/

				if( preg_match_all('%<(flipblog)?:media.+(mediaID="([0-9]+)").+/?>%U', $Post->content, $match) )
					$mediaIDs = array_merge($mediaIDs, $Post->mediaIDs = $match[3]);
			}

			$medias = \Manager\Media::loadFromDB($mediaIDs);

			foreach($diaries as $Post)
			{
				if( isset($Post->mediaIDs) )
				{
					foreach($Post->mediaIDs as $mediaID)
						if( isset($medias[$mediaID]) )
							$Post->addMedia($medias[$mediaID]);

					unset($Post->mediaIDs);
				}
			}
			reset($diaries);
		}

		return $diaries;
	}


	public function __construct()
	{
		parent::__construct();
		$this->media = array();
	}


	public function getHTMLContent()
	{
		$content = $this->content;

		if( preg_match_all('%<(pre|code).*>(.*)</(pre|code)>%Usme', $content, $matches) )
			foreach($matches[2] as $code)
				$content = str_replace($code, htmlspecialchars($code), $content);

		#$content = preg_replace('%(<(pre|code).*>)(.*)(</(pre|code)>)%Usme', "'\\1'.htmlspecialchars('\\3').'\\4'", $content);

		$replacements = array
		(
			"\t" => '&nbsp;&nbsp;&nbsp;&nbsp;'
		);

		$content = str_replace(array_keys($replacements), $replacements, $content);


		if( preg_match_all('%<flipblog:([A-Za-z]+) .*/>%', $content, $tags) )
		{
			### Loop thru all tags and parse their attributes
			foreach($tags[0] as $index => $tag)
			{
				$type = $tags[1][$index];
				$pluginName = '\\Plugin\\' . ucfirst($type);

				if( class_exists($pluginName) )
				{
					$Plugin = new $pluginName();

					preg_match_all('%([A-Za-z]+)="(.*)"%U', $tag, $attributes);

					$dataPairs = array();

					foreach($attributes[0] as $index => $attr)
					{
						$dataPairs[$attributes[1][$index]] = $attributes[2][$index];
					}

					$replacementHTML = $Plugin->getHTML($dataPairs);

					$content = str_replace($tag, $replacementHTML, $content);
				}
			}
		}

		return $content;
	}

	public function getURL()
	{
		return sprintf('/DiaryView.php?diaryID=%u', $this->postID);
	}
}