/**
 * @package CG template switcher Module
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @copyright (c) 2025 ConseilGouz. All Rights Reserved.
 * @author ConseilGouz 
 * 
 **/
 document.addEventListener("DOMContentLoaded", function(){
    // check CG custom classes
    fields = document.querySelectorAll('.view-module .clear');
    for(var i=0; i< fields.length; i++) {
        let field = fields[i];
        field.parentNode.parentNode.style.clear = "both";
        field.parentNode.parentNode.parentNode.style.clear = "both";
        field.parentNode.parentNode.parentNode.parentNode.style.clear = "both";
    }
    fields = document.querySelectorAll('.view-module .left');
    for(var i=0; i< fields.length; i++) {
        let field = fields[i];
        if (field.type == 'range') {
            field.parentNode.parentNode.parentNode.parentNode.style.float = "left";
        } else if (field.children && field.children.length > 0 && field.children[0].type == "radio") {
            field.parentNode.parentNode.parentNode.style.float = "left";
        } else {
            field.parentNode.parentNode.style.float = "left";
        }
    }
    fields = document.querySelectorAll('.view-module .right');
    for(var i=0; i< fields.length; i++) {
        let field = fields[i];
        if (field.type == 'range') {
            field.parentNode.parentNode.parentNode.parentNode.style.float = "right";
        } else if   (field.children && field.children.length > 0 && field.children[0].type == "radio") {
            field.parentNode.parentNode.parentNode.style.float = "right";
        } else {
            field.parentNode.parentNode.style.float = "right";
        }
    }
    fields = document.querySelectorAll('.view-module .half');
    for(var i=0; i< fields.length; i++) {
        let field = fields[i];
        if (field.type == 'range')  {
            field.parentNode.parentNode.parentNode.parentNode.style.width = "50%";
        } else if (field.children && field.children.length > 0 && field.children[0].type == "radio") {
            field.parentNode.parentNode.parentNode.style.width = "50%";
        } else {
            field.parentNode.parentNode.style.width = "50%";
        }
    }
    fields = document.querySelectorAll('.view-module .gridauto');
    for(var i=0; i< fields.length; i++) {
        let field = fields[i];
        field.parentNode.parentNode.style.gridColumn = "auto";
    }
})