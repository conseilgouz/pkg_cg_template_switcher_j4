/**
 * @package CG template switcher Module
 * @version 2.0.1 
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2021 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
var options;
var CG_TS_Images = new Array();
jQuery(document).ready(function($) {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
	}
	options = Joomla.getOptions('mod_cg_template_switcher');
	if (typeof options === 'undefined' ) { // cache Joomla problem
		request = {
			'option' : 'com_ajax',
			'module' : 'cg_template_switcher',
			'data'   : 'param',
			'format' : 'raw'
		};
		jQuery.ajax({
			type   : 'POST',
			data   : request,
			success: function (response) {
				options = JSON.parse(response);
				go_switch(options);
			}
		});
	};
	if (typeof options !== 'undefined' ) {
			go_switch(options);
	}
});
function go_switch(options) {
	jQuery(".fancybox").fancybox();
	jQuery("#CG_TS_OKBtn").click(function() {
		sortValue = jQuery("#CG_TS_Select").find(":selected").val();		
		CG_TS_Cookie(sortValue);
	});
	jQuery("#CG_TS_CancelBtn").click(function() {
		CG_TS_Cookie_Del()
	});
	if (options.showpreview == 'true') {
		var templates = options.templates;
		for (var k in templates) {
			CG_TS_Images[k] = new Image(templates[k]['width'],templates[k]['height']);
			CG_TS_Images[k].src = templates[k]['src'];
			CG_TS_Images[k].preview = templates[k]['preview'];
		}
	}
	jQuery("#CG_TS_Select").on( 'change', function(){
			sortValue = jQuery("#CG_TS_Select").find(":selected").val();		
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
		document.getElementById("CG_TS_Switcher").innerHTML = decodeURIComponent("%3C")+'a href="'+ CG_TS_Images[s].preview +'" class="fancybox"'+decodeURIComponent("%3E")+
		decodeURIComponent("%3C")+'img src="'+CG_TS_Images[s].preview+'" id="CG_TS_Img"'+decodeURIComponent("%3E")+
		decodeURIComponent("%3C")+'/a'+decodeURIComponent("%3E");
	} else {
		document.getElementById("CG_TS_Switcher").innerHTML = options.noimage;
	}
	document.getElementById("CG_TS_SHOW").style ="display:block"; // show image (hidden when starting)
}
function CG_TS_Cookie_Del() {
	$secure = "";
	if (window.location.protocol == "https:") $secure="secure;"; 
	document.cookie = "cg_template=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;samesite=lax;"+$secure;
	jQuery('#cg_ts_form').submit();
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
	jQuery('#cg_ts_form').submit();
}

