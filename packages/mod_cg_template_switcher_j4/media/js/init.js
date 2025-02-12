/**
 * @package CG template switcher Module
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
var cgswitchmod = [];
var CG_TS_Images = new Array();
var once = [];
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
            document.getElementById('cg_ts_form_'+id).submit();
		});
	}
	btn_cancel = document.getElementById("CG_TS_CancelBtn_"+cgswoptions.id);
	if (btn_cancel) {
		btn_cancel.addEventListener('click',function() {
            id = this.getAttribute('data');
            CG_TS_Cookie_Del(id);
            document.getElementById('cg_ts_form_'+id).submit();
		});
	}
    // may have multiple buttons with same id 
	let btn_color = document.querySelectorAll("#cg_color_btn_"+cgswoptions.id);

    for (let i = 0; i < btn_color.length; i++) {
         btn_color[i].id = btn_color[i].id + i;
         one = document.querySelector('#cg_color_btn_'+cgswoptions.id+i);
 		 one.addEventListener('click',function(e) {
            e.stopPropagation();
            e.preventDefault();
            let id = this.getAttribute('data');
            let img = this.style.backgroundImage;
            let color = 0;
            let body = document.querySelector("body");
            if (img.indexOf('sun.svg') > 0) {
                img = img.replace('sun.svg','moon-stars.svg');
                //html.setAttribute('data-bs-theme',"dark");
                body.classList.add('cgcolor');
                color = 1;
            } else {
                //html.setAttribute('data-bs-theme',"");
                img = img.replace('moon-stars.svg','sun.svg');
                body.classList.remove('cgcolor');
                color = 0;
            }   
            btn  = document.querySelectorAll('.CG_COLOR_BTN');
            for (let i = 0; i < btn.length; i++) {
                btn[i].style.backgroundImage = img;
            }
            sortValue = 0;
            if (document.querySelector("#CG_TS_Select_"+id)) {
                sortValue = document.querySelector("#CG_TS_Select_"+id).selectedOptions[0].value;
            }
            CG_TS_Cookie(id,sortValue);
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
	sel = document.getElementById("CG_TS_Select_"+cgswoptions.id);
    if (sel) {
        sel.addEventListener( 'change', function(){
            id = this.parentNode.getAttribute('data');
            sortValue = this.selectedOptions[0].value;
            if (cgswoptions.showpreview == 'true') {
                CG_TS_ImageShow(id,sortValue);
            }
            CG_TS_Cookie(id,sortValue);
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
}
function CG_TS_ImageShow(id,s) {
    shows = document.querySelectorAll('.CG_TS_SHOW');
    for(var i=0; i<shows.length; i++) {
        shows[i].style ="display:none"; // hide all modules image block
    }
	if (CG_TS_Images[s]) {
		document.getElementById("CG_TS_Switcher_"+id).innerHTML = decodeURIComponent("%3C")+'img src="'+CG_TS_Images[s].preview+'" class="CG_TS_Img" id="CG_TS_Img_'+id+'"'+decodeURIComponent("%3E");
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
    // get color choice    
    let btn_color = document.getElementById("cg_color_btn_"+id);
    color = 0;
    if (btn_color) { // no color btn in current module
       let img = btn_color.style.backgroundImage;
       if (img.indexOf('sun.svg') > 0) {
          color = 0;
       } else {
          color = cgswitchmod[id].grayscale;
       }
    } else { // check if cookie color value
        my = document.cookie.match(new RegExp(
            "(?:^|; )" + 'cg_template'.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
            ))
        if (my.length > 0) {
            mycolor = my[1].split(':');
            color = mycolor[1];
        }
    
    }
	if (cgswitchmod[id].cookie_duration > 0) {
		var date = new Date();
		date.setTime(date.getTime()+(parseInt(cgswitchmod[id].cookie_duration)*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
	document.cookie = "cg_template="+b+':'+color+expires+"; path=/; samesite=lax;"+$secure;

    plg  = document.getElementById('jform_com_fields_default_template');
    if (plg) {
        for (let i = 0; i < plg.length; i++) {
            if (plg[i].value == b) { 
                plg.selectedIndex = i;
            }
        }
    }
    if (cgswitchmod[id].userid) { 
        url = '?option=com_ajax&module=cg_template_switcher&user='+ cgswitchmod[id].userid+'&tmpl='+b+'&color='+color+'&format=json';
        Joomla.request({
			method : 'POST',
			url : url,
            onSuccess: function(data, xhr) {
                console.log('Ajax OK');
            },
            onError: function(message) {console.log(message.responseText)}
        })
    }
    if (cgswitchmod[id].autoswitch == 'true') {
        document.getElementById('cg_ts_form_'+id).submit();
    }
}

