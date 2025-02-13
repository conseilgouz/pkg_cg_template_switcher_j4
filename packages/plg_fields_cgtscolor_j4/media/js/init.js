/**
 * @package CG template switcher Module
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
var plg_cgtscolor_options;

document.addEventListener('DOMContentLoaded', function() {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
		return;
	}
	plg_cgtscolor_options = Joomla.getOptions('plg_fields_cgtscolor');
	if (typeof plg_cgtscolor_options !== 'undefined' ) {
			go_plgswitchcolor();
	}
});
function go_plgswitchcolor() {
	inputs = document.querySelectorAll(".cgtscolor input");
    for(var i=0; i<inputs.length; i++) {
        inputs[i].addEventListener( 'change', function(){
            sortValue = this.value;
            cgplgcolor_cookie(sortValue);
        });
    }
}
function cgplgcolor_cookie(b) {
	var expires = "";
	if (plg_cgtscolor_options.cookie_duration > 0) {
		var date = new Date();
		date.setTime(date.getTime()+(parseInt(plg_cgtscolor_options.cookie_duration)*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
    one = 0;
    if (b != 'no') { 
        if (plg_cgtscolor_options.oneclick == "color") {
            one = plg_cgtscolor_options.gray;
        } else if (plg_cgtscolor_options.oneclick != "none") {
            one = plg_cgtscolor_options.oneclick;
        }
    }
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
    // get current value and extract color info if any.
    name = 'cg_template';
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    current = matches ? decodeURIComponent(matches[1]) : ''; 
    arr = current.split(':');
    if (arr.length == 2) {
        val = arr[0]+':'+one;
    } else {
        val = '0:'+one;
    }
	document.cookie = "cg_template="+val+expires+"; path=/; samesite=lax;"+$secure;
}

