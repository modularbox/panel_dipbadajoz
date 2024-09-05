"use strict";

var cargaSubidor = function () {
    var subidor = function () {

        $('#kt_dropzone_2').dropzone({
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
                console.log(done)
                done();
                /*if (file.name == "") {
                    done("error.");
                } else {
                    done();
                }*/
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
