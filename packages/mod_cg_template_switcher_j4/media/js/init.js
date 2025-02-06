/**
 * @package CG template switcher Module
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
var options;
var CG_TS_Images = new Array();
document.addEventListener('DOMContentLoaded', function() {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
		return;
	}
	options = Joomla.getOptions('mod_cg_template_switcher');
	if (typeof options !== 'undefined' ) {
			go_switch(options);
	}
});
function go_switch(options) {
	btn_ok = document.getElementById("CG_TS_OKBtn");
	if (btn_ok) {
		btn_ok.addEventListener('click',function() {
		sortValue = document.querySelector("#CG_TS_Select").selectedOptions[0].value;
		CG_TS_Cookie(sortValue);
		});
	}
	btn_cancel = document.getElementById("CG_TS_CancelBtn");
	if (btn_cancel) {
		btn_cancel.addEventListener('click',function() {
		CG_TS_Cookie_Del()
		});
	}
	if (options.showpreview == 'true') {
		var templates = options.templates;
		for (var k in templates) {
			CG_TS_Images[k] = new Image(templates[k]['width'],templates[k]['height']);
			CG_TS_Images[k].src = templates[k]['src'];
			CG_TS_Images[k].preview = templates[k]['preview'];
		}
	}
	document.getElementById("CG_TS_Select").addEventListener( 'change', function(){
			sortValue = this.selectedOptions[0].value;		
			if (options.showpreview == 'true') {
				CG_TS_ImageShow(sortValue);
			}
			if (options.autoswitch == 'true') {
				CG_TS_Cookie(sortValue);
			}
	});
}

function CG_TS_ImageShow(s) {
	if (CG_TS_Images[s]) {
		document.getElementById("CG_TS_Switcher").innerHTML = decodeURIComponent("%3C")+'img src="'+CG_TS_Images[s].preview+'" id="CG_TS_Img"'+decodeURIComponent("%3E");
	} else {
		document.getElementById("CG_TS_Switcher").innerHTML = options.noimage;
	}
	document.getElementById("CG_TS_SHOW").style ="display:block"; // show image (hidden when starting)
}
function CG_TS_Cookie_Del() {
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
	document.cookie = "cg_template=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;samesite=lax;"+$secure;
	document.getElementById('cg_ts_form').submit();
}
function CG_TS_Cookie(b) {
	var expires = "";
	if (options.cookie_duration > 0) {
		var date = new Date();
		date.setTime(date.getTime()+(parseInt(options.cookie_duration)*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
	document.cookie = "cg_template="+encodeURIComponent(b)+expires+"; path=/; samesite=lax;"+$secure;

    plg  = document.getElementById('jform_com_fields_default_template');
    if (plg) {
        for (let i = 0; i < plg.length; i++) {
            if (plg[i].value == b) { 
                plg.selectedIndex = i;
            }
        }
    }
	document.getElementById('cg_ts_form').submit();
}

