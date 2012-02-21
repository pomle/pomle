var
	PlayQueue,
	Cordless,
	Panel;

$(function()
{
	var lockKeyboard = false;

	// Initialize Controllers
	PlayQueue = new PlaylistController($('#playqueue .playlist'));
	Cordless = new CordlessController($('#cordless'), PlayQueue);
	Panel = new PanelController();

	// Set Up Re-usable jQuery objects
	var sidebar = $('.sidebar');

	var panel_library = $('#library');
	var panel_activePlaylist = $('#activePlaylist');
	var panel_playlistBrowser = $('#playlistBrowser');

	var scrollable_playqueue = $('#playqueue').find('.items');
	var scrollable_upload = $('#playqueue').find('.queue');

	// Keyboard Shortcuts
	$(document).keydown(function(e)
	{
		if( lockKeyboard ) return true;

		switch(e.keyCode)
		{
			case 37: // Left Arrow
				e.preventDefault();
				Cordless.playlistPrev();
			break;

			case 39: // Right Arrow
				e.preventDefault();
				Cordless.playlistNext();
			break;

			case 34: // Page Down
				e.preventDefault();
			break;

			case 33: // Page Up
				e.preventDefault();
			break;

			case 36: // Home
				e.preventDefault();
			break;

			case 35: // End
				e.preventDefault();
			break;

			case 80: // "p"
				playqueueLockToggle();
			break;

			case 85: // "u"
				uploadLockToggle();
			break;

			case 32: // Spacebar
				Cordless.playbackToggle();
			break;
		}
		//console.log(e.keyCode);
	})
	.find(':input')
		.on("focus", function() { lockKeyboard = true; })
		.on("blur", function() { lockKeyboard = false; })
		;


	$(window).on('resize', function() {
		var window_height = $(this).height();
		scrollable_playqueue.css('height', (window_height - 130) + 'px');
		scrollable_upload.css('max-height', (window_height - 130) + 'px');
	}).trigger('resize');

	sidebar.find('.lock').on('click', function(e) {
		e.preventDefault();
		$(this).closest('.sidebar').toggleClass('locked');
	});



	$('form#search').on('submit', function(e) {
		e.preventDefault();

		var url = $(this).attr('action') + '&' + $(this).serialize();

		Panel.placeURL(url, panel_library);
	});

	// "Live Event Bindings, use minimally
	$(document)
		.on("click", '.panelLibrary', 'click', function(e)
		{
			e.preventDefault();
			var name = $(this).attr('href').substr(1);
			Panel.placeLibrary(name, panel_library);
		})
		.on("click", '.panelPlaylist', 'click', function(e)
		{
			e.preventDefault();
			var name = $(this).attr('href').substr(1);
			Panel.placeLibrary(name, panel_playlistBrowser);
		})

		// Append Library Playlist to PlayQueue
		.on("click", ".userTrackList .append", function(e) {
			e.preventDefault();
			var userTracks = $(this).closest('.userTrackList').find('.items .userTrack').clone();
			PlayQueue.appendTo(userTracks);
		})
		// Play Library Playlist to PlayQueue
		.on("click", ".userTrackList .play", function(e) {
			e.preventDefault();
			var userTracks = $(this).closest('.userTrackList').find('.items .userTrack').clone();
			PlayQueue.replaceWith(userTracks);
			Cordless.playlistSeek(0);
			Cordless.playbackStart();
		})
		// Append Library Track to PlayQueue
		.on("click", "#library .userTrackList .userTrack .title", function(e) {
			e.preventDefault();
			var userTrack = $(this).closest('.userTrack').clone();
			PlayQueue.appendTo(userTrack);
		})
		// Jump to Track in PlayQueue
		.on("click", ".playlist .userTrack .title", function(e) {
			e.preventDefault();
			var userTrack = $(this).closest('.userTrack');
			Cordless.trackLoadItem(userTrack);
			Cordless.playbackStart();
		})

		// Playlist Shuffle
		.on("click", ".playlist>.control .shuffle", function(e) {
			e.preventDefault();
			var P = new PlaylistController($(this).closest('.playlist'));
			P.shuffle();
		})
		// Playlist Clear
		.on("click", ".playlist>.control .clear", function(e) {
			e.preventDefault();
			var P = new PlaylistController($(this).closest('.playlist'));
			P.clear();
		})
		;

});