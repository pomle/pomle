$(function()
{
	$('.brickTile .layoutSwitch').bind('click', function(e) {
		e.preventDefault();
		var brickTile = $('.brickTile');
		brickTile.find('.items').fadeOut(
			'fast',
			function()
			{
				$(this).parent('.brickTile').toggleClass('matrix').toggleClass('list');
				$(this).fadeIn();
			}
		);

		$.get($(this).attr('href'));
	});
})


var BrickTile =
{
	banned: {},
	fetched: {},
	images: {},
	ajaxRunning: false,

	getArrayRandom: function(a)
	{
		if( a.length == 0 ) return false;
		var pointer = Math.random() * a.length;
		return a[Math.floor(pointer)];
	},

	getRandomTile: function(tiles)
	{
		return $(this.getArrayRandom(tiles));
	},

	getViewportTiles: function(tiles)
	{
		var w = $(window);
		var boundary = [w.scrollTop(), w.scrollTop() + w.height()];

		return tiles.filter
		(
			function(index)
			{
				var overlap = [this.offsetTop, this.offsetTop + this.offsetHeight];
				return (boundary[1] > overlap[0]) && (boundary[0] < overlap[1]);
			}
		);
	},

	randomizeMap: function(brickTile)
	{
		var tiles = brickTile.find('.item').filter('[data-mediapool!="[]"]');

		if( tiles.length == 0 ) return false;

		tiles = this.getViewportTiles(tiles); // Remove tiles outside of viewport

		return this.randomizeTile(this.getRandomTile(tiles));
	},

	randomizeTile: function(tile, onComplete)
	{
		if( !tile ) return false;

		if( this.ajaxRunning ) return false;

		var hashPoolJSON;
		if( !(hashPoolJSON = tile.attr('data-mediapool')) ) return false;

		var imgTag = tile.find('img');
		var hashPool = jQuery.parseJSON(hashPoolJSON);

		if( !hashPool ) return false; // parseJSON failed or no mediaHashes in pool

		var mediaHash = this.getArrayRandom(hashPool);

		if( !mediaHash ) return false;

		//console.log(mediaHash);

		if( this.banned[mediaHash] )
			return false; // This hash was unsuccessful once, so we don't grab it again

		if( this.fetched[mediaHash] )
			return this.updateTile(tile, this.fetched[mediaHash]);

		this.ajaxRunning = true;
		jQuery.ajax
		({
			url: '/helpers/mediaGen/BrickTile.php?mediaHash=' + mediaHash,
			dataType: 'json',
			complete: function()
			{
				BrickTile.ajaxRunning = false;
			},
			error: function(a, textError, b)
			{
				//console.log(textError);
				BrickTile.banned[mediaHash] = true;
			},
			success: function(url)
			{
				if( !url ) return false;
				BrickTile.updateTile(tile, url);
				BrickTile.fetched[mediaHash] = url;
			}
		});

		return true;
	},

	updateTile: function(tile, url, onComplete)
	{
		var imgTag = tile.find('img');

		var updateImage = function()
		{
			imgTag.hide().attr('src', this.src).fadeIn();
			//if(onComplete) onComplete();
			return true;
		};

		if( this.images[url] )
		{
			//console.log("Cached!!!");
			return this.images[url].onload();
		}

		this.images[url] = new Image();
		this.images[url].onload = updateImage;
		this.images[url].src = url;
	}
}