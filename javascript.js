// jQuery not really required, it's here to overcome an inability to pass configuration options to the fiddle remotely
$(document).ready(function() {
    // Custom example logic
    function $(id) {
        return document.getElementById(id);
    }

    var uploader = new plupload.Uploader({
        runtimes : 'html5,html4',
        browse_button : 'pickfiles', // you can pass in id...
        container: $('container'), // ... or DOM Element itself
        max_file_size : '10mb',

        // Fake server response here
        // url : '../upload.php',
        url: "/echo/json",

        filters : [],

        init: {
            PostInit: function() {
                $('filelist').innerHTML = '';

                $('uploadfiles').onclick = function() {
                    uploader.start();
                    return false;
                };
            },

            FilesAdded: function(up, files) {
                plupload.each(files, function(file) {
                    $('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                });
            },

            UploadProgress: function(up, file) {
                $(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
            },

            Error: function(up, err) {
                $('console').innerHTML += "\nError #" + err.code + ": " + err.message;
            }
        }
    });

    uploader.init();
});
