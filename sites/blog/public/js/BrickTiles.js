$(document).ready(function() {

	$('.bricktiles').each(function() {

		var tiles = $(this).find('.tile[data-mediapool]');

		var array_rand = function(a)
		{
			return a[Math.floor(Math.random()*a.length)];
		}

		var switcher = function()
		{
			var tile = $(array_rand(tiles));

			var image = tile.find('img');

			var hashPool = jQuery.parseJSON(tile.attr('data-mediapool'));

			var mediaHash = array_rand(hashPool);

			jQuery.getJSON(
				'/helpers/mediaGen/BrickTile.php?mediaHash=' + mediaHash,
				null,
				function(imageURL, textStatus, jqXHR)
				{
					//console.log(imageURL);
					if( imageURL )
					{
						//console.log('Loading image: ' + imageURL);
						preLoad = new Image();
						preLoad.onload = function()
						{
							//console.log('Image Loaded: ' + imageURL + ', Displaying');
							image.hide().attr('src', imageURL).fadeIn();
						}
						preLoad.src = imageURL;
					}
				}
			);

			setTimeout(switcher, (0 + (Math.random() * 3000)));
		}

		setTimeout(switcher, 1000);
	});
});