/**
 * @package CG template switcher Module
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
var cgswitchmod = [];
var CG_TS_Images = new Array();
document.addEventListener('DOMContentLoaded', function() {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
		return;
	}
    mains = document.querySelectorAll('.cg_ts_form');
    for(var i=0; i<mains.length; i++) {
        let $one = mains[i];
        switchid = $one.getAttribute("data");
        cgswitchmod[switchid] = Joomla.getOptions('mod_cg_template_switcher_'+switchid);
        if (typeof cgswitchmod[switchid] !== 'undefined' ) {
			go_switch(cgswitchmod[switchid]);
        }
    }
});
function go_switch(cgswoptions) {
	let btn_ok = document.getElementById("CG_TS_OKBtn_"+cgswoptions.id);
	if (btn_ok) {
		btn_ok.addEventListener('click',function() {
            id = this.getAttribute('data');
            sortValue = document.querySelector("#CG_TS_Select_"+id).selectedOptions[0].value;
            CG_TS_Cookie(id,sortValue);
		});
	}
	btn_cancel = document.getElementById("CG_TS_CancelBtn_"+cgswoptions.id);
	if (btn_cancel) {
		btn_cancel.addEventListener('click',function() {
            id = this.getAttribute('data');
            CG_TS_Cookie_Del(id);
		});
	}
	if (cgswoptions.showpreview == 'true') {
		var templates = cgswoptions.templates;
		for (var k in templates) {
			CG_TS_Images[k] = new Image(templates[k]['width'],templates[k]['height']);
			CG_TS_Images[k].src = templates[k]['src'];
			CG_TS_Images[k].preview = templates[k]['preview'];
		}
	}
	document.getElementById("CG_TS_Select_"+cgswoptions.id).addEventListener( 'change', function(){
        id = this.parentNode.getAttribute('data');
		sortValue = this.selectedOptions[0].value;
		if (cgswoptions.showpreview == 'true') {
			CG_TS_ImageShow(id,sortValue);
		}
		if (cgswoptions.autoswitch == 'true') {
			CG_TS_Cookie(id,sortValue);
		}
        mod  = document.querySelectorAll('.CG_TS_Select');
        if (mod.length > 1) { // multiple modules : update all modules
            for (let i = 0; i < mod.length; i++) {
                for (let j = 0; j < mod[i].length; j++) {
                    if (mod[i][j].value == sortValue) { 
                        mod[i].selectedIndex = j;
                    }
                }
            }
        }
	});
}

function CG_TS_ImageShow(id,s) {
	if (CG_TS_Images[s]) {
		document.getElementById("CG_TS_Switcher_"+id).innerHTML = decodeURIComponent("%3C")+'img src="'+CG_TS_Images[s].preview+'" id="CG_TS_Img"'+decodeURIComponent("%3E");
	} else {
		document.getElementById("CG_TS_Switcher_"+id).innerHTML = cgswitchmod[id].noimage;
	}
	document.getElementById("CG_TS_SHOW_"+id).style ="display:block"; // show image (hidden when starting)
}
function CG_TS_Cookie_Del(id) {
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
	document.cookie = "cg_template=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;samesite=lax;"+$secure;
	document.getElementById('cg_ts_form_'+id).submit();
}
function CG_TS_Cookie(id,b) {
	var expires = "";
	if (cgswitchmod[id].cookie_duration > 0) {
		var date = new Date();
		date.setTime(date.getTime()+(parseInt(cgswitchmod[id].cookie_duration)*24*60*60*1000));
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
	document.getElementById('cg_ts_form_'+id).submit();
}

