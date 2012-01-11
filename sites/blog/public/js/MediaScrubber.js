$(function()
{
	$('.mediaScrubber').each(function()
	{
		var M = new MediaScrubber(this);

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

		$(this).find('.slideshowToggle').click(function() { $(this).trigger('toggle'); });

		$(document).keydown(function(e)
		{
			switch(e.keyCode)
			{
				case 37:
					e.preventDefault();
					M.prev();
				break;

				case 39:
					e.preventDefault();
					M.next();
				break;
			}
		});
	});
});

function MediaScrubber(mediaScrubber)
{
	var Self = this;

	var element_Self = $(mediaScrubber); // The jQuery element
	var element_Busy = element_Self.find('.busy');

	var timer_Busy = null;
	var timer_Slideshow = null;

	var element_Control = element_Self.find('.control');
	var element_Canvas = element_Self.find('.image');
	var element_Caption = element_Control.find('.caption');

	this.mediaPool = jQuery.parseJSON(element_Self.attr('data-mediaPool'));
	this.index = parseInt(element_Self.attr('data-pageIndex'), 10);
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

			Self.updateCanvas(Media);
		}
		catch (error)
		{
			alert(error);
		}
	};

	this.fetchMedia = function(Media, displayOnComplete)
	{
		if( Media && !Media.url && !Self.isFetching )
		{
			Self.isFetching = true;

			jQuery.ajax(
			{
				url: '/helpers/mediaGen/MediaScrubber.php',
				type: 'get',
				dataType: 'json',
				data: Media,
				complete: function()
				{
					Self.isFetching = false;
					if( displayOnComplete ) Self.updateMedia();
				},
				error: function(x, textStatus)
				{
					displayOnComplete = false;
					Self.removeHash(Media.mediaHash);
				},
				success: function(Media_Completed)
				{
					if( Media_Completed.url )
						Media.url = Media_Completed.url;
					else
						Self.removeHash(Media.mediaHash);
				}
			});
		}

		return this;
	};

	this.findIndex = function(pointer)
	{
		if( Self.length == 0 ) return false;

		var index = pointer % Self.length;
		if(index < 0) index += Self.length;
		return index;
	};

	this.go = function(diff)
	{
		Self.seekDiff(diff);
		Self.updateMedia();
		return this;
	};

	this.goTo = function(index)
	{
		Self.seekIndex(index);
		Self.updateMedia();
		return this;
	};

	this.next = function()
	{
		Self.seekNext();
		Self.updateMedia();
		return this;
	};

	this.prev = function()
	{
		Self.seekPrev();
		Self.updateMedia();
		return this;
	};

	this.removeHash = function(mediaHash)
	{
		for(key in Self.mediaPool)
		{
			if( Self.mediaPool[key].mediaHash == mediaHash )
			{
				Self.mediaPool.splice(key, 1);
				Self.length = Self.mediaPool.length;
				break;
			}
		}
		return this;
	};

	this.seekDiff = function(diff)
	{
		return Self.seekIndex(Self.index + diff);
	};

	this.seekIndex = function(index)
	{
		index = Self.findIndex(index);

		if( index !== false )
			Self.index = index;

		return this;
	};

	this.seekNext = function()
	{
		return Self.seekDiff(1);
	};

	this.seekPrev = function()
	{
		return Self.seekDiff(-1);
	};

	this.seekTo = function(mediaHash)
	{
		return false; // Deprecated

		var i = 0;
		for(index in Self.hashPool)
		{
			if( Self.hashPool[index] == mediaHash )
			{
				Self.seekIndex(i);
				break;
			}
			i++;
		}
		return this;
	};

	this.slideshowPlay = function()
	{
		clearTimeout(timer_Slideshow);

		Self.isPlaying = true;
		element_Self.addClass('isPlaying');
		Self.next();
	}

	this.slideshowStop = function()
	{
		clearTimeout(timer_Slideshow);

		element_Self.removeClass('isPlaying');
		Self.isPlaying = false;
	}

	this.slideshowToggle = function()
	{
		if( Self.isPlaying )
			Self.slideshowStop();
		else
			Self.slideshowPlay();
	}

	this.updateBusy = function()
	{
		if( Self.indexRequested == Self.indexDisplaying )
		{
			element_Busy.fadeOut('fast');
			clearTimeout(timer_Busy);
		}
		else
			element_Busy.fadeIn('fast');

		return this;
	}

	this.updateCanvas = function(Media)
	{
		element_Caption.html('');
		var Buffer = new Image();
		Buffer.onload = function()
		{
			element_Canvas.fadeOut(25, (function()
			{
				element_Canvas.css('background-image', 'url(' + Media.url + ')').fadeIn(150);
				element_Control.find('.mediaURL').attr('href', Media.url);
				element_Caption.html(Media.caption);
			}));
		}
		Buffer.src = Media.url;
		return this;
	}

	this.updateMedia = function()
	{
		timer_Busy = setTimeout(Self.updateBusy, 1000);

		element_Control.find('.pageIndex').text((Self.index + 1) + ' / ' + (Self.length));

		if( !Self.mediaPool[Self.index] )
			return false;

		var Media = Self.mediaPool[Self.index];

		Self.indexRequested = Self.index;

		if( !Media.url )
			return Self.fetchMedia(Media, true);

		clearTimeout(timer_Slideshow);

		this.displayMedia(Media);

		Self.indexDisplaying = this.index;

		this.updateBusy();

		if( Self.isPlaying ) timer_Slideshow = setTimeout(Self.slideshowPlay, Self.slideshowDelay);

		return this;
	}
}