/**
 * @package CG template switcher Module
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
var plg_cgswitch_options;

document.addEventListener('DOMContentLoaded', function() {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
		return;
	}
	plg_cgswitch_options = Joomla.getOptions('plg_fields_cgtemplateswitcher');
	if (typeof plg_cgswitch_options !== 'undefined' ) {
			go_plgswitch();
	}
});
function go_plgswitch() {
	document.getElementById("jform_com_fields_template_defaut").addEventListener( 'change', function(){
		sortValue = this.selectedOptions[0].value;
        cgplgswitch_cookie(sortValue);
	});
}

function cgplgswitch_cookie(b) {
	var expires = "";
	if (plg_cgswitch_options.cookie_duration > 0) {
		var date = new Date();
		date.setTime(date.getTime()+(parseInt(plg_cgswitch_options.cookie_duration)*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
	document.cookie = "cg_template="+encodeURIComponent(b)+expires+"; path=/; samesite=lax;"+$secure;
    mod  = document.getElementById('CG_TS_Select');
    if (!mod) return;
    for (let i = 0; i < mod.length; i++) {
       if (mod[i].value == b) { 
            mod.selectedIndex = i;
       }
    }
}

