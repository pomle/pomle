<?
function postSort($postIDs, &$posts)
{
	foreach($postIDs as $postID)
	{
		if( isset($posts[$postID]) )
		{
			$posts[] = $posts[$postID];
			unset($posts[$postID]);
		}
	}

	return true;
}