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

	cycleTile: function(tile)
	{
		var hashPool, cycleIndex;

		if( !tile ) return false;

		if( !(hashPool = this.getTileHashPool(tile)) )
			return false;

		if( !(cycleIndex = tile.data('cycleIndex')) )
			cycleIndex = 0;

		this.updateTileHash(tile, hashPool[cycleIndex]);

		cycleIndex = (cycleIndex + 1) % hashPool.length;

		tile.data('cycleIndex', cycleIndex);

		return true;
	},

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

	getTileHashPool: function(tile)
	{
		if( !tile ) return false;

		var hashPoolJSON;
		if( !(hashPoolJSON = tile.attr('data-mediapool')) ) return false;

		var hashPool = jQuery.parseJSON(hashPoolJSON);

		if( !hashPool ) return false; // parseJSON failed or no mediaHashes in pool

		return hashPool;
	},

	getViewportTiles: function(tiles)
	{
		var w = $(window);
		var viewport = {top: w.scrollTop(), bottom: w.scrollTop() + w.height()};

		return tiles.filter
		(
			function(index)
			{
				var item = $(this);
				var element = {top: item.offset().top, bottom: item.offset().top + item.height()};
				return (viewport.bottom > element.top && viewport.top < element.bottom);
			}
		);
	},

	randomizeMap: function(brickTile)
	{
		var tiles = brickTile.find('.item').filter('[data-mediapool]');

		//console.log(tiles);

		if( tiles.length == 0 ) return false;

		tiles = this.getViewportTiles(tiles); // Remove tiles outside of viewport

		if( tiles.length == 0 ) return false;

		return this.cycleTile(this.getRandomTile(tiles));
	},

	randomizeTile: function(tile, onComplete)
	{
		var hashPool = this.getTileHashPool(tile);

		var mediaHash = this.getArrayRandom(hashPool);

		if( !mediaHash ) return false;

		this.updateTileHash(tile, mediaHash);

		return true;
	},

	updateTileHash: function(tile, mediaHash)
	{
		if( this.ajaxRunning ) return false;

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