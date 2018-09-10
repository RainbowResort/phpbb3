/**
* phpBB3 forum functions
*/

function find_username(url) {
	popup(url, 760, 570, '_usersearch');
	return false;
}

function popup(u, w, h, n) {
	if (!n) {
		n = '_popup';
	}

	window.open(u.replace(/&amp;/g, '&'), n, 'height=' + h + ',resizable=yes,scrollbars=yes,width=' + w);
	return false;
}

function jumpto(page, per_page, base_url) {
	if (page !== null && !isNaN(page) && page == Math.floor(page) && page > 0) {
		if (base_url.indexOf('?') == -1) {
			document.location.href = base_url + '?start=' + ((page - 1) * per_page);
		} else {
			document.location.href = base_url.replace(/&amp;/g, '&') + '&start=' + ((page - 1) * per_page);
		}
	}
}

function marklist(id, name, state) {
	var parent = document.getElementById(id) || document.all(id);

	if (!parent) {
		return;
	}

	var rb = parent.getElementsByTagName('input') || parent.document.all.tags('input');

	for (var r = 0; r < rb.length; r++) {
		if (rb[r].name.substr(0, name.length) == name && rb[r].disabled !== true) {
			rb[r].checked = state;
		}
	}
}

function selectCode(c) {
	'use strict';

	if (document.getElementsByTagName) {
		var e = c.parentNode.parentNode.getElementsByTagName('PRE')[0];
	} else if (document.selection) {
		var e = c.parentElement.parentElement.all.tags('PRE')[0];
	}

	var s, r;

	if (window.getSelection) {
		s = window.getSelection();
		if (window.opera && e.innerHTML.substring(e.innerHTML.length - 4) === '<BR>') {
			e.innerHTML = e.innerHTML + '&nbsp;';
		}
		r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	} else if (document.getSelection) {
		s = document.getSelection();
		r = document.createRange();
		r.selectNodeContents(e);
		s.removeAllRanges();
		s.addRange(r);
	} else if (document.selection) {
		r = document.body.createTextRange();
		r.moveToElementText(e);
		r.select();
	}
}

function getStuff(object) {
	if (document.getElementById) {
		return document.getElementById(object);
	} else if (document.all) {
		return document.all(object);
	}
}

/**
* Dropdown menus functions
*/

function removeComment(d) {
	var c = d.innerHTML.replace('<!--', ' ');
	var n = c.lastIndexOf('-->');

	if (n >= 0 && n + 3 >= c.length) {
		c = c.substring(0, n);
		d.innerHTML = c;
	}
}

function changeDisplay(d, l, p, focused) {
	if (d.style.display !== 'none') {
		if (document.removeEventListener) {
			document.removeEventListener('click', handler0, false);
			window.removeEventListener('resize', handler1, false);
			d.removeEventListener('resize', handler2, false);
		} else if (document.detachEvent) {
			document.detachEvent('onclick', handler0);
			window.detachEvent('onresize', handler1);
			d.detachEvent('onresize', handler2);
		}
	}

	if (focused) {
		var show = d.style.display != 'none';
		d.style.borderWidth = show ? '' : '2px';
		d.style.borderStyle = show ? '' : 'solid';
		d.style.display = show ? 'none' : 'block';
		if (typeof l === 'object') {l.blur();}
	} else {
		d.style.display = 'none';
		d.style.borderStyle = '';
		d.style.borderWidth = '';
	}

	if (d.style.display !== 'none') {
		cd = d;
		cl = l;
		cp = p;

		if (document.removeEventListener) {
			document.addEventListener('click', handler0, false);
			window.addEventListener('resize', handler1, false);
			d.addEventListener('resize', handler2, false);
		} else if (document.detachEvent) {
			document.attachEvent('onclick', handler0);
			window.attachEvent('onresize', handler1);
			d.attachEvent('onresize', handler2);
		}

		bugFix(cd, cl, cp);
	}
}

function toggleLists(d, l, p) {
	if (typeof qkl === 'object' && (d !== qkl)) {changeDisplay(qkl);}
	if (typeof nfl === 'object' && (d !== nfl)) {changeDisplay(nfl);}
	if (typeof usl === 'object' && (d !== usl)) {changeDisplay(usl);}
	if (typeof ttl === 'object' && (d !== ttl)) {changeDisplay(ttl);}
	if (typeof d === 'object') {changeDisplay(d, l, p, true);}
}

