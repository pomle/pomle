<h1><? echo htmlspecialchars(_('Dates')); ?></h1>

<ul>
	<?
	### Tracks newer than 24 h
	$timeNow = time();

	$query = \DB::prepareQuery("SELECT
			COUNT(*) AS trackCount
		FROM
			Music_UserTracks ut
		WHERE
			ut.userID = %u
			AND ut.timeCreated > %d",
		$User->userID,
		$timePre24h = ($timeNow - 60*60*24*1));

	$trackCount = \DB::queryAndFetchOne($query);

	if( $trackCount )
	{
		?>
		<li><? echo libraryLink(_('Last 24 hours'), 'ListInterval', sprintf('uts_f=%d&uts_t=%d', $timePre24h, $timeNow)); ?> (<? echo $trackCount; ?>)</li>
		<?
	}

	### Tracks newer than 7 days

	$query = \DB::prepareQuery("SELECT
			COUNT(*) AS trackCount
		FROM
			Music_UserTracks ut
		WHERE
			ut.userID = %u
			AND ut.timeCreated > %d",
		$User->userID,
		$timePre7d = ($timeNow - 60*60*24*7));

	$trackCount = \DB::queryAndFetchOne($query);

	if( $trackCount )
	{
		?>
		<li><? echo libraryLink(_('Last 7 days'), 'ListInterval', sprintf('uts_f=%d&uts_t=%d', $timePre7d, $timeNow)); ?> (<? echo $trackCount; ?>)</li>
		<?
	}


	### Grab Year and Months which had uploads
	$query = \DB::prepareQuery("SELECT
			YEAR(FROM_UNIXTIME(ut.timeCreated)) AS year,
			MONTH(FROM_UNIXTIME(ut.timeCreated)) AS month,
			COUNT(*) AS trackCount
		FROM
			Music_UserTracks ut
		WHERE
			ut.userID = %u
		GROUP BY
			year,
			month
		ORDER BY
			year DESC,
			month DESC",
		$User->userID);

	$Result = \DB::queryAndFetchResult($query);

	while($row = $Result->fetch_assoc())
	{
		$uts = mktime(0, 0, 0, $row['month'], 1, $row['year']);
		?>
		<li><? echo libraryLink(strftime('%Y %B', $uts), 'ListMonth', 'uts=' . $uts); ?> (<? echo $row['trackCount']; ?>)</li>
		<?
	}
	?>
</ul>