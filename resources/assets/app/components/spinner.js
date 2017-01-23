
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

export default function(size, color) {
    if (browserSupportsAnimation()) {
        let sizeclass = '';
        switch(size){
            case '6':
                sizeclass = '__size6';
                break;
            case '8':
                sizeclass = '__size8';
                break;
            case '10':
                sizeclass = '__size10';
                break;
            case '12':
                sizeclass = '__size10';
                break;
            case '14':
                sizeclass = '__size10';
                break;
            default:
                sizeclass = '';
                break;
        }
        let colorClass = color && color == 'white' ? '__white' : '';
        return '<span class="kabooodle__spinner '+sizeclass+' '+colorClass+' "></span>';
    } else {
        let src = KABOOODLE_APP.makeStaticAsset("assets/images/icons/ring-alt.gif");
        return '<img  src="'+src+'" style="margin:-2px 2px 0 0; padding:0;" height="'+size+'" width="'+size+'" >';
    }
}