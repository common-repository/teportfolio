var te_admin = {
    EVENTS: function() {
        // add button to TINYMCE
        te_admin.TINYMCE.add_button();
        // add color bar
        te_admin.PROGRESS_BAR.add_color();
        // delete bar 
        jQuery(document).on('click', '.te-fpb-delete', function(event) {
            te_admin.PROGRESS_BAR.delet_bar(this);
        });
        // add new bar 
        jQuery(document).on('click', '.te-fpb-add', function(event) {
            te_admin.PROGRESS_BAR.add_new();
        });
        // delete slide images
        jQuery(document).on('click', '.te-slide-img-delete', function(event) {
            te_admin.UPLOAD_IMG.delet_img(this);
            return false;
        });
        // add slide images
        jQuery(document).on('click', '.te-slide-add', function(event) {
            te_admin.UPLOAD_IMG.add_new(this);
            return false;
        });
        // add social buttons
        jQuery(document).on('change', '.teSB-select', function(event) {
            if (jQuery(this).val() == 0)
                return false;
            te_admin.SOCIAL_BUTTONS.add_buttons(jQuery(this).val());
            te_admin.SOCIAL_BUTTONS.update_id_button();
        });
        // delete social buttons
        jQuery(document).on('click', '.te-social-buttons-delete', function(event) {
            te_admin.SOCIAL_BUTTONS.delete_buttons(this);
            return false;
        });
    },
    PROGRESS_BAR: {
        add_color: function() {
            jQuery('.te-fpb-color').wpColorPicker();
        },
        delet_bar: function(thisClass) {
            jQuery(thisClass).parent().parent().remove();
        },
        add_new: function() {
            var html = '<div class="te-fpb-content">\n\
<div class="te-fpb-box">\n\
<p>Title</p>\n\
<input class="te-fpb-title" name="" type="text" value="" />\n\
</div>\n\
<div class="te-fpb-box">\n\
<p>Prcent</p>\n\
<select name="" class="te-fpb-prcent">\n\
<option value="10">10%</option>\n\
<option value="20">20%</option>\n\
<option value="30">30%</option>\n\
<option value="40">40%</option>\n\
<option value="50">50%</option>\n\
<option value="60">60%</option>\n\
<option value="70">70%</option>\n\
<option value="80">80%</option>\n\
<option value="90">90%</option>\n\
<option value="100">100%</option>\n\
</select>\n\
</div>\n\
<div class="te-fpb-box">\n\
<p>Color</p>\n\
<input name="" class="te-fpb-color" type="text" value="" />\n\
</div>\n\
\n\
<div class="te-fpb-box">\n\
<div class="te-fpb-delete">delete</div>\n\
</div>\n\
</div>\n\
';
            jQuery('.te-fpb-content-back').before(html);
            te_admin.PROGRESS_BAR.add_color();
            te_admin.PROGRESS_BAR.update_id_bar();
        },
        update_id_bar: function() {
            var id = 0;
            jQuery('.te-fpb-content').each(function() {
                // title 
                jQuery('.te-fpb-title', this).attr('name', '');
                jQuery('.te-fpb-title', this).attr('name', 'te-fpb[' + id + '][title]');
                // prcent
                jQuery('.te-fpb-prcent', this).attr('name', '');
                jQuery('.te-fpb-prcent', this).attr('name', 'te-fpb[' + id + '][prcent]');
                // color 
                jQuery('.te-fpb-color', this).attr('name', '');
                jQuery('.te-fpb-color', this).attr('name', 'te-fpb[' + id + '][color]');
                id++;
            });
        }
    },
    UPLOAD_IMG: {
        add_new: function(thisClass) {
            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = jQuery(thisClass);
            wp.media.editor.send.attachment = function(props, attachment) {
                var html = '\n\
<div class="te-slide-img">\n\
<img src="' + attachment.url + '" width="120" height="100" />\n\
<div class="te-slide-img-delete">delete</div>\n\
<input name="teSlide[]" type="hidden" value="' + attachment.url + '">\n\
</div>\n\
';
                jQuery('.te-fpb-img-back').before(html);
                wp.media.editor.send.attachment = send_attachment_bkp;
            };
            wp.media.editor.open(button);
            return false;
        },
        delet_img: function(thisClass) {
            jQuery(thisClass).parent().remove();
        }
    },
    TINYMCE: {
        add_button: function() {
            tinymce.PluginManager.add('te_tinymce_plugin', function(editor, url) {
                // Add a button that opens a window
                editor.addButton('te_tinymce_button', {
                    text: 'TE Portfolio',
                    icon: false,
                    onclick: function() {
                        // Open window
                        var settings = [
                            {//add type select
                                type: 'listbox',
                                name: 'float',
                                label: 'Position popup',
                                'values': [
                                    {text: 'right', value: '1'},
                                    {text: 'left', value: '2'}
                                ],
                                tooltip: 'Select a side floating content'
                            },
                            {//add type select
                                type: 'listbox',
                                name: 'portfolio_preview',
                                label: 'Portfolio preview',
                                'values': [
                                    {text: 'collage', value: '1'},
                                    {text: 'column 2', value: '2'},
                                    {text: 'column 3', value: '3'},
                                    {text: 'column 4', value: '4'}

                                ],
                                tooltip: 'Select the type of previews portfolio'
                            },
                            {//add type select
                                type: 'textbox',
                                name: 'title_button',
                                label: 'Button title',
                                tooltip: 'Enter the name of the header buttons'
                            },
                            {//add type select
                                type: 'listbox',
                                name: 'portfolio_filter',
                                label: 'Portfolio Filter',
                                'values': [
                                    {text: 'yes', value: '2'},
                                    {text: 'not', value: '1'}
                                ],
                                tooltip: 'Show filter?'
                            },
                            {//add type select
                                type: 'listbox',
                                name: 'portfolio_style',
                                label: 'Portfolio style',
                                'values': [
                                    {text: 'green', value: 'green'},
                                    {text: 'red', value: 'red'},
                                    {text: 'silver', value: 'silver'},
                                    {text: 'dark', value: 'dark'},
                                    {text: 'blue', value: 'blue'}
                                ],
                                tooltip: 'Select color style'
                            },
                            {//add type select
                                type: 'listbox',
                                name: 'portfolio_hover',
                                label: 'Hover effect',
                                'values': [
                                    {text: 'show', value: '5'},
                                    {text: 'right', value: '4'},
                                    {text: 'left', value: '3'},
                                    {text: 'top', value: '2'},
                                    {text: 'bottom', value: '1'}
                                ],
                                tooltip: 'Select hover effect'
                            },
                            {//add type select
                                type: 'container',
                                html: 'Portfolio category include:'

                            }
                        ];
                        // add category
                        if (jQuery('.meta_box_tinymce_te_portfolio').val()) {
                            var cat_json = JSON.parse(jQuery('.meta_box_tinymce_te_portfolio').val());
                            for (var i = 0, l = cat_json.length; i < l; i++) {
                                settings.push(
                                        {
                                            type: 'checkbox',
                                            label: cat_json[i]['name'],
                                            name: 'portfolio_category_' + cat_json[i]['id']
                                        }
                                );
                            }
                        }
                        // in the popup window settings
                        editor.windowManager.open({
                            title: 'TE Portfolio settings',
                            body: settings,
                            onsubmit: function(e) {
                                var cat_json = JSON.parse(jQuery('.meta_box_tinymce_te_portfolio').val());
                                var cat = '';
                                for (var i = 0, l = cat_json.length; i < l; i++) {
                                    if (e.data['portfolio_category_' + cat_json[i]['id']]) {
                                        cat += cat_json[i]['id'] + ',';
                                    }
                                }
                                cat = cat.slice(0, -1);
                                // Insert content when the window form is submitted
                                editor.insertContent('[tePortfolio float="' + e.data.float + '" portfolio_preview="' + e.data.portfolio_preview + '" portfolio_filter="' + e.data.portfolio_filter + '" portfolio_style="' + e.data.portfolio_style + '" portfolio_category="' + cat + '" portfolio_hover="' + e.data.portfolio_hover + '" title_button="' + e.data.title_button + '"][/tePortfolio]');
                            },
                            onrepaint: function(e) {
                                var window_id = this._id;
                                if (!jQuery('#' + window_id).hasClass('form-initialized')) {
                                    jQuery('#' + window_id).addClass('form-initialized');
                                    var inputs = jQuery('#' + window_id + '-body').find('.mce-formitem input');
                                    jQuery(inputs.get(0)).blur(function() {
                                    });
                                    jQuery(inputs.get(0)).focus(function() {
                                    });
                                }
                            }
                        });
                    }
                });
            });
            // activate Color Picker
            jQuery('.mce-last').wpColorPicker();
        }
    },
    SOCIAL_BUTTONS: {
        add_buttons: function(value) {
            var html = '<div class="te-social-buttons-block">\n\
<div class="te-social-buttons-img"><img src="/wp-content/plugins/tePortfolio/img/social/' + value + '.png" /></div>\n\
<div class="te-social-buttons-links">\n\
<p>' + value + ' <small>(without http: //)</small></p>\n\
<input class="te-social-buttons-link" type="text" name="teSB[][link]" value="" />\n\
<input class="te-social-buttons-name" type="hidden" name="teSB[][name]" value="' + value + '" />\n\
<div class="te-social-buttons-delete">delete</div>\n\
</div>\n\
</div>\n\
';
            jQuery('.te-social-buttons-list').append(html);
        },
        update_id_button: function() {
            var id = 0;
           jQuery('.te-social-buttons-block').each(function() {
                // buttons link
                jQuery('.te-social-buttons-link', this).attr('name', '');
                jQuery('.te-social-buttons-link', this).attr('name', 'teSB[' + id + '][link]');
                // buttons name
                jQuery('.te-social-buttons-name', this).attr('name', '');
                jQuery('.te-social-buttons-name', this).attr('name', 'teSB[' + id + '][name]');
                id++;
            });
        },
        delete_buttons: function(thisClass) {
            jQuery(thisClass).parent().parent().remove();
        }
    }
};

jQuery(document).ready(function() {
    te_admin.EVENTS();
});