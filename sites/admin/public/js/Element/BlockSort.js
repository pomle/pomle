 $(document).ready(function() {

	var item_last_clicked = $();

	$('.blockSort').each(function() {

		var clipboard = $();

		$(this).find('.items>.item').live('click', function(e)
		{
			item_last_clicked = $(this);

			var items = $(this).parent().children('.item');

			if( !e.ctrlKey && !e.shiftKey )
				items.removeClass('selected');

			if( e.shiftKey )
			{
				var index_last = items.filter('.selected').eq(0).index();
				var index_pointer = $(this).index();
				items.slice( Math.min(index_last, index_pointer), Math.max(index_last, index_pointer) + 1 ).addClass('selected');
			}
			else
			{
				$(this).toggleClass('selected');
			}
		});
	});

	$(window).on('keydown', function(e)
	{
		var items = item_last_clicked.parent().children('.item');

		//console.log(e.keyCode);

		var items_selected = items.filter('.selected');

		if( e.keyCode == 46 )
			items_selected.remove();

		if( e.ctrlKey )
		{
			switch(e.keyCode)
			{
				case 67:
					clipboard = items_selected.clone();
					clipboard.find('.itemID').val('');
				break;

				case 88:
					clipboard = items_selected.remove();
				break;

				case 86:
					items_selected.removeClass('selected').eq(0).before(clipboard.addClass('selected'));
					clipboard = clipboard.clone();
					clipboard.find('.sortID').val('');
				break;
			}
		}
	});
});