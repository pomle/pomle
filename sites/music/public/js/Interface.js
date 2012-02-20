var Panel =
{
	placeLibrary: function(name, placeIn, data)
	{
		Panel.placeURL('/ajax/Panel.php?type=Library&name=' + name, placeIn, data);
	},

	placeURL: function(url, placeIn, data)
	{
		if( placeIn.hasClass('isLocked') )
			console.error('Destination Panel Locked');

		$.ajax(
		{
			url: url,
			type: 'get',
			data: data,
			complete: function(jqXHR, textStatus)
			{},
			error: function(jqXHR, textStatus, errorThrown)
			{
				alert(textStatus);
			},
			success: function(data, textStatus, jqXHR)
			{
				placeIn.html(data);
			}
		});
	}
}



$(function()
{
	var element_control = $('#control');
	var control_height = element_control.outerHeight();

	var element_playlists = $('#playlists');
	var element_playlist_panels = element_playlists.find('.panels');

	var panel_library = $('#library');
	var panel_activePlaylist = $('#activePlaylist');
	var panel_playlistBrowser = $('#playlistBrowser');


	$(window).on('resize', function() {
		var window_height = $(this).height();

		element_playlist_panels.css('height', (window_height - 130) + 'px');

		console.log($(this).height());


		//element_playlist.css('height',
	}).trigger('resize');


	$(document).delegate('.panelLibrary', 'click', function(e)
	{
		e.preventDefault();
		var name = $(this).attr('href').substr(1);
		Panel.placeLibrary(name, panel_library);
	});

	$(document).delegate('.panelPlaylist', 'click', function(e)
	{
		e.preventDefault();
		var name = $(this).attr('href').substr(1);
		Panel.placeLibrary(name, panel_playlistBrowser);
	});

	$('form#search').on('submit', function(e) {
		e.preventDefault();

		var url = $(this).attr('action') + '&' + $(this).serialize();

		Panel.placeURL(url, panel_library);
	});

});