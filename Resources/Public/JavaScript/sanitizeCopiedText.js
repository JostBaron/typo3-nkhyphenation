
/**
 * Register to the copy and cut events.
 */
jQuery(function() {
    jQuery('body').bind({
        'copy': handleCopy,
        'cut': handleCopy
    });
});

/**
 * Removes all soft hyphens from a given element.
 * @param {Object} element The DOM-Element containing the hyphenated text.
 * @returns void
 */
function removeHyphens(element) {
    var softHyphen = jQuery("<div/>").html('&shy;').text();

    var $element = jQuery(element);
    var text = $element.text().replace(new RegExp(softHyphen, 'g'), '');
    $element.text(text);
}

/**
 * This eventhandler makes sure the copied text does not contain hyphens
 * anymore.
 * This was largely taken from Hyphenator.js.
 * 
 * @param {Object} event
 */
function handleCopy(event) {
    event = event || window.event;
    var target = event.target || event.srcElement;
    var currDoc = target.ownerDocument;
    var body = currDoc.getElementsByTagName('body')[0];
    var targetWindow = currDoc.defaultView || currDoc.parentWindow;

    //create a hidden shadow element
    var shadow = currDoc.createElement('div');
    
    // Hide the element
    shadow.style.color = window.getComputedStyle ? targetWindow.getComputedStyle(body, null).backgroundColor : '#FFFFFF';
    shadow.style.fontSize = '0px';

    body.appendChild(shadow);

    if (!!window.getSelection) {
        //FF3, Webkit, IE9
        event.stopPropagation();

        var selection = targetWindow.getSelection();
        var range = selection.getRangeAt(0);
        shadow.appendChild(range.cloneContents());

        // Actually remove the hyphens
        removeHyphens(shadow);
        selection.selectAllChildren(shadow);

        // Create a function to restore the old state
        var restore = function() {
            shadow.parentNode.removeChild(shadow);
            selection.removeAllRanges(); //IE9 needs that
            selection.addRange(range);
        };

    } else {
        // IE < 9
        event.cancelBubble = true;
        var selection = targetWindow.document.selection;
        var range = selection.createRange();
        shadow.innerHTML = range.htmlText;
        
        // Do the removal
        removeHyphens(shadow);

        var rangeShadow = body.createTextRange();
        rangeShadow.moveToElementText(shadow);
        rangeShadow.select();

        // Create a function to restore the old state
        var restore = function() {
            shadow.parentNode.removeChild(shadow);
            if (range.text !== "") {
                range.select();
            }
        };
    }

    // Restore directly after the copying is finished (the timeout is needed to
    // do it afterwards.
    setTimeout(restore, 0);
}
