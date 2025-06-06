jQuery(document).ready(function ($) {

    // Function for Custom Style tab
    initCustomStyleMenu();

    function initCustomStyleMenu() {
        // Add new custom style
        (initCustomStyleNew = function () {
            $('#mybootstrap a.advgb-customstyles-new').unbind('click').click(function (e) {
                that = this;
                var nonce_val = $('#advgb_cstyles_nonce_field').val();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'advgb_custom_styles_ajax',
                        task: 'new',
                        nonce: nonce_val
                    },
                    success: function (res, stt) {
                        if (stt === 'success') {
                            $(that).parent().before('<li class="advgb-customstyles-items" data-id-customstyle="' + res.id + '"><a><i class="title-icon"></i><span class="advgb-customstyles-items-title">' + res.title + '</span></a><a class="copy"><span class="dashicons dashicons-admin-page"></span></a><a class="trash"><span class="dashicons dashicons-no"></span></a><ul style="margin-left: 30px"><li class="advgb-customstyles-items-class">('+ res.name +')</li></ul></li>');
                            initCustomStyleMenu();
                        } else {
                            alert(stt);
                        }
                    },
                    error: function(jqxhr, textStatus, error) {
                        alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                    }
                });
                return false;
            })
        })();

        // Delete custom style
        (initCustomStyleDelete = function () {
            $('#mybootstrap .advgb-customstyles-items a.trash').unbind('click').click(function (e) {
                that = this;
                var cf = confirm('Do you really want to delete "' + $(this).prev().prev().text().trim() + '"?');
                if (cf === true) {
                    var id = $(that).parent().data('id-customstyle');
                    var nonce_val = $('#advgb_cstyles_nonce_field').val();

                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'advgb_custom_styles_ajax',
                            id: id,
                            task: 'delete',
                            nonce: nonce_val
                        },
                        success: function (res, stt) {
                            if (stt === 'success') {
                                $(that).parent().remove();
                                if (res.id == myStyleId) {
                                    customStylePreview();
                                } else {
                                    customStylePreview(myStyleId);
                                }
                            } else {
                                alert(stt);
                            }
                        },
                        error: function(jqxhr, textStatus, error) {
                            alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                        }
                    });
                    return false;
                }
            })
        })();

        // Copy custom style
        (initCustomStyleCopy = function () {
            $('#mybootstrap .advgb-customstyles-items a.copy').unbind('click').click(function (e) {
                that = this;
                var id = $(that).parent().data('id-customstyle');
                var nonce_val = $('#advgb_cstyles_nonce_field').val();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'advgb_custom_styles_ajax',
                        id: id,
                        task: 'copy',
                        nonce: nonce_val
                    },
                    success: function (res, stt) {
                        if (stt === 'success') {
                            $(that).parents('.advgb-customstyles-list').find('li').last().before('<li class="advgb-customstyles-items" data-id-customstyle="' + res.id + '"><a><i class="title-icon" style="background-color: '+res.identifyColor+'"></i><span class="advgb-customstyles-items-title">' + res.title + '</span></a><a class="copy"><span class="dashicons dashicons-admin-page"></span></a><a class="trash"><span class="dashicons dashicons-no"></span></a><ul style="margin-left: 30px"><li class="advgb-customstyles-items-class">('+ res.name +')</li></ul></li>');
                            initCustomStyleMenu();
                        } else {
                            alert(stt);
                        }
                    },
                    error: function(jqxhr, textStatus, error) {
                        alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                    }
                });
                return false;
            })
        })();

        // Choose custom style
        (initTableLinks = function () {
            $('#mybootstrap .advgb-customstyles-items').unbind('click').click(function (e) {
                id = $(this).data('id-customstyle');
                customStylePreview(id);

                return false;
            })
        })();
    }

    // Add Codemirror
    var myCssArea, myEditor, myCustomCss, myStyleId;
    myCssArea = document.getElementById('advgb-customstyles-css');
    myEditor = CodeMirror.fromTextArea(myCssArea, {
        mode: 'css',
        lineNumbers: true,
        extraKeys: {"Ctrl-Space": "autocomplete"}
    });

    $(myCssArea).on('change', function() {
        myEditor.setValue($(myCssArea).val());
    });

    myEditor.on("blur", function() {
        myEditor.save();
        $(myCssArea).trigger('propertychange');

    });

    myStyleId = advgbGetCookie('advgbCustomStyleID');
    // Fix Codemirror not displayed properly
    $('a[href="#custom-styles"]').one('click', function () {
        myEditor.refresh();
        customStylePreview(myStyleId);
    });

    customStylePreview(myStyleId);
    function customStylePreview(id_element) {
        if (typeof (id_element) === "undefined" || !id_element) {
            var firstStyle = $('#mybootstrap ul.advgb-customstyles-list li:first-child');
            id_element = firstStyle.data('id-customstyle');
            firstStyle.addClass('active');
        }
        if (typeof (id_element) === "undefined" || id_element ==="") return;
        $('#mybootstrap .advgb-customstyles-list li').removeClass('active');
        $('#mybootstrap .advgb-customstyles-list li[data-id-customstyle='+id_element+']').addClass('active');

        document.cookie = 'advgbCustomStyleID=' + id_element;
        var nonce_val = $('#advgb_cstyles_nonce_field').val();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'advgb_custom_styles_ajax',
                id: id_element,
                task: 'preview',
                nonce: nonce_val
            },
            beforeSend: function() {
                $('#advgb-customstyles-info').append('<div class="advgb-overlay-box"></div>');
            },
            success: function (res, stt) {
                if (stt === 'success') {
                    $('#advgb-customstyles-title').val(res.title);
                    $('#advgb-customstyles-classname').val(res.name);
                    $('#advgb-customstyles-identify-color').minicolors('value', res.identifyColor);

                    myStyleId = id_element;
                    myCustomCss = '{\n' + res.css + '\n}';

                    var previewTarget = $(".advgb-customstyles-target");
                    previewTarget.attr('style','');

                    if (typeof(myCustomCss) !== 'undefined' || myCustomCss !== '') {
                        $('#advgb-customstyles-css').val(myCustomCss);
                    } else {
                        $('#advgb-customstyles-css').val('');
                    }
                    myEditor.setValue(myCustomCss);
                    parseCustomStyleCss();

                    $('#advgb-customstyles-info').find('.advgb-overlay-box').remove();
                } else {
                    alert(stt);
                    $('#advgb-customstyles-info').find('.advgb-overlay-box').css({
                        backgroundImage: 'none',
                        backgroundColor: '#ff0000',
                        opacity: 0.2
                    });
                }
            },
            error: function(jqxhr, textStatus, error) {
                alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                $('#advgb-customstyles-info').find('.advgb-overlay-box').css({
                    backgroundImage: 'none',
                    backgroundColor: '#ff0000',
                    opacity: 0.2
                });
            }
        })
    }

    String.prototype.replaceAll = function(search, replace) {
        if (replace === undefined) {
            return this.toString();
        }
        return this.split(search).join(replace);
    };

    // Parse custom style text to css for preview
    function parseCustomStyleCss() {
        var previewTarget = $("#advgb-customstyles-preview .advgb-customstyles-target");
        var parser = new (less.Parser);
        var content = '#advgb-customstyles-preview .advgb-customstyles-target ' + myEditor.getValue();
        parser.parse(content, function(err, tree) {
            if (err) {
                // Show error to the user
                if (err.message == 'Unrecognised input') {
                    err.message = configData.message;
                }
                alert(err.message);
                return false;
            } else {
                cssString = tree.toCSS().replace("#advgb-customstyles-preview .advgb-customstyles-target {","");
                cssString = cssString.replace("}","").trim();
                cssString = cssString.replaceAll("  ", "");
                myCustomCss = cssString;

                previewTarget.removeAttr('style');

                var attributes = cssString.split(';');
                for(var i=0; i<attributes.length; i++) {
                    if( attributes[i].indexOf(":") > -1) {
                        var entry = attributes[i].split(/:(.+)/);
                        previewTarget.css( jQuery.trim(""+entry[0]+""), jQuery.trim(entry[1]) );
                    }
                }

                return true;
            }
        })
    }

    // Bind event to preview custom style after changed css text
    (initCustomCssObserver = function () {
        var cssChangeWait;
        $('#advgb-customstyles-css').bind('input propertychange', function() {
            clearTimeout(cssChangeWait);
            cssChangeWait = setTimeout(function() {
                parseCustomStyleCss();
            }, 500);
        });
    })();

    $('#save_custom_styles').click(function (e) {
        saveCustomStyleChanges();
    });

    // Save custome style
    function saveCustomStyleChanges() {
        var myStyleTitle = $('#advgb-customstyles-title').val().trim();
        var myClassname =  $('#advgb-customstyles-classname').val().trim();
        var myIdentifyColor =  $('#advgb-customstyles-identify-color').val().trim();
        var nonce_val = $('#advgb_cstyles_nonce_field').val();
        parseCustomStyleCss();

        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'advgb_custom_styles_ajax',
                id: myStyleId,
                title: myStyleTitle,
                name: myClassname,
                mycss: myCustomCss,
                mycolor: myIdentifyColor,
                task: 'style_save',
                nonce: nonce_val
            },
            beforeSend: function () {
                $('#customstyles-tab').append('<div class="advgb-overlay-box"></div>')
            },
            success: function (res, stt) {
                if (stt === 'success') {
                    $('#advgb-customstyles-info form').submit();
                } else {
                    alert(stt);
                    $('#customstyles-tab').find('.advgb-overlay-box').remove();
                }
            },
            error: function(jqxhr, textStatus, error) {
                alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                $('#customstyles-tab').find('.advgb-overlay-box').remove();
            }
        })
    }
});