function bugFix(d, l, p, refresh) {
	if (refresh && document.uniqueID && !document.querySelector) {
		d.style.display = 'none';
		d.style.display = 'block';
	}

	if (typeof l === 'object' && d.offsetParent === p) {
		d.style.top = (l.offsetTop + l.offsetHeight) + 'px';
	}

	if (typeof nfl === 'object' && (d === nfl)) {
		if (typeof l === 'object') {
			if (document.documentElement.dir == 'ltr') {
				if (l.parentNode.offsetWidth - l.offsetLeft - d.offsetWidth > 0) {
					d.style.right = l.parentNode.offsetWidth - l.offsetLeft - d.offsetWidth + 'px';
				} else {
					d.style.right = '0';
				}
			} else if (document.fireEvent && !document.querySelector) {
				if (l.parentNode.offsetWidth + l.offsetLeft - d.offsetWidth > 0) {
					d.style.left = l.parentNode.offsetWidth + l.offsetLeft - d.offsetWidth + 'px';
				} else {
					d.style.left = '0';
				}
			} else {
				if (l.offsetLeft + l.offsetWidth - d.offsetWidth > 0) {
					d.style.left = l.offsetLeft + l.offsetWidth - d.offsetWidth + 'px';
				} else {
					d.style.left = '0';
				}
			}
		}

		if (document.getElementById('notification_scroll')) {
			var ns = document.getElementById('notification_scroll');
			ns.style.height = 'auto';

			if (typeof window.innerHeight === 'number') {
				var TotalHeight = window.innerHeight;
			} else {
				var TotalHeight = document.documentElement.offsetHeight;
			}

			if (TotalHeight > 520) {
				var Maximum = 350;
			} else if (TotalHeight > 400) {
				var Maximum = 225;
			} else {
				var Maximum = 100;
			}

			if (document.fireEvent && !document.querySelector) {
				ns.style.overflowY = '';

				if (ns.offsetHeight > Maximum) {
					ns.style.height = Maximum + 'px';
					ns.style.overflowY = 'scroll';
				}
			} else {
				ns.style.maxHeight = Maximum + 'px';
				ns.style.overflow = 'auto';
			}
		}
	}

	if (!refresh && typeof iFrameFix === 'object') {
		d.appendChild(iFrameFix);
		d.style.overflow = 'visible';
		var b = '-' + d.style.borderWidth;

		if (document.documentElement.dir == 'ltr') {
			iFrameFix.style.left = b;
		} else {
			iFrameFix.style.right = b;
		}

		iFrameFix.style.top = b;
		iFrameFix.style.height = d.offsetHeight + 'px';
		iFrameFix.style.width = d.offsetWidth + 'px';
	}
}

/**
* Dropdown menus initialisation
*/

if (document.documentElement && (document.documentElement.className.indexOf('dropdown-enabled') > -1)) {
	var handler0 = function() {toggleLists();};
	var handler1 = function() {bugFix(cd, cl, cp, true);};
	var handler2 = function() {bugFix(cd, cl, cp);};
	var cd, cl, cp;

	if (document.uniqueID && !window.XMLHttpRequest) {
		var iFrameFix = document.createElement('iframe');
		iFrameFix.scrolling = 'no';
		iFrameFix.src = "javascript:'<html></html>';";
		iFrameFix.style.position = 'absolute';
		iFrameFix.style.zIndex = '-1';
		iFrameFix.style.filter = 'mask()';
	}

	if (document.getElementById('menubar')) {
		if (document.uniqueID && !document.querySelector) {
			var parentBar = document.getElementById('menubar').childNodes[0];
		} else {
			var parentBar = document.getElementById('menubar');
		}

		parentBar.style.position = 'relative';
		parentBar.style.zIndex = '1';

		if (document.getElementById('quick_links_list') && document.getElementById('quick_link')) {
			var qkl = document.getElementById('quick_links_list'); removeComment(qkl); qkl.onclick = function(e) {if (e) {e.stopPropagation();} else {window.event.cancelBubble = true;}};
			document.getElementById('quick_link').onclick = function(e) {if (e) {e.stopPropagation();} else {window.event.cancelBubble = true;} toggleLists(qkl, this, parentBar); return false;};
		}

		if (document.getElementById('user_list') && document.getElementById('user_link')) {
			if (document.getElementById('notification_list') && document.getElementById('notification_link')) {
				var nfl = document.getElementById('notification_list'); removeComment(nfl); nfl.onclick = function(e) {if (e) {e.stopPropagation();} else {window.event.cancelBubble = true;}};
				document.getElementById('notification_link').onclick = function(e) {if (e) {e.stopPropagation();} else {window.event.cancelBubble = true;} toggleLists(nfl, this, parentBar); return false;};
			}

			var usl = document.getElementById('user_list'); removeComment(usl); usl.onclick = function(e) {if (e) {e.stopPropagation();} else {window.event.cancelBubble = true;}};
			document.getElementById('user_link').onclick = function(e) {if (e) {e.stopPropagation();} else {window.event.cancelBubble = true;} toggleLists(usl, this, parentBar); return false;};
		}
	}
}

/**
* Change the global class to .hasjs
*/

if (document.documentElement) {
	document.documentElement.className = document.documentElement.className.replace(/nojs/gi,'hasjs');
} else if (document.all && (document.all.tags('html')[0].className.indexOf('nojs') > -1)) {
	document.all.tags('html')[0].className = 'hasjs';
}