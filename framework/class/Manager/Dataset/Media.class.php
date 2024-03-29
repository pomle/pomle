<?
namespace Manager\Dataset;

class Media
{
	public static function getDescriptionByType($type)
	{
		$types = self::getTypes();
		return isset($types[$type]) ? $types[$type] : $type;
	}

	public static function getData($mediaID)
	{
		$query = \DB::prepareQuery("SELECT * FROM Media WHERE ID = %u", $mediaID);
		return \DB::queryAndFetchOne($query);
	}

	public static function getFileOriginalName($mediaID)
	{
		$query = \DB::prepareQuery("SELECT fileOriginalName FROM Media WHERE ID = %u", $mediaID);
		return \DB::queryAndFetchOne($query);
	}

	public static function getHashFromID($mediaID)
	{
		$query = \DB::prepareQuery("SELECT fileHash FROM Media WHERE ID = %u", $mediaID);
		return \DB::queryAndFetchOne($query);
	}

	public static function getIDFromHash($mediaHash)
	{
		$query = \DB::prepareQuery("SELECT ID FROM Media WHERE fileHash = %s", $mediaHash);
		return \DB::queryAndFetchOne($query);
	}

	public static function getPlugins()
	{
		$pluginFiles = glob(DIR_ASENINE_CLASS . 'Media/*.class.php');

		$plugins = array();
		foreach($pluginFiles as $pluginFile)
		{
			preg_match('/\/([A-Za-z0-9]+).class.php/u', $pluginFile, $className);
			$className = '\\Media\\' . $className[1];
			if( class_exists($className) )
				$plugins[] = $className;
		}
		return $plugins;
	}

	public static function getSpreadByHash($mediaHash)
	{
		if( strlen($mediaHash) !== 32 ) throw New \Exception("Media Hash Length not 32 chars (\"$mediaHash\")");
		if( !defined('DIR_MEDIA') || !is_dir(DIR_MEDIA) ) throw New \Exception("DIR_MEDIA not defined or not valid dir");

		$cmd = sprintf('$(which find) %s -name %s | sort', \escapeshellarg(DIR_MEDIA), \escapeshellarg($mediaHash . '*'));
		$res = shell_exec($cmd);
		$arr = explode("\n", $res);
		$arr = array_filter($arr);
		$arr = preg_grep('%' . DIR_MEDIA_SOURCE . '%', $arr, PREG_GREP_INVERT); // Remove source media file from list
		return $arr;
	}

	public static function getSpreadByID($mediaID)
	{
		return self::getSpreadByHash(self::getHashFromID($mediaID));
	}

	public static function getTypes()
	{
		static $pluginNames;

		if( !isset($pluginNames) )
		{
			$pluginNames = array();
			foreach(self::getPlugins() as $className)
				$pluginNames[$className::TYPE] = $className::DESCRIPTION;

			asort($pluginNames);
		}

		return $pluginNames;
	}
}