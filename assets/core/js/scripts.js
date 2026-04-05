"use strict";

// Check JSON validity. No, show error in console and alert.
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
                $('#content').prepend('<div class="alert alert-danger">' + plus + '</div>');
            }
        }, 100);
        console.error('Invalid JSON returned from server! data:', data);
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

// Basic HTML encoder to avoid reinterpreting DOM text as HTML meta-characters
function encodeHtml(str) {
    if (str == null) {
        return '';
    }
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Sanitize HTML for email template preview
// Allows only safe formatting tags and strips scripts, event handlers, and dangerous attributes
function sanitize_email_template_html(html) {
    // Parse HTML inside an inert <template> so scripts are never executed while
    // the content is being sanitized.
    var template = document.createElement('template');
    template.innerHTML = html || '';
    var temp = template.content;
    
    // List of allowed tags (only safe formatting tags)
    var allowedTags = ['b', 'strong', 'em', 'i', 'p', 'br', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 
                       'code', 'pre', 'hr', 'span', 'div', 'a', 'ul', 'ol', 'li', 
                       'table', 'tr', 'td', 'th', 'thead', 'tbody'];
    
    // List of allowed attributes (only safe, non-executable attributes)
    // Note: 'style' attribute removed to prevent CSS-based attacks
    var allowedAttrs = ['class', 'href', 'title', 'alt', 'target', 'rel'];

    // Decode HTML entities and normalize attribute values before validation.
    function normalizeAttrValue(value) {
        var normalized = value || '';
        try {
            var parser = new DOMParser();
            var doc = parser.parseFromString(normalized, 'text/html');
            if (doc && doc.documentElement) {
                normalized = doc.documentElement.textContent || '';
            }
        } catch (e) {
            normalized = value || '';
        }

        // Remove control/format chars (including zero-width and BOM), trim, and lowercase.
        // Ranges: C0 controls (\u0000-\u001F), C1 controls (\u007F-\u009F),
        // zero-width/RTL markers (\u200B-\u200F, \u202A-\u202E, \u2060-\u206F),
        // and BOM (\uFEFF).
        normalized = normalized.replace(/[\u0000-\u001F\u007F-\u009F\u200B-\u200F\u202A-\u202E\u2060-\u206F\uFEFF]+/g, '').trim();
        return normalized.toLowerCase();
    }
    
    // Recursively clean all elements
    function cleanNode(node) {
        var tagName = node.tagName ? node.tagName.toLowerCase() : null;
        
        // Remove script, object, embed, and iframe tags that could execute code
        if (tagName && (tagName === 'script' || tagName === 'object' || 
            tagName === 'embed' || tagName === 'iframe' || tagName === 'style')) {
            node.remove();
            return;
        }
        
        // Remove disallowed tags (keep their content)
        if (tagName && allowedTags.indexOf(tagName) === -1) {
            var parent = node.parentNode;
            while (node.firstChild) {
                parent.insertBefore(node.firstChild, node);
            }
            node.remove();
            return;
        }
        
        // Remove dangerous attributes from allowed tags
        if (node.attributes) {
            var attrsToRemove = [];
            for (var i = 0; i < node.attributes.length; i++) {
                var attr = node.attributes[i];
                var attrNameLower = attr.name.toLowerCase();
                var attrValueNormalized = normalizeAttrValue(attr.value);
                
                // Remove event handlers (onclick, onload, etc.)
                if (attrNameLower.indexOf('on') === 0) {
                    attrsToRemove.push(attr.name);
                }
                // Remove attributes not in the allowlist
                else if (allowedAttrs.indexOf(attrNameLower) === -1) {
                    attrsToRemove.push(attr.name);
                }
                // Check for dangerous protocols in href attributes
                else if (attrNameLower === 'href' &&
                        (/^\s*(javascript|data|vbscript|file|about|blob)\s*:/i.test(attrValueNormalized))) {
                    attrsToRemove.push(attr.name);
                }
                // Enforce opener-safe behavior for links opened in a new tab
                else if (attrNameLower === 'target' && attrValueNormalized === '_blank') {
                    var relValue = node.getAttribute('rel') || '';
                    var relParts = relValue.split(/\s+/).filter(Boolean);
                    if (relParts.indexOf('noopener') === -1) {
                        relParts.push('noopener');
                    }
                    if (relParts.indexOf('noreferrer') === -1) {
                        relParts.push('noreferrer');
                    }
                    node.setAttribute('rel', relParts.join(' '));
                }
            }
            attrsToRemove.forEach(function(attrName) {
                node.removeAttribute(attrName);
            });
        }
        
        // Recursively clean child nodes
        var children = Array.from(node.childNodes);
        children.forEach(function(child) {
            if (child.nodeType === 1) { // Element node
                cleanNode(child);
            }
        });
    }
    
    
    // Clean all child nodes in the detached container
    Array.from(temp.childNodes).forEach(function(child) {
        if (child.nodeType === 1) {
            cleanNode(child);
        }
    });
    
    // Move the sanitized fragment into a container to return its HTML string.
    var container = document.createElement('div');
    while (temp.firstChild) {
        container.appendChild(temp.firstChild);
    }
    
    return container.innerHTML;
}

/**
 * Safely decode HTML entities without DOM-based XSS risks
 * Uses a map of common entities and regex replacement
 */
function decodeHtmlEntities(text) {
    // Unicode constants for validation (using JavaScript camelCase convention)
    var maxUnicodeCodepoint = 0x10FFFF;
    var surrogateMin = 0xD800;
    var surrogateMax = 0xDFFF;
    
    /**
     * Check if a codepoint is valid Unicode and not in the surrogate pair range
     */
    function isValidUnicodeCodepoint(code) {
        return code >= 0 && code <= maxUnicodeCodepoint && 
               (code < surrogateMin || code > surrogateMax);
    }
    
    // Map of HTML entities to their character equivalents
    var entities = {
        '&amp;': '&',
        '&lt;': '<',
        '&gt;': '>',
        '&quot;': '"',
        '&#39;': "'",
        '&#x27;': "'",
        '&apos;': "'"
    };
    
    // Replace known entities
    var decoded = text;
    for (var entity in entities) {
        if (entities.hasOwnProperty(entity)) {
            decoded = decoded.split(entity).join(entities[entity]);
        }
    }
    
    // Handle decimal numeric entities (&#123;) with bounds checking
    // Use String.fromCodePoint for proper Unicode support including supplementary planes
    decoded = decoded.replace(/&#(\d+);/g, function(match, dec) {
        var code = parseInt(dec, 10);
        if (isValidUnicodeCodepoint(code)) {
            // Use fromCodePoint if available (modern browsers), fallback to fromCharCode
            if (String.fromCodePoint) {
                return String.fromCodePoint(code);
            }
            return String.fromCharCode(code);
        }
        return match; // Return original if invalid
    });
    
    // Handle hexadecimal numeric entities (&#xAB;) with bounds checking
    // Use String.fromCodePoint for proper Unicode support including supplementary planes
    decoded = decoded.replace(/&#x([0-9A-Fa-f]+);/g, function(match, hex) {
        var code = parseInt(hex, 16);
        if (isValidUnicodeCodepoint(code)) {
            // Use fromCodePoint if available (modern browsers), fallback to fromCharCode
            if (String.fromCodePoint) {
                return String.fromCodePoint(code);
            }
            return String.fromCharCode(code);
        }
        return match; // Return original if invalid
    });
    
    return decoded;
}

