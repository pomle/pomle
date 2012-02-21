var PanelController = function()
{
	this.placeLibrary = function(name, placeIn, data)
	{
		Panel.placeURL('/ajax/Panel.php?type=Library&name=' + name, placeIn, data);
	},

	this.placeURL = function(url, placeIn, data)
	{
		if( placeIn.hasClass('isLocked') )
		{
			return false;
			console.log('Destination Panel Locked');
		}

		$.ajax(
		{
			url: url,
			type: 'get',
			data: data,
			complete: function(jqXHR, textStatus)
			{},
			error: function(jqXHR, textStatus, errorThrown)
			{
				alert(textStatus);
			},
			success: function(data, textStatus, jqXHR)
			{
				placeIn.html(data);
			}
		});
	}
}