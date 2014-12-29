/* Author: William G. Rivera*/
"use strict";

$(document).ready(function() {

    $(".dropdown-toggle").click(function(e) {
        var menu = $(this).next('.dropdown-menu'),
            mousex = e.pageX + 20, //Get X coodrinates
            mousey = e.pageY + 20, //Get Y coordinates
            menuWidth = menu.width(), //Find width of tooltip
            menuHeight = menu.height(), //Find height of tooltip
            menuVisX = $(window).width() - (mousex + menuWidth), //Distance of element from the right edge of viewport
            menuVisY = $(window).height() - (mousey + menuHeight); //Distance of element from the bottom of viewport

        if (menuVisX < 20) { //If tooltip exceeds the X coordinate of viewport
            // menu.css({'left': '-89px'});
        } if (menuVisY < 20) { //If tooltip exceeds the Y coordinate of viewport
            menu.css({
                'top': 'auto',
                'bottom': '100%'
            });
        }
    });

    $('html').click(function () {
        $('.dropdown-menu:visible').not('.datepicker').removeAttr('style'); //Integrate this into the function.
    });

    // Handle click event for Email Template Tags insertion
    // Example Usage
    // <a href="#" class="text-tag" data-tag="{{{client_name}}}">Client Name</a>
    var lastTaggableClicked;
    $('.text-tag').bind('click', function () {
        var templateTag = this.getAttribute("data-tag");
        insertAtCaret(lastTaggableClicked.id, templateTag);
        return false;
    });

    // Keep track of the last "taggable" input/textarea
    $('.taggable').on('focus', function(){
        lastTaggableClicked = this;
    });

    // Load Resize Function
    $(window).resize();

});

/*Fix Scrollbar on Main Content*/
$(window).resize(function(){

    // Set sidebar height
    var D = document;
    var doc_height = Math.max(Math.max(D.body.scrollHeight,    D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight));
    $('.sidebar').height(doc_height);

    // Set height for menus
    var windowheight = $(this).outerHeight();
    var height = windowheight - $('nav.navbar').outerHeight();
    height = height - $('.main-area .headerbar').outerHeight() - $('.nav-tabs').outerHeight();

    if ( $('#form-settings') ) {
        height = height - 50;
    }

    if ( $('.main-area').outerWidth() < 800 ) {
        $('.main-area .container-fluid, .main-area .tab-content, .main-area .content, .main-area .table-content').css('margin-top',34,'height',height);
    } else {
        $('.main-area .container-fluid, .main-area .tab-content, .main-area .content, .main-area .table-content').height(height);
    }

});

// Insert text into textarea at Caret Position
function insertAtCaret(areaId, text) {
    var txtarea = document.getElementById(areaId);
    var scrollPos = txtarea.scrollTop;
    var strPos = 0;
    var br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
        "ff" : (document.selection ? "ie" : false));
    if(br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        strPos = range.text.length;
    } else if(br == "ff") strPos = txtarea.selectionStart;

    var front = (txtarea.value).substring(0, strPos);
    var back = (txtarea.value).substring(strPos, txtarea.value.length);
    txtarea.value = front + text + back;
    strPos = strPos + text.length;
    if(br == "ie") {
        txtarea.focus();
        var range = document.selection.createRange();
        range.moveStart('character', -txtarea.value.length);
        range.moveStart('character', strPos);
        range.moveEnd('character', 0);
        range.select();
    } else if(br == "ff") {
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
            $("#" + key).val(val);
        }
    });
}