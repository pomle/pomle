$(function()
{
	scrobbleUpdate();
});

var timer = null;
var delay = 1000 * 60 * 5;
var spread = 1000;

//delay = 1000 * 60 * .25;

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
			var i = 0;
			var tracks = $(xml).find('track');

			tiles.each(function(index, tile)
			{
				var updateWait = 0;

				if( !(track = $(tracks).eq(index)) )
					return false;

				var track_image_url = track.find('image[size="extralarge"]').text();
				var url = track.find('url').text();

				var mbid = track.find('artist').attr('mbid');
				var t_artist = track.find('artist').text();
				var t_track = track.find('name').text();
				var title = t_track + '<small>' + t_artist + '</small>';
				var info = track.attr('nowplaying') ? '<img src="/img/LastFM-EQ-Icon.gif"> Now Playing...' : track.find('date').text();

				updateWait = 100 + Math.random() * spread;
				//updateWait = 100 + 100 * i;

				setTimeout(function()
					{
						$.ajax(
						{
							type: "GET",
							url: 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' + encodeURIComponent(t_artist) + '&api_key=' + lastfm_api_key,
							dataType: "xml",
							success: function(xml)
							{
								var artist_image_url = $(xml).find('artist:first').eq(0).find('image[size="extralarge"]:first').text();
								BrickTile.updateTiles(
									tile,
									artist_image_url || track_image_url || '/img/BrickTile_Fallback_LastFM.png',
									url,
									title,
									info
								);

								attachSpotifyURI(tile, t_track, t_artist);
							}
						});


					},
					updateWait
				);

				i++;
			});
		}
	});
}

var attachSpotifyURI = function(tile, track, artist)
{
	var updateTile = function(tile, spotifyURI)
	{
		$(tile).find('a').attr('href', spotifyURI);
		$(tile).find('.mainText').prepend('<img src="/img/Icon_Spotify.png"> ');
		return true;
	};


	$.ajax(
	{
		type: "GET",
		url: 'http://ws.spotify.com/search/1/track?q=' + encodeURIComponent(artist + ' ' + track.replace(/[&-]/g, ' ')),
		dateType: "xml",
		success: function(xml)
		{
			var tracks = $(xml).find('track');
			tracks.each(function(i, track)
			{
				var spotifyURI = $(track).attr('href');
				updateTile(tile, spotifyURI);
				return false;

				/*if( $(track).find('artist name').text() == artist )
				{
					updateTile(tile, spotifyURI);


				}*/
			});
		}
	});
}