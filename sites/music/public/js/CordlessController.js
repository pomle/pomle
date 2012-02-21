var CordlessController = function(element, Playlist)
{
	var self = this;

	this.isPlaying = false;
	this.isTrackLoaded = false;

	this.playingArtist = null;
	this.playingTitle = null;

	this.eventListeners = {};

	var Player = new Audio();
	this.Player = Player;

	var controls = element.find('.controls');
	var scrubber = element.find('.scrubber');
	var progress = scrubber.find('.progress');

	var time_current = element.find('.time .current');
	var time_total = element.find('.time .total');

	var track_title = element.find('.trackinfo .title');
	var track_artist = element.find('.trackinfo .artist');
	var track_error = element.find('.trackinfo .error');

	// Probably not working
	Player.onabort = function()
	{
		console.log('onabort');
	}

	// Never seen it fire yet
	Player.onload = function()
	{
		console.log('onload');
	}

	// Never seen it fire yet 2
	Player.load = function()
	{
		console.log('load');
	}

	Player.onerror = function(error)
	{
		self.eventAudioError();
	}

	/*
	Player.oncanplay = function()
	{
		self.isTrackLoaded = true;
	}

	Player.ondurationchange = function()
	{
		time_total.text(self.formatHumanDuration(this.duration));
	}

	Player.onended = function()
	{
		self.playlistNext();
		self.playbackStart();
	}

	Player.ontimeupdate = function()
	{
		console.log(this.currentTime);
		self.progressSet(this.currentTime / this.duration);
		time_current.text(self.formatHumanDuration(this.currentTime));
	});

	Player.onplay = function()
	{
		console.log('Audio.onplay');
		self.isPlaying = true;

	}

	// Does not seem to work
	Player.onpause = function()
	{
		console.log('Audio.onpause');
		self.isPlaying = false;
	}*/
	this.addEventListener = function(event, callback)
	{
		self.eventListeners[event].push(callback);
	}


	this.controlUpdateProgress = function()
	{
		self.progressSet(Player.currentTime / Player.duration);

		if( isFinite(Player.currentTime) )
			time_current.text(self.formatHumanDuration(Player.currentTime));

		if( isFinite(Player.duration) )
			time_total.text(self.formatHumanDuration(Player.duration));
	}

	this.eventAudioError = function()
	{
		self.playbackStop();
		self.trackUnload();

		track_error.text('Error: ' + Player.src);
	}

	this.eventDurationChanged = function()
	{
		time_total.text(self.formatHumanDuration(Player.duration));
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
		//console.log(Player.seeking);


		if( self.isPlaying )
		{
			self.controlUpdateProgress();

			if( Player.ended )
			{
				self.playlistNext();
			}
		}
	}

	this.playlistNext = function()
	{
		self.playlistSkip(1);
	}

	this.playlistPrev = function()
	{
		return self.playlistSkip(-1);
	}

	this.playlistSeek = function(index)
	{
		var
			wasPlaying = self.isPlaying,
			item;

		if( item = PlayQueue.itemSeek(index) )
			return self.trackLoadItem(item);

		return false;
	}

	this.playlistSkip = function(diff)
	{
		var item;

		if( item = PlayQueue.itemSkip(diff) )
			return self.trackLoadItem(item);
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
	}

	this.playbackStart = function()
	{
		if( !self.isTrackLoaded )
			self.playlistSkip(0);

		Player.play();
		self.isPlaying = true;
		self.eventPlaybackStarted();
	}

	this.playbackStop = function()
	{
		Player.pause();
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
			Player.currentTime = Math.min(seconds, Player.duration);
			self.eventTimeChanged();
		}
	}

	this.triggerEvent = function(event)
	{
		if( self.eventListeners[event] )
			for(var index in self.eventListeners[event] )
				self.eventListeners[event][index]();
	}

	this.trackLoadItem = function(item)
	{
		var
			wasPlaying = self.isPlaying,
			userTrackID,
			title;

		if( !(userTrackID = $(item).data('usertrackid')) )
			return false;

		self.playbackReset();

		if( this.trackLoadURL('/MusicServer.php?userTrackID=' + userTrackID) )
		{
			self.isTrackLoaded = true;

			item.addClass('isCurrent').siblings().removeClass('isCurrent');

			title = item.find('.artist').text() + ' - ' + item.find('.title').text();
			track_title.html(title);

			self.playingArtist = item.data('artist');
			self.playingTitle = item.data('title');

			element.data('playing-artist', self.playingArtist);
			element.data('playing-title', self.playingTitle);

			if( wasPlaying )
				self.playbackStart();

			return true;
		}

		return false;
	}

	this.trackLoadURL = function(url)
	{
		// We really just blindly pass the url to the player. If it is not successful we will catch the error in Player.onerror()
		Player.src = url;

		return true;
	}

	this.trackUnload = function()
	{
		self.isTrackLoaded = false;

		time_current.text('0:00');
		time_total.text('0:00');

		track_title.text('');
		track_artist.text('');
		track_error.text('');

		self.progressSet(0);
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

		self.playbackSeek(Player.duration * (pos / max));
	})
	.on('click', function(e)
	{
		e.preventDefault();
	});

	var refreshLoop = setInterval(self.mainLoop, 100);
}