/**
 * Ascripta: Editor
 * 
 * The following scripts apply to the site backend editor.
 *
 * @version     1.4.2
 * @package     AE/Assets/JS
 * @author      Ascripta
 */


(function() {

    tinymce.PluginManager.add('ae_editor_elements_button', function(editor, url) {

        /**
         * Buttons
         */

        var buttons = {
            fields: {},
            styles: {}
        };

        buttons.fields = [{
            type: 'textbox',
            name: 'url',
            label: 'URL',
            value: ''
        }, {
            type: 'textbox',
            name: 'title',
            label: 'Title',
            value: 'test'
        }, {
            type: 'checkbox',
            name: 'external',
            label: '',
            text: 'Open link in a new tab',
            checked: false
        }];

        buttons.styles.primary = {
            text: 'Primary Button',
            onclick: function(e) {
                if (editor.selection.getContent().length) {
                    buttons.fields[0].value = editor.selection.getNode().getAttribute("href");
                }
                if (editor.selection.getContent().length) {
                    buttons.fields[1].value = editor.selection.getContent().replace(/<\/?[^>]+(>|$)/g, "");
                }
                editor.windowManager.open({
                    title: 'Primary Button',
                    body: buttons.fields,
                    onsubmit: function(e) {
                        editor.insertContent(
                            '<a href="' + e.data.url + '" class="btn btn-primary"' + (e.data.external ? 'target="_blank"' : '') + '>' +
                            e.data.title +
                            '</a>'
                        );
                    }
                });
                e.stopPropagation();
            }
        };

        buttons.styles.secondary = {
            text: 'Secondary Button',
            onclick: function(e) {
                if (editor.selection.getContent().length) {
                    buttons.fields[0].value = editor.selection.getNode().getAttribute("href");
                }
                if (editor.selection.getContent().length) {
                    buttons.fields[1].value = editor.selection.getContent().replace(/<\/?[^>]+(>|$)/g, "");
                }
                editor.windowManager.open({
                    title: 'Secondary Button',
                    body: buttons.fields,
                    onsubmit: function(e) {
                        editor.insertContent(
                            '<a href="' + e.data.url + '" class="btn btn-default"' + (e.data.external ? 'target="_blank"' : '') + '>' +
                            e.data.title +
                            '</a>'
                        );
                    }
                });
                e.stopPropagation();
            }
        };

        /**
         * Formats
         */

        var formats = {
            fields: {},
            styles: {}
        };

        formats.styles.lead = {
            text: 'Lead',
            onclick: function() {
                editor.selection.getNode().className = 'lead';
            }
        };

        /**
         * Editor
         */

        editor.addButton('ae_editor_elements_button', {
            title: 'Elements',
            type: 'menubutton',
            icon: 'icon dashicons-image-filter',
            menu: [{
                text: 'Buttons',
                menu: [buttons.styles.primary, buttons.styles.secondary]
            }, {
                text: 'Formats',
                menu: [formats.styles.lead]
            }]
        });

    });

})();
