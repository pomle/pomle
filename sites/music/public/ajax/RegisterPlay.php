<?
require '../../Init.inc.php';

try
{
	if( !isset($_GET['type']) || preg_match('/[^A-Za-z]/', $type = $_GET['type']) > 0 )
		throw New \Exception('Invalid Panel Type');

	if( !isset($_GET['name']) || preg_match('/[^A-Za-z]/', $name = $_GET['name']) > 0 )
		throw New \Exception('Invalid Panel Name');

	$includeFile = DIR_ELEMENT_PANEL . sprintf('%s.%s.inc.php', $type, $name);

	if( !file_exists($includeFile) )
		throw New \Exception(sprintf("File %s does not exist", $includeFile));

	require $includeFile;

}
catch(\Exception $e)
{
	if( DEBUG )
		echo \Element\Message::error($e->getMessage());
	else
		echo \Element\Message::error(_("Error Updating Panel"));
}
