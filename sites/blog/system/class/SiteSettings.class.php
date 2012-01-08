<?
class SiteSettings
{
	public static function createDefault()
	{
		$Setting = new \UserSetting();
		$Setting->brickTileLayout = 'matrix';
		return $Setting;
	}
}