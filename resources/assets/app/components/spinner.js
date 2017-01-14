
/**
 *
 * @returns {boolean}
 */
function browserSupportsAnimation(){
    let property = 'animation';
    let elm = document.createElement('div');
    property = property.toLowerCase();

    if (elm.style[property] != undefined)
        return true;

    var propertyNameCapital = property.charAt(0).toUpperCase() + property.substr(1),
        domPrefixes = 'Webkit Moz ms O'.split(' ');

    for (var i = 0; i < domPrefixes.length; i++) {
        if (elm.style[domPrefixes[i] + propertyNameCapital] != undefined)
            return true;
    }

    return false;
}

export default function(size) {
    if (browserSupportsAnimation()) {
        return '<span class="kabooodle__spinner"></span>';
    } else {
        let src = KABOOODLE_APP.makeStaticAsset("assets/images/icons/ring-alt.gif");
        return '<img  src="'+src+'" style="margin:-2px 2px 0 0; padding:0;" height="'+size+'" width="'+size+'" >';
    }
}