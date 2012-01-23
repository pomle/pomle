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
			var artists = {};

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
				var caption = t_track + '<small>' + t_artist + '</small>';
				var info = track.attr('nowplaying') ? '<img src="/img/LastFM-EQ-Icon.gif"> Now Playing...' : track.find('date').text();

				if( !artists[t_artist] )
					artists[t_artist] = [];

				artists[t_artist].push(
					{
						'title': t_track,
						'caption': caption,
						'info': info,
						'url': url,
						'image': track_image_url,
						'tile': tile
					}
				);

				i++;
			});

			$.each(artists, function(artist, tracks)
			{
				$.ajax(
					{
					type: "GET",
					url: 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&artist=' + encodeURIComponent(artist) + '&api_key=' + lastfm_api_key,
					dataType: "xml",
					error: function()
					{
						$.each(tracks, function(index, track)
						{
							BrickTile.updateTiles(track.tile, '/img/BrickTile_Fallback_LastFM.png', track.url, track.caption, track.info);
						});
					},
					success: function(xml)
					{
						var artist_images = $(xml).find('artist').eq(0).find('image[size="extralarge"]');

						$.each(tracks, function(index, track) {
							var updateWait = 100 + Math.random() * spread;
							var image_index = index % artist_images.length;
							var image_url = artist_images.eq(image_index).text();

							setTimeout(
								function()
								{
									BrickTile.updateTiles(track.tile, image_url, track.url, track.caption, track.info);
									attachSpotifyURI(track.tile, track.title, artist);
								},
								updateWait
							);
						});
					}
				});

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