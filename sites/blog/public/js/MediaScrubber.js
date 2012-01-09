$(function()
{
	$('.mediaDock').each(function() {
		var M = new MediaDock(this);

		$(this).find('.next,.prev').bind('click', function(e)
		{
			e.preventDefault();
			M.go(parseInt(this.rel, 10));
		});

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

function MediaDock(mediaDock)
{
	var E = $(mediaDock); // The jQuery element
	var M = this;
	var B = E.find('.busy');

	var T = null;

	var Control = E.find('.control');
	var Canvas = E.find('.image');

	this.hashPool = jQuery.parseJSON(E.attr('data-mediaHashs'));
	this.mediaPool = {};
	this.index = parseInt(E.attr('data-pageIndex'), 10);
	this.length = this.hashPool.length;

	this.indexRequested = this.index;
	this.indexDisplaying = this.index;

	this.isFetching = false;

	this.displayMedia = function(Media)
	{
		try
		{
			//var txtCaption = Media.caption ? Media.caption : '';
			if( !Media.mediaURL ) throw('Media.url not set');

			/*if( Media.mediaID )
				directLink.attr('href', '#mediaID:' + Media.mediaID).show();
			else
				directLink.attr('href', '#mediaID:').hide();*/
			this.updateCanvas(Media.mediaURL);
		}
		catch (error)
		{
			alert(error);
		}
	};

	this.fetchMedia = function(mediaHash, displayOnComplete)
	{
		if( mediaHash && !M.isFetching )
		{
			M.isFetching = true;

			var dataSet = {'mediaHash': mediaHash};
			jQuery.ajax(
			{
				url: '/helpers/mediaGen/MediaScrubber.php',
				type: 'get',
				dataType: 'json',
				data: dataSet,
				complete: function()
				{
					M.isFetching = false;
					if( displayOnComplete ) M.updateMedia();
				},
				error: function(x, textStatus)
				{
					displayOnComplete = false;
					M.removeHash(mediaHash);
				},
				success: function(media)
				{
					if( media[mediaHash] )
						M.mediaPool[mediaHash] = media[mediaHash];
					else
						M.removeHash(mediaHash);
				}
			});
		}

		return this;
	};

	this.findIndex = function(pointer)
	{
		if( this.length == 0 ) return false;

		var index = pointer % this.length;
		if(index < 0) index += this.length;
		return index;
	};

	this.go = function(diff)
	{
		this.seekDiff(diff);
		this.updateMedia();
		return this;
	};

	this.goTo = function(index)
	{
		this.seekIndex(index);
		this.updateMedia();
		return this;
	};

	this.next = function()
	{
		this.seekNext();
		this.updateMedia();
		return this;
	};

	this.prev = function()
	{
		this.seekPrev();
		this.updateMedia();
		return this;
	};

	this.removeHash = function(mediaHash)
	{
		for(key in this.hashPool)
		{
			if( this.hashPool[key] == mediaHash )
			{
				this.hashPool.splice(key, 1);
				this.length = this.hashPool.length;
				break;
			}
		}
		return this;
	};

	this.seekDiff = function(diff)
	{
		return this.seekIndex(this.index + diff);
	};

	this.seekIndex = function(index)
	{
		index = this.findIndex(index);

		if( index !== false )
			this.index = index;

		return this;
	};

	this.seekNext = function()
	{
		return this.seekDiff(1);
	};

	this.seekPrev = function()
	{
		return this.seekDiff(-1);
	};

	this.seekTo = function(mediaHash)
	{
		var i = 0;
		for(index in this.hashPool)
		{
			if( this.hashPool[index] == mediaHash )
			{
				this.seekIndex(i);
				break;
			}
			i++;
		}
		return this;
	};

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
		var Buffer = new Image();
		Buffer.onload = function()
		{
			Canvas.css('background-image', 'url(' + this.src + ')');
			Control.find('.mediaURL').attr('href', this.src);
		}
		Buffer.src = mediaURL;
		return this;
	}

	this.updateMedia = function()
	{
		T = setTimeout(M.updateBusy, 500);

		Control.find('.pageIndex').text((this.index + 1) + ' / ' + (this.length));

		var mediaHash = this.hashPool[this.index];

		M.indexRequested = this.index;

		if( !this.mediaPool[mediaHash] )
			return this.fetchMedia(mediaHash, true);

		this.displayMedia(this.mediaPool[mediaHash]);

		M.indexDisplaying = this.index;

		this.updateBusy();

		return this;
	}
}