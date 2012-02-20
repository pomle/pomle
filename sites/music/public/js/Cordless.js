var CordlessController = function(element)
{
	var self = this;

	this.isPlaying = false;
	this.isTrackLoaded = false;

	this.playingArtist = null;
	this.playingTitle = null;

	this.endAction = 'playlistNext';

	var audio = this.audio = new Audio();

	var controls = element.find('.controls');
	var scrubber = element.find('.scrubber');
	var progress = scrubber.find('.progress');

	var playlist = this.activePlaylist = $('#activePlaylist');

	var time_current = element.find('.time .current');
	var time_total = element.find('.time .total');
	var track_title = element.find('.trackinfo .title');

	/*audio.onabort = function()
	{
		console.log('onabort');

		self.isTrackLoaded = false;
		self.playbackStop();
		time_current.text('0:00');
		time_total.text('0:00');
		self.progressSet(0);
	}*/

	audio.onload = function()
	{
		console.log('onload');
	}

	audio.onerror = function()
	{
		self.eventAudioError();
	}

	/*
	audio.oncanplay = function()
	{
		self.isTrackLoaded = true;
	}

	audio.ondurationchange = function()
	{
		time_total.text(self.formatHumanDuration(this.duration));
	}

	audio.onended = function()
	{
		self.playlistNext();
		self.playbackStart();
	}

	audio.ontimeupdate = function()
	{
		console.log(this.currentTime);
		self.progressSet(this.currentTime / this.duration);
		time_current.text(self.formatHumanDuration(this.currentTime));
	});

	audio.onplay = function()
	{
		console.log('Audio.onplay');
		self.isPlaying = true;

	}

	// Does not seem to work
	audio.onpause = function()
	{
		console.log('Audio.onpause');
		self.isPlaying = false;
	}*/

	this.controlUpdateProgress = function()
	{
		self.progressSet(audio.currentTime / audio.duration);

		if( isFinite(audio.currentTime) )
			time_current.text(self.formatHumanDuration(audio.currentTime));

		if( isFinite(audio.duration) )
			time_total.text(self.formatHumanDuration(audio.duration));
	}

	this.eventAudioError = function()
	{
		this.isPlaying = false;
		this.isTrackLoaded = false;

		console.log('Error Caught');
	}

	this.eventDurationChanged = function()
	{
		time_total.text(self.formatHumanDuration(audio.duration));
	}

	this.eventPlaybackStarted = function()
	{
		element.addClass('isPlaying');
	}

	this.eventPlaybackStopped = function()
	{
		element.removeClass('isPlaying');
	}


	this.eventTimeChanged = function()
	{
		this.controlUpdateProgress();
	}

	this.formatHumanDuration = function(seconds)
	{
		var minutes = Math.floor(seconds / 60);
		seconds = Math.floor(seconds % 60);

		return minutes + ':' + this.zeroPad(seconds, 2);
	}

	this.mainLoop = function()
	{
		//console.log(audio.seeking);


		if( self.isPlaying )
		{
			self.controlUpdateProgress();

			if( audio.ended )
			{
				self.playlistNext();
			}
		}
	}

	this.playlistGet = function()
	{
		return playlist.find('.items .item');
	}

	this.playlistNext = function()
	{
		return self.playlistSkip(1);
	}

	this.playlistPrev = function()
	{
		return self.playlistSkip(-1);
	}

	this.playlistSeek = function(index)
	{
		var wasPlaying = self.isPlaying;

		var items = self.playlistGet();
		var item = items.eq(index);

		if( item.length == 0 )
			return false;

		self.playbackReset();

		if( !self.trackLoadItem(item) )
			return false;

		if( wasPlaying )
			self.playbackStart();

		return true;
	}

	this.playlistSkip = function(diff)
	{
		var currentIndex, items, item;

		items = self.playlistGet();

		currentIndex = items.filter('.isCurrent').index();

		if( currentIndex == -1 ) currentIndex = 0;

		return self.playlistSeek(currentIndex + diff);
	}

	this.progressSet = function(value)
	{
		value = Math.min(Math.abs(value), 1) * 100;
		progress.css('width', value + '%');
	}



	this.playbackReset = function()
	{
		self.playbackStop();
		self.trackUnload();

		time_current.text('0:00');
		time_total.text('0:00');

		self.progressSet(0);
	}

	this.playbackStart = function()
	{
		if( !self.isTrackLoaded )
			self.playlistSkip(0);

		element.addClass('isPlaying');

		audio.play();

		self.isPlaying = true;

		self.eventPlaybackStarted();
	}

	this.playbackStop = function()
	{
		element.removeClass('isPlaying');

		audio.pause();

		self.isPlaying = false;

		self.eventPlaybackStopped();
	}

	this.playbackToggle = function()
	{
		if( self.isPlaying )
			self.playbackStop();
		else
			self.playbackStart();
	}

	this.playbackSeek = function(seconds)
	{
		if( self.isTrackLoaded )
		{
			audio.currentTime = seconds;
			self.eventTimeChanged();
		}
	}

	this.trackLoadItem = function(item)
	{
		var userTrackID, title;

		if( !(userTrackID = $(item).data('usertrackid')) ) return false;

		item.addClass('isCurrent').siblings().removeClass('isCurrent');

		title = item.find('.artist').text() + ' - ' + item.find('.title').text();
		track_title.html(title);

		self.playingArtist = item.data('artist');
		self.playingTitle = item.data('title');

		element.data('playing-artist', self.playingArtist);
		element.data('playing-title', self.playingTitle);

		return this.trackLoadURL('/MusicServer.php?userTrackID=' + userTrackID);
	}

	this.trackLoadURL = function(url)
	{
		audio.src = url;
		self.isTrackLoaded = true;

		return true;
	}

	this.trackUnload = function()
	{
		self.isTrackLoaded = false;
	}


	this.zeroPad = function(str, len)
	{
		str = '' + str;
		while(str.length < len)
			str = '0' + str;

		return str;
	}

	controls.find('.play_pause').on('click', function(e)
	{
		e.preventDefault();
		self.playbackToggle();
	});

	controls.find('.prev').on('click', function(e)
	{
		e.preventDefault();
		self.playlistPrev();
	});

	controls.find('.next').on('click', function(e)
	{
		e.preventDefault();
		self.playlistNext();
	});

	scrubber.on('mousedown', function(e)
	{
		var pos = e.pageX - $(this).offset().left;
		var max = $(this).width();

		self.playbackSeek(audio.duration * (pos / max));
	})
	.on('click', function(e)
	{
		e.preventDefault();
	});

	var refreshLoop = setInterval(self.mainLoop, 100);
}

var cordless;

$(function()
{
	cordless = new CordlessController($('.cordless'));
});