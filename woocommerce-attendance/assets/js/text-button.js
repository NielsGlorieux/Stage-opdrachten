(function () {
    
    tinymce.PluginManager.add('gavickpro_tc_button',
        function (editor, url) { editor.addButton('gavickpro_tc_button', { 
            title: 'Shortcodes', 
            type: 'listbox', 
            fixedWidth: true,
            text: 'shortcodes',
            values: [
           
            { text: shortcode.title, 
            value: '[title]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

            },
            { text: shortcode.last_name,
            value: '[last_name]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

            },
             { text: shortcode.first_name, 
            value: '[first_name]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

            },
             { text: shortcode.company, 
            value: '[company]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

             },
             { text: shortcode.email, 
            value: '[email]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

             },
             { text: shortcode.country, 
            value: '[country]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

             }
            , { text: shortcode.address, 
            value: '[address]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

             },
             { text: shortcode.city, 
            value: '[city]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

            },
               { text: shortcode.state, 
            value: '[state]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

             },
               { text: shortcode.postcode, 
            value: '[postcode]', 
            onclick: function () { 
                editor.insertContent(this.value()); 
                } 

               }
            
            ] 
        });
        });

})();