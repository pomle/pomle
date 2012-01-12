$(function()
{
	var dom = $('.track');

	var artist = dom.attr('data-artist');
	var track = dom.attr('data-track');

	$.ajax(
	{
		type: "GET",
		url: 'http://ws.audioscrobbler.com/2.0/?method=artist.getinfo&api_key=' + lastfm_api_key + '&artist=' + escape(artist),
		dataType: "xml",
		success: function(xml)
		{
			var bio = $(xml).find('bio').find('content').text();
			bio = bio.replace(/\n/g, '<br>');
			dom.find('.description').find('.bio').html(bio);
			dom.find('.description').fadeIn(1000);
		}
	});

	//console.log('http://ws.audioscrobbler.com/2.0/?method=track.getinfo&api_key=' + lastfm_api_key + '&artist=' + url_artist + '&track=' + url_track);

	/*$.ajax(
	{
		type: "GET",
		url: 'http://ws.audioscrobbler.com/2.0/?method=library.gettracks&api_key=' + api_key + '6&user=pomle&artist=' + artist,
		dataType: "xml",
		success: function(xml)
		{
			var bio = $(xml).find('bio').find('content').text();
			dom.find('.description').find('.bio').html(bio).slideDown(3000);
		}
	});

	http://ws.audioscrobbler.com/2.0/?method=library.gettracks&api_key=b25b959554ed76058ac220b7b2e0a026&user=pomle*/
});