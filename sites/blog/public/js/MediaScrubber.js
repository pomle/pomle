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
	var E = $(mediaScrubber); // The jQuery element
	var M = this;
	var B = E.find('.busy');

	var T = null;
	var S = null;

	var Control = E.find('.control');
	var Canvas = E.find('.image');
	var Caption = Control.find('.caption');

	this.mediaPool = jQuery.parseJSON(E.attr('data-mediaPool'));
	this.index = parseInt(E.attr('data-pageIndex'), 10);
	this.length = this.mediaPool.length;

	this.indexRequested = this.index;
	this.indexDisplaying = this.index;

	this.isFetching = false;
	this.isPlaying = false;

	this.displayMedia = function(Media)
	{
		try
		{
			if( !Media.url ) throw('Media.url not set');

			Caption.html(Media.caption || '');

			M.updateCanvas(Media.url);
		}
		catch (error)
		{
			alert(error);
		}
	};

	this.fetchMedia = function(Media, displayOnComplete)
	{
		if( Media && !Media.url && !M.isFetching )
		{
			M.isFetching = true;

			jQuery.ajax(
			{
				url: '/helpers/mediaGen/MediaScrubber.php',
				type: 'get',
				dataType: 'json',
				data: Media,
				complete: function()
				{
					M.isFetching = false;
					if( displayOnComplete ) M.updateMedia();
				},
				error: function(x, textStatus)
				{
					displayOnComplete = false;
					M.removeHash(Media.mediaHash);
				},
				success: function(Media_Completed)
				{
					if( Media_Completed.url )
						Media.url = Media_Completed.url;
					else
						M.removeHash(Media.mediaHash);
				}
			});
		}

		return this;
	};

	this.findIndex = function(pointer)
	{
		if( M.length == 0 ) return false;

		var index = pointer % M.length;
		if(index < 0) index += M.length;
		return index;
	};

	this.go = function(diff)
	{
		M.seekDiff(diff);
		M.updateMedia();
		return this;
	};

	this.goTo = function(index)
	{
		M.seekIndex(index);
		M.updateMedia();
		return this;
	};

	this.next = function()
	{
		M.seekNext();
		M.updateMedia();
		return this;
	};

	this.prev = function()
	{
		M.seekPrev();
		M.updateMedia();
		return this;
	};

	this.removeHash = function(mediaHash)
	{
		for(key in M.mediaPool)
		{
			if( M.mediaPool[key].mediaHash == mediaHash )
			{
				M.mediaPool.splice(key, 1);
				M.length = M.mediaPool.length;
				break;
			}
		}
		return this;
	};

	this.seekDiff = function(diff)
	{
		return M.seekIndex(M.index + diff);
	};

	this.seekIndex = function(index)
	{
		index = M.findIndex(index);

		if( index !== false )
			M.index = index;

		return this;
	};

	this.seekNext = function()
	{
		return M.seekDiff(1);
	};

	this.seekPrev = function()
	{
		return M.seekDiff(-1);
	};

	this.seekTo = function(mediaHash)
	{
		return false; // Deprecated

		var i = 0;
		for(index in M.hashPool)
		{
			if( M.hashPool[index] == mediaHash )
			{
				M.seekIndex(i);
				break;
			}
			i++;
		}
		return this;
	};

	this.slideshowPlay = function()
	{
		M.isPlaying = true;
		E.addClass('isPlaying');
		M.next();
	}

	this.slideshowStop = function()
	{
		clearTimeout(S);
		E.removeClass('isPlaying');
		M.isPlaying = false;
	}

	this.slideshowToggle = function()
	{
		if( M.isPlaying )
			M.slideshowStop();
		else
			M.slideshowPlay();
	}

	this.updateBusy = function()
	{
		if( M.indexRequested == M.indexDisplaying )
		{
			B.fadeOut('fast');
			clearTimeout(T);
		}
		else
			B.fadeIn('fast');

		return this;
	}

	this.updateCanvas = function(mediaURL)
	{
		Canvas.fadeOut(25, (function() {
			var Buffer = new Image();
			Buffer.onload = function()
			{
				Canvas.css('background-image', 'url(' + this.src + ')').fadeIn(150);
				Control.find('.mediaURL').attr('href', this.src);

				if( M.isPlaying ) S = setTimeout(M.next, 5000);
			}
			Buffer.src = mediaURL;
			return this;
		}));


	}

	this.updateMedia = function()
	{
		T = setTimeout(M.updateBusy, 1000);

		Control.find('.pageIndex').text((M.index + 1) + ' / ' + (M.length));

		if( !M.mediaPool[M.index] )
			return false;

		var Media = M.mediaPool[M.index];

		M.indexRequested = M.index;

		if( !Media.url )
			return M.fetchMedia(Media, true);

		this.displayMedia(Media);

		M.indexDisplaying = this.index;

		this.updateBusy();

		return this;
	}
}