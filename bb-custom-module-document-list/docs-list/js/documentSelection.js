var pd = new Array();
(function ($) {

    DocumentUpload = {
        _documentSelector: null,

        _init: function()
        {
            $('body').delegate('.fl-docx-field .fl-docx-select', 'click', DocumentUpload._selectDocuments);
        },

        _selectDocuments: function()
        { 
            if(DocumentUpload._documentSelector === null) {
                DocumentUpload._documentSelector = wp.media({
                    title: 'Select documents',
                    button: { text: 'Select documents' },
                    library : { type : ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
                        'application/msword',
                        'application/pdf',
                        'application/mspowerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
                        'application/vnd.oasis.opendocument.text',
                        'application/excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/octet-stream',
                        'text/plain',
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'image/x-icon',
                        'image/svg+xml',
                        'audio/mpeg3',
                        'audio/mp4',
                        'audio/ogg',
                        'audio/wav',
                        'video/mp4',
                        'video/quicktime',
                        'audio/x-ms-wmv',
                        'video/avi',
                        'audio/mpeg',
                        'video/ogg',
                        'video/3gpp',
                        'video/3gpp2'
                        ] },
                    multiple: 'add'  
                }); 
                     
                DocumentUpload._documentSelector.on('open', function () {
                    var selection = DocumentUpload._documentSelector.state().get('selection'); 
                    if (pd.length > 0) {
                        ids = pd;   
                        console.log(pd);
                    } else {
                        var id = document.getElementById('idies').value;
                        var pieces = id.split(',');
                        pieces.shift();
                        console.log(id);
                        if (pieces.length > 0) {
                            ids = pieces;
                        } else {
                            ids = [];
                        }
                    }    
                    ids.forEach(function (id) {     
                        attachment = wp.media.attachment(id);
                        attachment.fetch();
                        console.log(attachment);
                        selection.add( attachment ? [ attachment ] : [] );
                    });
                });          
            }
            DocumentUpload._documentSelector.once('select', $.proxy(DocumentUpload._multiDocumentsSelected, this));
            DocumentUpload._documentSelector.open(); 
        },
        _multiDocumentsSelected: function()
        {     
            var docs = new Array();
            docs = DocumentUpload._documentSelector.state().get('selection').toJSON();
            var wrap = $(this).closest('.fl-docx-field'),
            pdfField = wrap.find('input[type=hidden]'),
            count = wrap.find('.fl-multiple-docs-count');                
            count.html('<b>' + docs.length + '</b>' + (docs.length > 1 ? " documents selected" : " document selected"));          
            wrap.removeClass('fl-docx-empty');
            wrap.find('label.error').remove();
            pd = [];
            for (var i = 0; i < docs.length; i++){              
                pd.push(docs[i].id);         
            }
            jQuery.unique(pd);
            pdfField.val(pd).trigger('change');
            console.log(pd);
        }
    };
    $(function(){
        DocumentUpload._init();
    });
})(jQuery);