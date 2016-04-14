var FormDropzone = function () {
	return {
		//main function to initiate the module
		init: function () {  
			var myDropzone = new Dropzone("#my-dropzone", {
				maxFilesize: 2,
				uploadMultiple: false,
				maxFiles: 1,
				acceptedFiles: ".jpeg,.jpg,.png,.gif, .pdf, .doc, .docx, .zip"        		
			});

			myDropzone.on("addedfile", function(file) {
				// Create the remove button
				var removeButton = Dropzone.createElement("<button class='btn btn-sm btn-block red'><i class='fa fa-trash'></i></button>");

				// Capture the Dropzone instance as closure.
				var _this = this;
				// Listen to the click event
				removeButton.addEventListener("click", function(e) {
					// Make sure the button click doesn't submit the form:
					e.preventDefault();
					e.stopPropagation();
					// Remove the file preview.
					_this.removeFile(file);
					// If you want to the delete the file on the server as well,
					// you can do the AJAX request here.
				});

				// Add the button to the file preview element.
				file.previewElement.appendChild(removeButton);
			});

			// Execute when file uploads are complete
			myDropzone.on("complete", function() {
				// If all files have been uploaded
				if (this.getQueuedFiles().length == 0
						&& this.getUploadingFiles().length == 0) {
					var _this = this;
					// Remove all files
					_this.removeAllFiles();
				}
			});

			myDropzone.on("success", function(file, responseText) {
				// Handle the responseText here. For example, add the text to the preview element:
				if(responseText.status == true){
					$('#dropzone-to-imagem').val(responseText.data.short);
					$('#'+$('#dropzone-to-id').val()).val(responseText.data.short).trigger('change');
					$('#modal_file_upload_form_static').modal('hide');
				}
			});         	
		}
	};
}();
