<?
require '../Init.inc.php';

header("Content-Type: application/xml; charset=UTF-8");

$feedType = in_array($_GET['type'], array(POST_TYPE_ALBUM, POST_TYPE_DIARY, POST_TYPE_TRACK)) ? $_GET['type'] : null;

$cacheKey = 'RSS_TIMELINE_' . $feedType;

$rssXML = Cache::get($cacheKey);
$rssXML = false;

if( $rssXML === false )
{
	setlocale(LC_TIME, null);

	$prefix = array
	(
		POST_TYPE_ALBUM => 'F',
		POST_TYPE_DIARY => 'D',
		POST_TYPE_TRACK => 'L'
	);

	$baseURL = 'http://pomle.com';
	$Fetcher = new \Fetch\Post(50, 0);

	$xml = new XMLWriter();
	$xml->openMemory();
	$xml->setIndent(true);
	$xml->setIndentString("\t");
	$xml->startDocument('1.0', 'UTF-8', 'yes');
	$xml->startElement('rss');
		$xml->writeAttribute('version', '2.0');
		$xml->startElement('channel');
			$xml->writeElement('title', _('Pomle.com'));
			$xml->writeElement('link', $baseURL);
			$xml->writeElement('description', 'The Timeline of Pomle');

			switch($feedType)
			{
				case POST_TYPE_ALBUM:
					$posts = $Fetcher->getAlbums();
				break;

				case POST_TYPE_DIARY:
					$posts = $Fetcher->getDiaries();
				break;

				case POST_TYPE_TRACK:
					$posts = $Fetcher->getTracks();
				break;

				default:
					$posts = $Fetcher->getTimeline();
				break;
			}

			foreach($posts as $Post)
			{
				try
				{
					$xml->startElement('item');
						$xml->writeElement('title', $feedType ? $Post->title : sprintf('%s: %s', $prefix[$Post::TYPE], $Post->title));
						$xml->writeElement('link', $Post->getURL());
						$xml->startElement('description');

							if( $Post->PreviewMedia && ($imageURL = \Media\Producer\Blog::createFromMedia($Post->PreviewMedia)->getRSSPreview()) )
								$xml->text(sprintf('<img src="%s" alt="">', $imageURL));

							$xml->text($Post->getSummary());

						$xml->endElement();
						$xml->writeElement('pubdate', strftime('%a, %d %b %Y %H:%M:%S GMT', $Post->timePublished));
					$xml->endElement();
				}
				catch(Exception $e)
				{
					die('XML Generation Failed, Reason: ' . $e->getMessage());
				}
			}

			$xml->endElement();
		$xml->endElement();
	$xml->endElement();

	$rssXML = $xml->outputMemory();

	Cache::set($cacheKey, $rssXML, 3600 * 4);
}

header('Content-Type: text/xml; charset=UTF-8');
header('Content-Length: ' . strlen($rssXML));

echo $rssXML;