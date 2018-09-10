var RefId = 'timezone';
var AltId = 'tz_select_date';

function ChangeCurrentTime(this_select) {
	document.getElementById(RefId).innerHTML = '';
	for (i = 0 ; i < RefSelect.getElementsByTagName('optgroup').length ; i++) {
		if (RefSelect.getElementsByTagName('optgroup')[i].getAttribute('data-tz-value') == this_select.value) {
			document.getElementById(RefId).appendChild(RefSelect.getElementsByTagName('optgroup')[i].cloneNode(true));
		}
	}
	DefineOption();
}

function DefineOption() {
	for (i = 0 ; i < document.getElementById(RefId).getElementsByTagName('option').length ; i++) {
		if (document.getElementById(RefId).getElementsByTagName('option')[i].value == RefSelect.options[RefNumber].value) {
			document.getElementById(RefId).getElementsByTagName('option')[i].selected = 'selected';
		}
	}
}

function ResetTimeZone() {
	if (typeof RefNumber !== 'undefined' && typeof RefSelect !== 'undefined') {
		document.getElementById(RefId).innerHTML = '';
		document.getElementById(RefId).appendChild(RefSelect.options[RefNumber].parentNode.cloneNode(true));
	}
}

if (((document.compatMode && !window.opera) || document.getElementsByClassName) && document.getElementById(AltId) && document.getElementById(RefId)) {
	document.getElementById(AltId).style.display = 'block';
	var RefNumber = document.getElementById(RefId).selectedIndex;
	var RefSelect = document.getElementById(RefId).cloneNode(true);
	ResetTimeZone();
	DefineOption();
}