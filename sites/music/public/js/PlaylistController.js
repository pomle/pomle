var PlaylistController = function(element)
{
	var self = this;

	var playlist = $(element).find('.userTrackItems');

	this.appendTo = function(userTrackItems)
	{
		playlist.append(userTrackItems);
	}

	this.clear = function()
	{
		playlist.html('');
	}

	this.getItems = function()
	{
		return playlist.find('.item');
	}

	this.replaceWith = function(userTrackItems)
	{
		playlist.html(userTrackItems);
	}

	this.itemCurrent = function()
	{
		var currentIndex, items, item;

		items = self.getItems();

		return items.filter('.isCurrent'); // Return items with class isCurrent. There should always just be one
	}

	this.itemNext = function()
	{
		return self.itemSkip(1);
	}

	this.itemPrev = function()
	{
		return self.itemSkip(-1);
	}

	this.itemSeek = function(index)
	{
		var items = self.getItems();

		if( index < 0 || index >= items.length ) return false;

		return items.eq(index);
	}

	this.itemSkip = function(diff)
	{
		var
			currentIndex,
			newIndex,
			item;

		item = self.itemCurrent();

		currentIndex = item.index();

		// If there is no currentIndex, go to first index
		newIndex = ( currentIndex == -1 ) ? 0 : currentIndex + diff;

		return self.itemSeek(newIndex);
	}

	this.shuffle = function()
	{
		var arr =  playlist.find('.item');

		for(
			var j, x, i = arr.length; i;
			j = parseInt(Math.random() * i),
			x = arr[--i], arr[i] = arr[j], arr[j] = x
		);

		self.replaceWith(arr);
	}
}