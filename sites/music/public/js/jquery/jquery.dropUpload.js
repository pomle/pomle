var dropUploadQueue = [];

(function( $ ){
	jQuery.event.props.push("dataTransfer");

	var
		isLoopRunning = false,
		loopSize = 0;

	var emptyCallback = function() {};

	var
		settings = {},
		default_settings = {
			'fileDropCountMax': null,
			'fileMeta': emptyCallback,
			'fileParamName': 'dropUploadFile',
			'fileSimTransfers': 1,
			'fileSizeMax': null,

			'onComplete': emptyCallback,
			'onDropError': emptyCallback,
			'onDropSuccess': emptyCallback,
			'onDragEnter': emptyCallback,
			'onDragOver': emptyCallback,
			'onDragLeave': emptyCallback,

			'onProgressUpdated': emptyCallback,

			'onUploadCompleted': emptyCallback,
			'onUploadFailed': function(File, message)
			{
				alert(message);
			},
			'onUploadQueued': emptyCallback,
			'onUploadSucceeded': emptyCallback,
			'onUploadStarted': emptyCallback,

			'url': ''
			},
			queue = dropUploadQueue;

	var eventDrop = function(e)
	{
		e.preventDefault();

		try
		{
			if( !e.dataTransfer.files || e.dataTransfer.files.length == 0 )
				throw("No files in array");

			var FileList = e.dataTransfer.files;

			if( settings.fileDropCountMax && FileList.length > settings.fileDropCountMax )
				throw("Too many files");

			settings.onDropSuccess();
		}
		catch(e)
		{
			settings.onDropError(e.message);
			return false;
		}


		filesIterator(FileList, function(File) {
			if( isFileAccepted(File) ) queueFile(File);
		});


		if( !isLoopRunning )
			uploadLoopEngage();

		return true;
	}

	var eventDragEnter = function(e)
	{
		//e.preventDefault();
		settings.onDragEnter();
	}

	var eventDragLeave = function(e)
	{
		//e.preventDefault();
		settings.onDragLeave();
	}

	var eventDragOver = function(e)
	{
		e.preventDefault();
		settings.onDragOver();
	}

	// Just a method that disables default browser behavior for certian events
	var eventKillDefault = function(e)
	{
		e.preventDefault();
		return false;
	}

	// Lets us iterate over file lists in a consistent manner
	var filesIterator = function(FileList, callback)
	{
		for(var index = 0; index < FileList.length; index++)
			callback(FileList[index]);

		return true;
	}

	var isFileAccepted = function(File)
	{
		if( settings.fileSizeMax &&  (File.size > settings.fileSizeMax) )
			return false;

		return true;
	}

	var queueFile = function(File)
	{
		var fileMeta;

		File.meta = settings.fileMeta() || {};

		settings.onUploadQueued(File);

		queue.push(File);
	}

	var uploadLoopEngage = function()
	{
		isLoopRunning = true;

		while(queue.length > 0 && loopSize < settings.fileSimTransfers)
		{
			var File = queue.shift();

			try
			{
				uploadFile(File, uploadLoopEngage);
			}
			catch(e)
			{
				settings.onUploadFailed(File, e.message);
			}
		}

		isLoopRunning = false;
	}

	var uploadFile = function(File, onCompleteCallback)
	{
		loopSize++;

		var File = File;
		var FR = new FileReader();

		// Defines the call that is made when upload has stopped occuring
		var uploadFinished = function()
		{
			loopSize--;

			settings.onUploadCompleted(File);

			if( typeof onCompleteCallback == 'function' )
				onCompleteCallback();
		}

		FR.File = File;
		FR.onload = function(e)
		{
			var
				boundary	= '---------------------------7d01ecf406a6'; // Boundary should be a string that is unlikely to occur by chance in the data stream
				dashdash	= '--',
				crlf		= '\r\n',
				data		= '';

			// Instruction for data generation taken from http://www.paraesthesia.com/archive/2009/12/16/posting-multipartform-data-using-.net-webrequest.aspx
			/*
				Generate a "boundary." A boundary is a unique string that serves as a delimiter between each of the form values you'll be sending in your request. Usually these boundaries look something like
					---------------------------7d01ecf406a6
				with a bunch of dashes and a unique value.

				Set the request content type to multipart/form-data; boundary= and your boundary, like:
					multipart/form-data; boundary=---------------------------7d01ecf406a6

				Any time you write a standard form value to the request stream, you'll write:
					Two dashes.
					Your boundary.
					One CRLF (\r\n).
					A content-disposition header that tells the name of the form field you'll be inserting. That looks like:
						Content-Disposition: form-data; name="yourformfieldname"
					Two CRLFs.
					The value of the form field - not URL encoded.
					One CRLF.

				Any time you write a file to the request stream (for upload), you'll write:
					Two dashes.
					Your boundary.
					One CRLF (\r\n).
					A content-disposition header that tells the name of the form field corresponding to the file and the name of the file. That looks like:
						Content-Disposition: form-data; name="yourformfieldname"; filename="somefile.jpg"
					One CRLF.
					A content-type header that says what the MIME type of the file is. That looks like:
					Content-Type: image/jpg
					Two CRLFs.
					The entire contents of the file, byte for byte. It's OK to include binary content here. Don't base-64 encode it or anything, just stream it on in.
					One CRLF.

				At the end of your request, after writing all of your fields and files to the request, you'll write:
					Two dashes.
					Your boundary.
					Two more dashes.
			*/

			// Meta
			$.each(this.File.meta, function(index, meta)
			{
				data += dashdash + boundary + crlf;
				data += 'Content-Disposition: form-data; name="' + meta.name + '"' + crlf + crlf;
				data += meta.value;
				data += crlf;
			});

			// Binary
			data += dashdash + boundary + crlf;
			data += 'Content-Disposition: form-data; name="' + settings.fileParamName + '"; filename="' + File.name + '"' + crlf;
			data += 'Content-Type: ' + File.type + crlf + crlf;
			data += e.target.result; // e.target.result is the binary data that FileReader() provides
			data += crlf;

			// End delimiter
			data += dashdash + boundary + dashdash;


			var XHR = new XMLHttpRequest();
			XHR.open("POST", settings.url, true);
			XHR.setRequestHeader('Content-Type', 'multipart/form-data; boundary=' + boundary);
			//XHR.File = File;


			XHR.onerror = function(e)
			{
				settings.onUploadFailed(File);
				uploadFinished();
			};

			XHR.onload = function(e)
			{
				settings.onUploadSucceeded(File, this.responseText);
				uploadFinished();
			};

			XHR.upload.onprogress = function(e)
			{
				if (e.lengthComputable)
					settings.onProgressUpdated(File, e.loaded / e.total);
			};

			XHR.sendAsBinary(data);
		}

		FR.readAsBinaryString(File);
	}

	var methods = {
		init: function( options ) {

			// This seems to extend the XMLHttpRequest object
			if( !XMLHttpRequest.prototype.sendAsBinary )
			{
				XMLHttpRequest.prototype.sendAsBinary = function(datastr)
				{
					var byteValue = function(x)
					{
						return x.charCodeAt(0) & 0xff;
					}

					var ords = Array.prototype.map.call(datastr, byteValue);
					var ui8a = new Uint8Array(ords);
					this.send(ui8a.buffer);
				}
			}


			settings = $.extend(default_settings, options);

			return this.each(function(){

				$(this)
					.bind('drop.filedrop', eventDrop)
					.bind('dragover.filedrop', eventDragOver)
					//.find('*').andSelf()
					.bind('dragenter.filedrop', eventDragEnter)
					.bind('dragleave.filedrop', eventDragLeave);



				// I think this is to prevent the browser from opening the file
				$(document)
					.bind('drop.filedrop', eventKillDefault)
					.bind('dragenter.filedrop', eventKillDefault)
					.bind('dragover.filedrop', eventKillDefault)
					.bind('dragleave.filedrop', eventKillDefault);
			});
		},
		destroy: function()
		{
			return this.each(function(){
				$(window).unbind('.dropUpload');
			});
		}
	};

	$.fn.dropUpload = function( method ) {

		// Method calling logic
		if ( methods[method] ) {
			return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return methods.init.apply( this, arguments );
		} else {
			$.error( 'Method ' +  method + ' does not exist on jQuery.dropUpload' );
		}
	};

})( jQuery );