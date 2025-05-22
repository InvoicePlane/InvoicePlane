"use strict";

// Check JSON validity. No, show error in alert
function json_parse(data, debug) {
    if (typeof(debug) != 'undefined' && debug) {
        console.trace(data);
    }
    let response = data.search(/\{"success"\:/) > -1 ? {"success":0, "validation_errors":0} : {};
    try {
        response = JSON.parse(data);
    } catch (e) {
        var plus = '<h3>⚠ Parse JSON error! <a href="#js-error" data-toggle="collapse" class="btn btn-default">🛈</a></h3><div class="collapse" id="js-error">' + data + '</div>';
        setTimeout(function() {
            if($('#content .alert-danger').length) {
                $('#content .alert-danger').prepend(plus);
            } else {
                $('#content').prepend(plus);
            }
        }, 100);
        // console.error('Invalid JSON returned from server:', data.replace(/<[^>]+>/g, ''));
    } finally {
        return response;
    }
}

// Insert text into textarea at Caret Position
function insert_at_caret(areaId, text) {
    var txtarea = document.getElementById(areaId),
        scrollPos = txtarea.scrollTop,
        strPos = 0,
        br = ((txtarea.selectionStart || txtarea.selectionStart === '0') ?
            "ff" : (document.selection ? "ie" : false)),
        range;

    if (br === "ie") {
        txtarea.focus();
        range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if (br === "ff") {
        strPos = txtarea.selectionStart;
    }

    var front = (txtarea.value).substring(0, strPos),
        back = (txtarea.value).substring(strPos, txtarea.value.length);

    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if (br === "ie") {
        txtarea.focus();
        range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        range.moveStart('character', strPos);
        range.moveEnd('character', 0);
        range.select();
    } else if (br === "ff") {
        txtarea.selectionStart = strPos;
        txtarea.selectionEnd = strPos;
        txtarea.focus();
    }
    txtarea.scrollTop = scrollPos;
}

// takes mdl_email_template row as JSON, array with names to use in form fields.
function inject_email_template(template_fields, email_template) {
    $.each(email_template, function (key, val) {
        // remove prefix from key
        key = key.replace("email_template_", "");
        // if key is in template_fields, apply value to form field
        if (val && template_fields.indexOf(key) > -1) {
            $("#" + key).val(val).trigger('change');
        }
    });
}

function update_email_template_preview() {
    $('#email-template-preview').contents().find("body").html($('.email-template-body').val());
}

// Insert HTML tags into textarea
function insert_html_tag(tag_type, destination_id) {
    var text, sel, text_area, selectedText, startPos, endPos, replace, replaceText, len;
    switch (tag_type) {
        case 'text-bold':
            text = ['<b>', '</b>'];
            break;
        case 'text-italic':
            text = ['<em>', '</em>'];
            break;
        case 'text-paragraph':
            text = ['<p>', '</p>'];
            break;
        case 'text-linebreak':
            text = ['<br>', ''];
            break;

        case 'text-h1':
            text = ['<h1>', '</h1>'];
            break;
        case 'text-h2':
            text = ['<h2>', '</h2>'];
            break;
        case 'text-h3':
            text = ['<h3>', '</h3>'];
            break;
        case 'text-h4':
            text = ['<h4>', '</h4>'];
            break;

        case 'text-code':
            text = ['<code>', '</code>'];
            break;
        case 'text-hr':
            text = ['<hr/>', ''];
            break;
        case 'text-css':
            text = ['<style></style>', ''];
            break;
    }

    // Get the selected text
    text_area = document.getElementById(destination_id);
    if (document.selection !== undefined) {
        text_area.focus();
        sel = document.selection.createRange();
        selectedText = sel.text;
    }
    else if (text_area.selectionStart !== '') {
        startPos = text_area.selectionStart;
        endPos = text_area.selectionEnd;
        selectedText = text_area.value.substring(startPos, endPos);
    }

    // Check if <style> should be added
    if (tag_type === 'text-css') {
        replace = text[0] + '\n\r' + text_area.value;
        $(text_area).val(replace);
        update_email_template_preview();
        return true;
    }

    // Check if there is only one HTML tag
    if (text[1].length === 0) {
        insert_at_caret(destination_id, text[0]);
        update_email_template_preview();
        return true;
    }

    // Check if text is selected, replace it or just insert the tag at cursor position
    if (!selectedText || !selectedText.length) {
        text = text[0] + text[1];
        insert_at_caret(destination_id, text);
        update_email_template_preview();
    } else {
        replaceText = text[0] + selectedText + text[1];
        len = text_area.value.length;
        replace = text_area.value.substring(0, startPos) + replaceText + text_area.value.substring(endPos, len);
        $(text_area).val(replace);
        update_email_template_preview();
    }
}

// Get crsf names from ipconfig (config_item) on meta tags - since v1.6.3
const csrf_token_name = document.querySelector('meta[name="csrf_token_name"]').getAttribute('content');   // Default: _ip_csrf
const csrf_cookie_name = document.querySelector('meta[name="csrf_cookie_name"]').getAttribute('content'); // Default: ip_csrf_cookie

const legacy_calculation = parseInt(document.querySelector('meta[name="legacy_calculation"]').getAttribute('content')); // Default: 1 (legacy on)

// For Quote & Invoice views. Verify and set alert on item tax fields. All or not rule - since v1.6.3
function check_items_tax_usages(e) {
    // Not for legacy & only when e-Ivoice active(todo? global taxes?)
    if (legacy_calculation || ! document.querySelector('#e_invoice_active')) return;

    let x; // Loop index
    let oks = [0,0]; // Counters: No tax, Tax.
    let taxfield = document.querySelectorAll('.item select[name="item_tax_rate_id"]'); // get all tax selects

    for (x = 0; x < taxfield.length; x++) {
        if (taxfield[x].value != 0) {
            oks[1]++; // +1 for Tax
        }
        else {
            oks[0]++; // +1 for No
        }
     // taxfield[x].classList.add('alert-success'); // dbg! But Idea** green2ok. Todo?: same thing for all inputs amount.
        taxfield[x].classList.remove('alert-danger');

        // Have already event? Not: Add listener to Old&New fields to check when value change
        if ( ! $(taxfield[x]).data('hasTaxEvent')) {
            $(taxfield[x]).on('change', check_items_tax_usages).data('hasTaxEvent', true);
        }
    }
    // Zero with 0 == error. Need One == 0 to be valid (Why not an alert-success with Idea**)
    if (oks[0] != 0 && oks[1] != 0) {
        for (x = 0; x < taxfield.length; x++) {
            taxfield[x].classList.add('alert-danger'); // redNo0k
         // taxfield[x].classList.remove('alert-success'); // Idea** (Todo?: like for all inputs amount?)
        }
        // Only true, not from event. Set focus 1st tax selector (See items_tax_usages_bad() in number_helper)
        'undefined' != typeof(e) && e === true && taxfield[0].focus();
     }
}

$(function () {
    // Automatical CSRF protection for
    // All jquery POST requests
    $.ajaxPrefilter(function (options) {
        if (options.type === 'post' || options.type === 'POST' || options.type === 'Post') {
            if (options.data === '') {
                options.data += '?' + csrf_token_name + '=' + Cookies.get(csrf_cookie_name);
            } else {
                options.data += '&' + csrf_token_name + '=' + Cookies.get(csrf_cookie_name);
            }
        }
    });
    $(document).ajaxComplete(function () {
        $('[name="' + csrf_token_name + '"]').val(Cookies.get(csrf_cookie_name));
    });
    // Update crsf on all submit way's
    $('form').on('submit', function(){
        $('input[name="' + csrf_token_name + '"]').prop('value', Cookies.get(csrf_cookie_name));
    });

    // Set the default options for all instances of Select2
    $.fn.select2.defaults.set('selectionCssClass', ':all:');

    // Correct the height of the content area
    var $content = $('#content'),
        $html = $('html');

    var documentHeight = $html.outerHeight(),
        navbarHeight = $('.navbar').outerHeight(),
        headerbarHeight = $('#headerbar').outerHeight(),
        submenuHeight = $('#submenu').outerHeight(),
        contentHeight = documentHeight - navbarHeight - headerbarHeight - submenuHeight;
    if ($content.outerHeight() < contentHeight) {
        $content.outerHeight(contentHeight);
    }

    // Dropdown Datepicker fix
    $html.click(function () {
        $('.dropdown-menu:visible').not('.datepicker').removeAttr('style');
    });

    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Select2 for all select inputs
    $('.simple-select').select2();

    // Select2 for all multiple select inputs (customs)
    $('select.multiple-select').select2()
    .on('select2:select', function (e) {
        var $element = $(e.params.data.element);
        if($element.val() == '') { // none selected
            $(this).val('').trigger('change.select2'); // reset all & set to none
        }
        else {
            var vals = $(this).select2('val'); // options (array)
            if(vals.length && vals[0] == '') { // have none inside
                $(this).val(vals.slice(1)).trigger('change.select2'); // remove none & set
            }
        }
    })
    .on('select2:unselect', function(e) {
        if(! $(this).select2('val').length) { // zero option
            $(this).val('').trigger('change.select2'); // set to none
            // todo? how to prevent open
        }
    });

    // Enable clipboard toggles
    var clipboards = new ClipboardJS('.to-clipboard');

    // Keep track of the last "taggable" input/textarea
    $('.taggable').on('focus', function () {
        window.lastTaggableClicked = this;
    });

    // Template Tag handling
    $('.tag-select').select2().on('change', function (event) {
        var select = $(event.currentTarget);
        // Add the tag to the field
        if (typeof window.lastTaggableClicked !== 'undefined') {
            insert_at_caret(window.lastTaggableClicked.id, select.val());
        }

        // Reset the select and exit
        select.val([]);
        return false;
    });

    // HTML tags to email templates textarea
    $('.html-tag').click(function () {
        var tag_type = $(this).data('tagType');
        var body_id = $('.email-template-body').attr('id');
        insert_html_tag(tag_type, body_id);
    });

    // Email Template Preview handling
    var email_template_body_id = $('.email-template-body').attr('id');

    if ($('#email_template_preview').empty()) {
        update_email_template_preview();
    }

    $(email_template_body_id).bind('input propertychange', function () {
        update_email_template_preview();
    });

    $('#email-template-preview-reload').click(function () {
        update_email_template_preview();
    });

    // Spinner loader helper (global scope access)
    window.fullpage_loader = $('#fullpage-loader');
    window.loader_error = $('#loader-error');
    window.loader_icon = $('#loader-icon');
    window.reset_loader = function () {
        loader_error.hide();
        loader_icon.addClass('fa-spin').removeClass('text-danger');
        clearTimeout(window.fullpageloaderTimeout);
    }
    window.close_loader = function () {
        fullpage_loader.fadeOut(200);
        reset_loader();
    }
    window.show_loader = function (timeout) {
        timeout = timeout ? parseInt(timeout) : 10000; // 10s by default
        // Reset
        reset_loader();
        // Show
        fullpage_loader.fadeIn(200);
        window.fullpageloaderTimeout = window.setTimeout(function () {
            loader_error.fadeIn(200);
            loader_icon.removeClass('fa-spin').addClass('text-danger');
        }, timeout);
    }

    // Fullpage loader (Open spinner) From FORM? Only valid
    $(document).on('click', '.ajax-loader', function () {

        // Get parent form of clicked element
        const form = $(this).closest('form');
        // Have form? Yes, Check if valid.
        if (form.length && !form[0].checkValidity()) {
            return; // No valid, don't show spinner.
        }
        // Show loader
        show_loader();

    });

    // Fullpage loader (Close spinner) by red cross (top right)
    $(document).on('click', '.fullpage-loader-close', function () {
        close_loader();
    });

    var password_input = $('.passwordmeter-input');
    if (password_input) {
        password_input.on('input', function(){
            var strength = zxcvbn(password_input.val());

            $('.passmeter-2, .passmeter-3').hide();
            if (strength.score === 4) {
                $('.passmeter-2, .passmeter-3').show();
            } else if (strength.score === 3) {
                $('.passmeter-2').show();
            }
        });
    }

    // Detect Ctrl + S on the whole document
    $(document).on('keydown', function (e) {
        if (e.ctrlKey && e.key === 's') {
            // Detect if modal is open
            if ($('.modal-footer .btn-success:visible').length) {
                e.preventDefault();
                $('.modal-footer .btn-success').click();
            } else if ($('#headerbar .btn-success').length) {
                e.preventDefault();
                $('#headerbar .btn-success').click();
            }
        }
    });

    // Open/close QR code settings depending on checked QR code checkbox
    const checkboxQrCode = document.getElementById('settings[qr_code]');
    const panelQrCodeSettings = document.getElementById('panel-qr-code-settings');

    if (checkboxQrCode && panelQrCodeSettings) {
        checkboxQrCode.addEventListener('click', () => {
            panelQrCodeSettings.querySelectorAll('.row').forEach((row) => {
                if (checkboxQrCode.checked) {
                    row.classList.remove('hidden');
                } else {
                    row.classList.add('hidden');
                }
            });
        });
    }
});
