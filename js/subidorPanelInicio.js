"use strict";

var cargaSubidor = function () {
    var subidor = function () {

        $('#kt_dropzone_panelInicio').dropzone({
            url: "./phpincludes/subidor.php", 
            paramName: "archivo",
            params: {idRelacionado: '1', tabla: "cont"},
            maxFiles: 10,
            maxFilesize: 10, // MB
            addRemoveLinks: true,
            removedfile: function(file) {
				var name = file.name;        
				$.ajax({
					type: 'POST',
					url: 'delete.php',
					data: "id="+name,
					dataType: 'html'
				});
					var _ref;
					return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;        
            },
            /*acceptedFiles: "image/*,application/pdf,.psd",*/
            accept: function(file, done) {
                //console.log(file.status)
               /* console.log(done)*/
                done();
                /*if (file.name == "") {
                    done("error.");
                } else {
                    done();
                }*/
            },
            init: function () {
                
                var imagenesDropzone = this;
                $.ajax({
                    url : './adminajax.php',
                    data : { 'op': "9998" },
                    type : 'POST',
                    success : function(data){
                        var dataArray= $.parseJSON(data);

                        for (var i = 0; i < dataArray.length; i++) {
                            var mockFile = { name: dataArray[i], size: 12345 };
                            imagenesDropzone.emit('addedfile', mockFile);
                            imagenesDropzone.options.thumbnail.call(imagenesDropzone, mockFile, dataArray[i]);
                            imagenesDropzone.emit('complete', mockFile);
                            imagenesDropzone._updateMaxFilesReachedClass();
                        }   
                    }
                });
                
                /*var mockFile = { name: "1.jpg", size: 12345 };
                this.emit('addedfile', mockFile);
                this.options.thumbnail.call(this, mockFile, "1.jpg");
                this.emit('complete', mockFile);
                this._updateMaxFilesReachedClass();*/
                
               
                
                /*var mockFile = { name: "1.jpg", size: 12345, type: 'image/jpeg' };
                this.files.push(mockFile);
                this.emit('addedfile', mockFile);
                this.createThumbnailFromUrl(mockFile, "1.jpg");
                this.emit('complete', mockFile);
                this._updateMaxFilesReachedClass();*/

            }
        });
        

        
        /*$(".dz-remove").on("click", function (e) {
             e.preventDefault();
             e.stopPropagation();

             var imageId = $(this).parent().find(".dz-filename > span").text();

             $.ajax({
             url: "Your url here",
             data: { imageId: imageId},
             type: 'POST',
             success: function (data) {
                  if (data.NotificationType === "Error") {
                       //toastr.error(data.Message);
                  } else {
                       //toastr.success(data.Message);                          
                  }},
                  error: function (data) {
                       //toastr.error(data.Message);
                  }
             })

        });*/
        
    }

    return {
        init: function() {
            subidor();
        }
    };
}();

jQuery(document).ready(function () {
	cargaSubidor.init();
});
