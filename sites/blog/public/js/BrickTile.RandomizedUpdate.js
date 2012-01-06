$(function()
{
	var tileMap = $('.brickTile');
	var minTime = 1000;
	var devTime = 1000;

	var refreshRandomTile = function()
	{
		var result = BrickTile.randomizeMap(tileMap);
		setTimeout(refreshRandomTile, minTime + devTime * Math.random());
	};

	setTimeout(refreshRandomTile, 1000); // Initial wait
});

