$(function()
{
	var queueItemTemplate = '<div class="item"><div class="fileName">-</div><div class="progressBar"><div class="progress"></div></div></div>';

	var upload = $('#upload');
	var dropArea = upload.find('.dropArea');

	var queue = upload.find('.queue');
	var messages = upload.find('.messages');
	var form = upload.find('form');

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
		'onFileCompleted': function(File)
		{
			File.queueItem.remove();
		},
		'onFileQueued': function(File)
		{
			var qi = $(queueItemTemplate);
			qi.find('.fileName').html(File.name);
			qi.appendTo(queue);
			File.queueItem = qi;
		},
		'onFileSucceeded': function(File, response)
		{
			var json = jQuery.parseJSON(response);

			if( json && json.length && json[0].html )
			{
				var message = $(json[0].html);
				messages.prepend(message);
				setTimeout(function() { message.fadeOut(); }, 3000);
			}
		},
		'onProgressUpdated': function(File, progress)
		{
			File.queueItem.find('.progress').css('width', (progress * 100) + '%');
		},
		'onQueueCompleted': function()
		{
			console.log('Queue Complete');
			upload.removeClass('extended');
		}
	});

	$(document)
		.on('dragenter', function(e) {
			upload.addClass('extended').addClass('locked');
		})
		;
});