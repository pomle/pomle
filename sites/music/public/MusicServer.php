<?
require '../Init.inc.php';

try
{
	if( !isset($_GET['userTrackID']) )
		throw New Exception("Must specify userTrackID");

	$userTrackID = $_GET['userTrackID'];


	if( isset($_GET['format']) )
		$format = $_GET['format'];

	elseif( isset($_SERVER['HTTP_ACCEPT']) && preg_match('%audio/(ogg|mp3)%', $_SERVER['HTTP_ACCEPT'], $match) )
		$format = $match[1];

	else
		$format = 'mp3';


	switch($format)
	{
		case 'ogg':
			$format = 'ogg';
			$codec = 'libvorbis';
			$ext = 'oga';
			$contentType = 'audio/ogg';
		break;

		case 'mp3':
			$format = 'mp3';
			$codec = 'libmp3lame';
			$ext = 'mp3';
			$contentType = 'audio/mp3';
		break;

		default:
			throw New \Exception(sprintf('Unknown format "%s"', $format));
	}


	if( !$UserTrack = \Music\UserTrack::loadFromDB($userTrackID) )
		throw New Exception(MESSAGE_USERTRACK_MISSING);


	$mediaHash = $UserTrack->Track->Audio->mediaHash;

	$fileTitle = sprintf('%s.%s', $UserTrack, $ext);
	$filePath = DIR_MEDIA . sprintf('cordless/%s/', $format);
	$fileName = $filePath . $mediaHash;


	if( !file_exists($fileName) )
	{
		if( !file_exists($filePath) && !mkdir($filePath, 0755, true) )
			throw New \Exception("Could not create destination dir " . $filePath);

		$Factory = new \Media\Generator\AudioTranscode($UserTrack->Track->Audio, $format, $codec, 128000, 44100, 2);

		if( !$Factory->saveToFile($fileName) )
			throw New \Exception("File Generation Failed");
	}

	$UserTrack->registerPlay();

	servePartial($fileName, $fileTitle, $contentType);
}
catch(Exception $e)
{
	header('HTTP/1.1 404 Not Found');

	if( DEBUG ) echo $e->getMessage();

	exit();
}

function servePartial($fileName, $fileTitle = null, $contentType = 'application/octet-stream')
{
	if( !file_exists($fileName) )
		throw New \Exception(sprintf('File not found: %s', $fileName));

	if( !is_readable($fileName) )
		throw New \Exception(sprintf('File not readable: %s', $fileName));


	header_remove('Cache-Control');
	header_remove('Pragma');


	### Defaults to sending whole file
	$byteOffset = 0;
	$byteLength = $fileSize = filesize($fileName);

	header('Accept-Ranges: bytes', true);
	header(sprintf('Content-Type: %s', $contentType), true);

	if( $fileTitle )
		header(sprintf('Content-Disposition: attachment; filename="%s"', $fileTitle));

	### Parse Content-Range header for byte offsets, looks like "bytes=11525-" OR "bytes=11525-12451"
	if( isset($_SERVER['HTTP_RANGE']) && preg_match('%bytes=(\d+)-(\d+)?%i', $_SERVER['HTTP_RANGE'], $match) )
	{
		### Offset signifies where we should begin to read the file
		$byteOffset = (int)$match[1];

		### Length is for how long we should read the file according to the browser, and can never go beyond the file size
		if( isset($match[2]) )
			$byteLength = min( (int)$match[2], $byteLength - $byteOffset);

		header("HTTP/1.1 206 Partial content");
		header(sprintf('Content-Range: bytes %d-%d/%d', $byteOffset, $byteLength - 1, $fileSize));  ### Decrease by 1 on byte-length since this definitiot is of which bytes are sent, zero-indexed (byte 0 being first byte)
	}

	$byteRange = $byteLength - $byteOffset;

	header(sprintf('Content-Length: %d', $byteRange));

	header(sprintf('Expires: %s', date('D, d M Y H:i:s', time() + 60*60*24*90) . ' GMT'));


	$buffer = ''; 	### Variable containing the buffer
	$bufferSize = 512 * 16; ### Just a reasonable buffer size
	$bytePool = $byteRange; ### Contains how much is left to read of the byteRange

	if( !$handle = fopen($fileName, 'r') )
		throw New \Exception("Could not get file handle");

	if( fseek($handle, $byteOffset, SEEK_SET) == -1 )
		throw New \Exception("fseek failed");

	$log = '';

	while( $bytePool > 0 )
	{
		$log .= sprintf("Bytepool is %12d\n", $bytePool);

		$chunkSize = min($bufferSize, $bytePool);

		$log .= sprintf("Want to read: %12d bytes\n", $chunkSize);

		$buffer = fread($handle, $chunkSize);

		$chunkSize = strlen($buffer);

		$log .= sprintf("Actually read: %12d bytes\n", $chunkSize);

		if( $chunkSize == 0 )
		{
			trigger_error('Chunksize became 0', E_USER_WARNING);
			file_put_contents(DIR_LOG . 'MusicServer.ChunkSize.error', $log);
			break;
		}

		$bytePool -= $chunkSize;

		### Write the buffer to output
		print $buffer;

		flush();
	}

	exit();
}