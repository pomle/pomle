$(function()
{
	var timer = null;
	var delay = 1000 * 60 * 5;

	var scrobbleUpdate = function()
	{
		var tiles = $('#recentScrobbles.brickTile .item');

		clearTimeout(timer);

		$.ajax(
		{
			type: "GET",
			url: 'http://ws.audioscrobbler.com/2.0/?method=user.getrecenttracks&user=&api_key=' + lastfm_api_key + '&user=' + lastfm_user + '&limit=' + tiles.length,
			dataType: "xml",
			complete: function()
			{
				timer = setTimeout(scrobbleUpdate, delay);
			},
			success: function(xml)
			{
				var tracks = $(xml).find('track');

				tiles.each(function(index, tile)
				{
					if( !(track = $(tracks).eq(index)) )
						return false;

					var imageURL = track.find('image[size="extralarge"]').text();

					BrickTile.updateTiles(
						tile,
						imageURL,
						track.find('url').text(),
						track.find('name').text() + '<small>' + track.find('artist').text() + '</small>',
						track.attr('nowplaying') ? '<img src="/img/LastFM-EQ-Icon.gif"> Now Playing...' : track.find('date').text()
					);
				});
			}
		});
	}

	scrobbleUpdate();
});