$(function()
{
	$('.mediaScrubber').each(function()
	{
		var M = new MediaScrubber(this);
		var skipSteps = Math.max(5, Math.floor(M.length / 10));

		$(this).bind('play',
			function() { M.slideshowPlay(); }
		).bind('stop',
			function() { M.slideshowStop(); }
		).bind('toggle',
			function() { M.slideshowToggle(); }
		);

		$(this).find('.next,.prev').bind('click', function(e)
		{
			e.preventDefault();
			M.go(parseInt(this.rel, 10));
		});

		$(this).find('.slideshowToggle').click(function(e) { e.preventDefault(); $(this).trigger('toggle'); });

		$(document).keydown(function(e)
		{
			switch(e.keyCode)
			{
				case 37: // Left Arrow
					e.preventDefault();
					M.prev();
				break;

				case 39: // Right Arrow
					e.preventDefault();
					M.next();
				break;

				case 34: // Page Down
					e.preventDefault();
					M.go(skipSteps);
				break;

				case 33: // Page Up
					e.preventDefault();
					M.go(-skipSteps);
				break;

				case 36: // Home
					e.preventDefault();
					M.goTo(0);
				break;

				case 35: // End
					e.preventDefault();
					M.goTo(M.length - 1);
				break;
			}
		});
	});
});

function MediaScrubber(mediaScrubber)
{
	var self = this;

	var element_self = $(mediaScrubber); // The jQuery element
	var element_Busy = element_self.find('.busy');

	var timer_Busy = null;
	var timer_Slideshow = null;

	var element_Control = element_self.find('.control');
	var element_Canvas = element_self.find('.image');
	var element_Caption = element_Control.find('.caption');

	this.mediaPool = jQuery.parseJSON(element_self.attr('data-mediaPool'));
	this.index = parseInt(element_self.attr('data-pageIndex'), 10);
	this.length = this.mediaPool.length;

	this.indexRequested = this.index;
	this.indexDisplaying = this.index;

	this.isFetching = false;
	this.isPlaying = false;

	this.slideshowDelay = 5000;

	this.displayMedia = function(Media)
	{
		try
		{
			if( !Media.url ) throw('Media.url not set');

			self.updateCanvas(Media);
		}
		catch (error)
		{
			alert(error);
		}
	};

	this.fetchMedia = function(Media, displayOnComplete)
	{
		if( Media && !Media.url && !self.isFetching )
		{
			self.isFetching = true;

			jQuery.ajax(
			{
				url: '/helpers/mediaGen/MediaScrubber.php',
				type: 'get',
				dataType: 'json',
				data: Media,
				complete: function()
				{
					self.isFetching = false;
					if( displayOnComplete )
						self.updateMedia();
				},
				error: function(x, textStatus)
				{
					displayOnComplete = false;
					self.removeHash(Media.mediaHash);
				},
				success: function(Media_Completed)
				{
					if( Media_Completed.url )
						Media.url = Media_Completed.url;
					else
						self.removeHash(Media.mediaHash);
				}
			});
		}

		return this;
	};

	this.findIndex = function(pointer)
	{
		if( self.length == 0 ) return false;

		var index = pointer % self.length;
		if(index < 0) index += self.length;
		return index;
	};

	this.go = function(diff)
	{
		self.seekDiff(diff);
		self.updateMedia();
		return this;
	};

	this.goTo = function(index)
	{
		self.seekIndex(index);
		self.updateMedia();
		return this;
	};

	this.next = function()
	{
		self.seekNext();
		self.updateMedia();
		return this;
	};

	this.prev = function()
	{
		self.seekPrev();
		self.updateMedia();
		return this;
	};

	this.removeHash = function(mediaHash)
	{
		for(key in self.mediaPool)
		{
			if( self.mediaPool[key].mediaHash == mediaHash )
			{
				self.mediaPool.splice(key, 1);
				self.length = self.mediaPool.length;
				break;
			}
		}
		return this;
	};

	this.seekDiff = function(diff)
	{
		return self.seekIndex(self.index + diff);
	};

	this.seekIndex = function(index)
	{
		index = self.findIndex(index);

		if( index !== false )
			self.index = index;

		return this;
	};

	this.seekNext = function()
	{
		return self.seekDiff(1);
	};

	this.seekPrev = function()
	{
		return self.seekDiff(-1);
	};

	this.seekTo = function(mediaHash)
	{
		return false; // Deprecated

		var i = 0;
		for(index in self.hashPool)
		{
			if( self.hashPool[index] == mediaHash )
			{
				self.seekIndex(i);
				break;
			}
			i++;
		}
		return this;
	};

	this.slideshowPlay = function()
	{
		clearTimeout(timer_Slideshow);

		self.isPlaying = true;
		element_self.addClass('isPlaying');
		self.next();
	}

	this.slideshowStop = function()
	{
		clearTimeout(timer_Slideshow);

		element_self.removeClass('isPlaying');
		self.isPlaying = false;
	}

	this.slideshowToggle = function()
	{
		if( self.isPlaying )
			self.slideshowStop();
		else
			self.slideshowPlay();
	}

	this.updateBusy = function()
	{
		if( self.indexRequested == self.indexDisplaying )
		{
			element_self.removeClass('isBusy');
			clearTimeout(timer_Busy);
		}
		else
			element_self.addClass('isBusy');

		return this;
	}

	this.updateCanvas = function(Media)
	{
		element_Caption.html('');
		var Buffer = new Image();
		Buffer.onload = function()
		{
			element_Canvas.fadeOut(self.isPlaying ? 150 : 0, (function()
			{
				element_Canvas.css('background-image', 'url(' + Media.url + ')').fadeIn(self.isPlaying ? 250 : 100);
				element_Control.find('.mediaURL').attr('href', Media.url);
				element_Control.find('.mediaDownloadURL').attr('href', '/helpers/MediaDownload.php?mediaID=' + Media.mediaID);
				element_Caption.html(Media.caption);
			}));
		}
		Buffer.src = Media.url;
		return this;
	}

	this.updateMedia = function()
	{
		timer_Busy = setTimeout(self.updateBusy, 1000);

		element_Control.find('.pageIndex').text((self.index + 1) + ' / ' + (self.length));

		if( !self.mediaPool[self.index] )
			return false;

		var Media = self.mediaPool[self.index];

		self.indexRequested = self.index;

		if( !Media.url )
			return self.fetchMedia(Media, true);

		clearTimeout(timer_Slideshow);

		self.displayMedia(Media);

		self.indexDisplaying = self.index;

		self.updateBusy();

		if( self.isPlaying ) timer_Slideshow = setTimeout(self.slideshowPlay, self.slideshowDelay);

		return this;
	}
}