function update_email_template_preview() {
    var rawHtml = $('.email-template-body').val();
    
    // Only decode HTML entities if the content appears to be double-encoded
    // (i.e., contains encoded HTML tags like &lt;strong&gt; instead of actual <strong> tags)
    // This prevents changing intentionally entity-encoded content meant to display literally
    var htmlToRender = rawHtml;
    
    // Improved regex patterns to detect complete encoded and real HTML tags
    // Requires closing bracket to ensure these are actual tags, not just < or &lt; characters
    var containsEncodedHtmlTags = /&lt;\s*\/?[a-zA-Z][\w:.-]*[^>]*&gt;/.test(rawHtml);
    var containsRawHtmlTags = /<\s*\/?[a-zA-Z][\w:.-]*[^>]*>/.test(rawHtml);
    
    // Only decode if we have encoded tags but no real tags (indicating double-encoding)
    if (containsEncodedHtmlTags && !containsRawHtmlTags) {
        // Safer entity decoding using a dedicated function
        htmlToRender = decodeHtmlEntities(rawHtml);
    }
    
    // Sanitize the HTML to ensure only safe tags are rendered
    var sanitizedHtml = sanitize_email_template_html(htmlToRender);
    var iframe = $('#email-template-preview')[0];
    
    // Initialize iframe with a proper HTML document if needed
    if (iframe && iframe.contentDocument) {
        var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
        
        // If the iframe doesn't have a proper document structure, create one
        if (!iframeDoc.body) {
            iframeDoc.open();
            iframeDoc.write('<!DOCTYPE html><html><head><meta charset="utf-8"></head><body></body></html>');
            iframeDoc.close();
        }
        
        // Now set the sanitized HTML content
        iframeDoc.body.innerHTML = sanitizedHtml;
    }
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

// Get CSRF configuration from meta tags - since v1.6.3
const csrf_token_name = document.querySelector('meta[name="csrf_token_name"]').getAttribute('content'); // Default: _ip_csrf

// Get CSRF token value from meta tag instead of reading HttpOnly cookie
// This allows the cookie to have HttpOnly=true for XSS protection while still providing
// the token to JavaScript for AJAX requests. The server rotates this value on each page load.
let csrf_token_value = document.querySelector('meta[name="csrf_token_value"]').getAttribute('content');

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
    // Automatic CSRF protection for all jQuery POST requests
    // Uses meta tag value instead of reading HttpOnly cookie directly
    $.ajaxPrefilter(function (options) {
        if (options.type === 'post' || options.type === 'POST' || options.type === 'Post') {
            if (options.data === '') {
                options.data += '?' + csrf_token_name + '=' + csrf_token_value;
            } else {
                options.data += '&' + csrf_token_name + '=' + csrf_token_value;
            }
        }
    });

    // Update CSRF token value after each AJAX request completes
    // The server may rotate the token, so we refresh it from response headers or page updates
    $(document).ajaxComplete(function (event, xhr, settings) {
        // Check if server returned a new token in response headers
        var newToken = xhr.getResponseHeader('X-CSRF-Token');
        if (newToken) {
            csrf_token_value = newToken;
        }
        // Update all CSRF hidden inputs with current token value
        $('[name="' + csrf_token_name + '"]').val(csrf_token_value);
    });

    // Update CSRF token on all form submissions
    $('form').on('submit', function(){
        $('input[name="' + csrf_token_name + '"]').prop('value', csrf_token_value);
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

    if ($('#email-template-preview').length) {
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
    window.loader_error_icon = $('#loader-error-icon');
    window.reset_loader = function () {
        loader_error.hide();
        loader_error_icon.hide();
        loader_icon.show().addClass('fa-spin').removeClass('text-danger');
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
            loader_icon.hide();
            loader_error_icon.fadeIn(200);
            loader_error.fadeIn(200);
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