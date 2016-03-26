var PluploadHandler = function( $, plupload ) {
    var self = this;
    this.plupload = plupload;
    this.uploader = new plupload.Uploader({
        runtimes : 'html5,flash,silverlight,html4',
        browse_button : document.getElementById('uploader_button'),
        url : uploader_url,  
        flash_swf_url : '/plupload-2.1.2/js/Moxie.swf',
        drop_element : "dropFilesHere", 
        file_data_name:"user-image-custom",
        filters : {
            max_file_size : '10mb',
            mime_types: [
                {title : "Image files", extensions : "jpg,jpeg,gif,png"}
            ]
        },
        init: {
            PostInit: function() {
                $('.filelist').html('');
            },
            Error: function(up, err) {
                console.log("\nError #" + err.code + ": " + err.message);
            }
        }
    });
    this.uploader.init();
    this.uploader.bind("FilesAdded", handlePluploadFilesAdded);
    this.uploader.bind("FileUploaded", handlePluploadFileUploaded);
    function handlePluploadFilesAdded(up, files) {
        console.log("+ handlePluploadFilesAdded");
        up.start();
    }
    function handlePluploadFileUploaded(up, file, res) {
        console.log("++ res.response: " + JSON.stringify(res.response));
        var img = "<img src='" + res.response + "?" + Date.now() + "'>";
        tinymce.activeEditor.execCommand('mceInsertContent', false, img);
    }
}

function initTinymce() {
    tinymce.remove('.use-tinymce');
    tinymce.init({selector:'textarea', 
                 plugins: "code link visualblocks", 
                 height: 200,
                 menubar: false,
                 resize: true,
                 statusbar: true,
                 extended_valid_elements : "span[!class]",
                 toolbar: "formatselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link code uploader_button",
                 visualblocks_default_state: false,
                 setup: function(editor) {
                     editor.addButton('uploader_button', {
                         type: 'button',
                         title: 'Insert image',
                         icon: 'image',
                         id: 'uploader_button'
                     });
                     editor.on('init', function(e) {
                         var pluploadHandler = new PluploadHandler(jQuery, plupload, 'html', 800 );
                     });
                 }           
    });     
}
initTinymce();
