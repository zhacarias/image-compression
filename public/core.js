/**
 * Created by qmamc0001 on 2/24/2017.
 */
$(function () {

    function block() {
         $.blockUI({
            message: '<span id="spinner_content"></span> <span class="sr-only">Processing...</span>',
            css : { 
                border: 'none', 
                padding: '15px', 
                backgroundColor: 'none', 
                '-webkit-border-radius': '10px', 
                '-moz-border-radius': '10px', 
                opacity: .5, 
                color: '#fff' 
            } 
        }); 

        var opts = {
              lines: 15 // The number of lines to draw
            , length: 28 // The length of each line
            , width: 14 // The line thickness
            , radius: 42 // The radius of the inner circle
            , scale: 1 // Scales overall size of the spinner
            , corners: 1 // Corner roundness (0..1)
            , color: '#000' // #rgb or #rrggbb or array of colors
            , opacity: 0.40 // Opacity of the lines
            , rotate: 0 // The rotation offset
            , direction: 1 // 1: clockwise, -1: counterclockwise
            , speed: 1.5 // Rounds per second
            , trail: 28 // Afterglow percentage
            , fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
            , zIndex: 2e9 // The z-index (defaults to 2000000000)
            , className: 'spinner' // The CSS class to assign to the spinner
            , top: '50%' // Top position relative to parent
            , left: '50%' // Left position relative to parent
            , shadow: false // Whether to render a shadow
            , hwaccel: false // Whether to use hardware acceleration
            , position: 'absolute' // Element positioning
        }
        var target = document.getElementById('spinner_content');
        var spinner = new Spinner(opts).spin(target);
    }
    
    function unblock() {
        $.unblockUI();
    }
    
    var myDropzone = new Dropzone("div#dropzone_form", {
        url: '/file-upload',
        maxFiles: 12,
        processing: function () {
            block();
        },
        success: function (data) {
            var responseText = data.xhr.responseText;
            var status = data.xhr.status;
            if (status == 200) {
                swal(responseText);
            } else {
                swal("Error!", responseText, "error");
            }
            $('#drop_download').prop('disabled','');
            unblock();
        },
        maxfilesexceeded: function(file) {
            swal('Oops..', 'Max of 12 only, please click reset', 'error');
            myDropzone.removeFile(file);
        }
    });
    
    $('#drop_clear').on('click', function () {
        $('#drop_download').prop('disabled','disabled');
        myDropzone.removeAllFiles(true);
    });
    
    $('#drop_download').on('click', function () {
        $.ajax({
            url: '/download',
            type: 'get',
            success: function (data) {
                window.location.href = data;
            }
        });
    });

    $(window).on('mousemove', function () {
        $('#drop_download').removeClass("active");
        $('#drop_clear').removeClass("active");
    });
    
});


