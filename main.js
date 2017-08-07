/*
*  Autofill Function
*/
var bizdir_yourInfo_autofill = "For us to contact you...";
var bizdir_description_autofill = "Please enter a brief (800 characters or less) description of the organization (text only, no HTML, line breaks, or formatting)...";
var bizdir_cat_description_autofill = "Please enter a brief (800 characters or less) description of the category (text only, no HTML, line breaks, or formatting)...";
var bizdir_keywords_autofill = "Words for the organization to be found by...";
var bizdir_companyInfo_autofill = "To be displayed with your listing...";
var bizdir_geoInfo_autofill = "For geographical searching...";
var bizdir_categoryInfo_autofill = "The name of the category...";

function bizdir_clearAutoFill(id,type) {
	if(id == null || id == "" || type == null || type == "")
		return;
	var input = document.getElementById(id);
	if(input != null && type != null)
		switch(type) {
			case "YourInfo": if(input.value == bizdir_yourInfo_autofill) input.value = ""; input.style.color = "#000000"; break;
			case "Description": if(input.value == bizdir_description_autofill) input.value = ""; input.style.color = "#000000"; break;
			case "Cat_Description": if(input.value == bizdir_cat_description_autofill) input.value = ""; input.style.color = "#000000"; break;
			case "Keywords": if(input.value == bizdir_keywords_autofill) input.value = ""; input.style.color = "#000000"; break;
			case "CompanyInfo": if(input.value == bizdir_companyInfo_autofill) input.value = ""; input.style.color = "#000000"; break;
			case "GeoInfo": if(input.value == bizdir_geoInfo_autofill) input.value = ""; input.style.color = "#000000"; break;
			case "CategoryInfo": if(input.value == bizdir_categoryInfo_autofill) input.value = ""; input.style.color = "#000000"; break;
		}
}

function bizdir_clearAllAutoFill() {
	//Clear text fields
	var all_inputs = document.getElementsByTagName('input');
	for(var i=0;i<all_inputs.length;i++) {
		var input = all_inputs[i];
		switch(input.getAttribute('bizdir_autofill')) {
			case 'YourInfo': 
				if(input.value == bizdir_yourInfo_autofill || input.value == "" || input.value == null) {
					input.value = "";
					input.style.color = "#000000"; 
				}
			break;
			case 'Keywords': 
				if(input.value == bizdir_keywords_autofill || input.value == "" || input.value == null) {
					input.value = "";
					input.style.color = "#000000"; 
				}
			break;
			case 'CompanyInfo':
				if(input.value == bizdir_companyInfo_autofill || input.value == "" || input.value == null) {
					input.value = "";
					input.style.color = "#000000";
				}
			break;
			case 'GeoInfo':
				if(input.value == bizdir_geoInfo_autofill || input.value == "" || input.value == null) {
					input.value = "";
					input.style.color = "#000000";
				}
			break;
            case 'CategoryInfo':
                if(input.value == bizdir_categoryInfo_autofill || input.value == "" || input.value == null) {
					input.value = "";
					input.style.color = "#000000";
				}
            break;
		}
	}
	var all_textareas = document.getElementsByTagName('textarea');
	for(var i=0;i<all_textareas.length;i++) {
		var input = all_textareas[i];
		if(
		   	input.getAttribute('bizdir_autofill') == 'Description' && 
			(input.value == "" || input.value == null || input.value == bizdir_description_autofill)
		) {
			input.style.color = '#000000';
			input.value = "";
		} else if(
		   	input.getAttribute('bizdir_autofill') == 'Cat_Description' && 
			(input.value == "" || input.value == null || input.value == bizdir_cat_description_autofill)
		) {
			input.style.color = '#000000';
			input.value = "";
		}
	}
}

function bizdir_populateAutofill() {
	var override = arguments[0] == true;
	var all_inputs = document.getElementsByTagName('input');
	for(var i=0;i<all_inputs.length;i++) {
		var input = all_inputs[i];
		switch(input.getAttribute('bizdir_autofill')) {
			case 'YourInfo': 
				if(override || input.value == bizdir_yourInfo_autofill || input.value == "" || input.value == null) {
					input.value = bizdir_yourInfo_autofill;
					input.style.color = "#999999"; 
				}
			break;
			case 'Keywords': 
				if(override || input.value == bizdir_keywords_autofill || input.value == "" || input.value == null) {
					input.value = bizdir_keywords_autofill;
					input.style.color = "#999999"; 
				}
			break;
			case 'CompanyInfo':
				if(override || input.value == bizdir_companyInfo_autofill || input.value == "" || input.value == null) {
					input.value = bizdir_companyInfo_autofill;
					input.style.color = "#999999";
				}
			break;
			case 'GeoInfo':
				if(override || input.value == bizdir_geoInfo_autofill || input.value == "" || input.value == null) {
					input.value = bizdir_geoInfo_autofill;
					input.style.color = "#999999";
				}
			break;
            case 'CategoryInfo':
                if(override || input.value == bizdir_categoryInfo_autofill || input.value == "" || input.value == null) {
					input.value = bizdir_categoryInfo_autofill;
					input.style.color = "#999999";
				}
            break;
		}
	}
	var all_textareas = document.getElementsByTagName('textarea');
	for(var i=0;i<all_textareas.length;i++) {
		var input = all_textareas[i];
		if(
		   	input.getAttribute('bizdir_autofill') == 'Description' && 
			(input.value == "" || input.value == null || input.value == bizdir_description_autofill || override)
		) {
			input.style.color = '#999999';
			input.value = bizdir_description_autofill;
		} else if(
		   	input.getAttribute('bizdir_autofill') == 'Cat_Description' && 
			(input.value == "" || input.value == null || input.value == bizdir_cat_description_autofill || override)
		) {
			input.style.color = '#999999';
			input.value = bizdir_cat_description_autofill;
		}
	}
}
/*
* ON LOAD
*/
function bizdir_onload() {
	bizdir_populateAutofill();
	var bizdir_submit = document.getElementById("bizdir_submit")
	if(bizdir_submit != null)
		bizdir_submit.disabled = false;
}

window.onload = bizdir_onload;
/*
*  HELPER FUNCTIONS
*/
function clearMessage() {
	var messages = document.getElementById('bizdir_messages');
	if(messages != null) {
		messages.className = '';
		messages.innerHTML = '';
	}
}
