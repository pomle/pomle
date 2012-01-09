$(function()
{
	var tileMap = $('.brickTile');
	var minTime = 500;
	var devTime = 3000;

	var refreshRandomTile = function()
	{
		var result = BrickTile.randomizeMap(tileMap);
		console.log(result);
		var waitTime = minTime + devTime * Math.random();
		setTimeout(refreshRandomTile, waitTime);
	};

	setTimeout(refreshRandomTile, 5000); // Initial wait
});

