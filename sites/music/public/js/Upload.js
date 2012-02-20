function initDropUpload()
{
	var queueItemTemplate = '<div class="item"><div class="fileName">-</div><div class="progressBar"><div class="progress"></div></div></div>';

	var dropUpload = $('.dropUpload');
	var dropArea = dropUpload.find('.dropArea');

	var queue = dropUpload.find('.queue');
	var messages = dropUpload.find('.messages');
	var form = dropUpload.parent('form');

	dropArea.dropUpload(
	{
		'url': form.attr('action'),
		'fileMeta': function()
		{
			return form.serializeArray();
		},
		'fileParamName': 'file',
		'fileSizeMax': null,
		'onDragEnter': function()
		{
			dropArea.addClass('isHovering');
		},
		'onDragLeave': function()
		{
			dropArea.removeClass('isHovering');
		},
		'onDropSuccess': function()
		{
			messages.html('');
			dropArea.removeClass('isHovering');
		},
		'onProgressUpdated': function(File, progress)
		{
			File.queueItem.find('.progress').css('width', (progress * 100) + '%');
		},
		'onUploadCompleted': function(File)
		{
			File.queueItem.remove();
		},
		'onUploadQueued': function(File)
		{
			var qi = $(queueItemTemplate);
			qi.find('.fileName').html(File.name);
			qi.appendTo(queue);
			File.queueItem = qi;
		},
		'onUploadSucceeded': function(File, response)
		{
			var json = jQuery.parseJSON(response);

			if( json && json.length )
				messages.prepend(json[0].html);
		}
	});
}