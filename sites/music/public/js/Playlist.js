var PlaylistController = function(element)
{
	var playlist = $(element).find('.items');

	this.appendTo = function(userTrackItems)
	{
		playlist.append(userTrackItems);
	}

	this.clear = function()
	{
		playlist.html('');
	}

	this.replaceWith = function(userTrackItems)
	{
		playlist.html(userTrackItems);
	}
}

var ActivePlaylist;

$(function()
{
	ActivePlaylist = new PlaylistController($('#activePlaylist'));

	$('#playlists').find('.tabs .index .tab').on('click', function(e) {
		e.preventDefault();
		var panel = $(this).data('panel');
		$(this).addClass('active').siblings().removeClass('active');
		$(this).closest('.tabs').find('.panels').find('#' + panel).show().siblings().hide();
	});


	$(document)
		.on("click", ".userTrackList .append", function(e) {
			e.preventDefault();

			var userTracks = $(this).closest('.userTrackList').find('.items .userTrack').clone();

			ActivePlaylist.appendTo(userTracks);
		})
		.on("click", ".userTrackList .play", function(e) {
			e.preventDefault();

			var userTracks = $(this).closest('.userTrackList').find('.items .userTrack').clone();

			ActivePlaylist.replaceWith(userTracks);
			cordless.playlistSeek(0);
		})
		.on("click", ".userTrackList .userTrack .title", function(e) {
			e.preventDefault();

			var userTrack = $(this).closest('.userTrack').clone();

			ActivePlaylist.appendTo(userTrack);
		})
		.on("click", ".playlist .userTrack .title", function(e) {
			e.preventDefault();

			var userTrack = $(this).closest('.userTrack');

			cordless.trackLoadItem(userTrack);
			cordless.playbackStart();
		})

		;
});