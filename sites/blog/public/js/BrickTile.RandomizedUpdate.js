$(function()
{
	var tileMap = $('.brickTile');
	var minTime = 300;
	var devTime = 3000;

	var refreshRandomTile = function()
	{
		var result = BrickTile.randomizeMap(tileMap);
		setTimeout(refreshRandomTile, minTime + devTime * Math.random());
	};

	setTimeout(refreshRandomTile, 5000); // Initial wait
});

