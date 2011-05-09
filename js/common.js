function getElementsByClass(searchClass,node,tag) {
	var classElements = new Array();
	if ( node == null )
		node = document;
	if ( tag == null )
		tag = '*';
	var els = node.getElementsByTagName(tag);
	var elsLen = els.length;
	var pattern = new RegExp('(^|\\\\s)'+searchClass+'(\\\\s|$)');
	for (i = 0, j = 0; i < elsLen; i++) {
		if ( pattern.test(els[i].className) ) {
			classElements[j] = els[i];
			j++;
		}
	}
	
	return classElements;
}
//addEvent(window,"load", function() {});
function addEvent(obj, type, fn)
{
    if (obj.attachEvent) {
        obj['e' + type + fn] = fn;
        obj[type + fn] = function() {obj['e' + type + fn](window.event);}
        obj.attachEvent('on' + type, obj[type + fn]);
    } else {
        obj.addEventListener(type, fn, false);
    }
}

Array.prototype.inArray = function (value) {
	var i;
	for (i=0; i < this.length; i++) {
		if (this[i] === value) {
			return true;
		}
	}
	return false;
};

Array.prototype.click = function (event) {
	var i;
	for (i=0; i < this.length; i++)
	{
		addEvent(this[i], "click", event);
	}
	return false;
};

//Object.prototype.previous = function (tagName) {
//	var el = this.previousSibling; 
//	while(el != null)
//	{
//		alert("aaaaaaa");
//		if(el.tagName!=null && el.tagName.toLowerCase() == tagName.toLowerCase())
//		{
//			return el;
//		}
//		var el = el.previousSibling; 
//	}
//	return null;
//};
//Object.extend(function (tagName) {
//	var el = this.previousSibling; 
//	while(el != null)
//	{
//		alert("aaaaaaa");
//		if(el.tagName!=null && el.tagName.toLowerCase() == tagName.toLowerCase())
//		{
//			return el;
//		}
//		var el = el.previousSibling; 
//	}
//	return null;
//}, {});


function confirmAction()
{
	
}

function $()
{
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++)
	{
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}
function el()
{
	var elements = new Array();
	for (var i = 0; i < arguments.length; i++)
	{
		var element = arguments[i];
		if (typeof element == 'string')
			element = document.getElementById(element);
		if (arguments.length == 1)
			return element;
		elements.push(element);
	}
	return elements;
}

function $$(searchClass,node,tag)
{
	getElementsByClass(searchClass,node,tag)
}

function log(el)
{
	console.log(el);
}

function goBack(amt)
{
	amt = amt==null || amt==undefined ? -1 : amt;
//	window.history.go(amt);
	window.history.back(amt);
}

function go(url)
{
	window.location=url;
}

function reloadPage() {
	window.location.reload();
}

var isIE = navigator.appName == 'Microsoft Internet Explorer' && navigator.userAgent.indexOf('Opera') < 1 ? 1 : 0;
var isOp = navigator.userAgent.indexOf('Opera') > -1 ? 1 : 0;
var isGe = navigator.userAgent.indexOf('Gecko') > -1 && navigator.userAgent.indexOf('Safari') < 1 ? 1 : 0;

function hasClass(object, className) {
	if (!object.className) return false;
	return (object.className.search('(^|\\s)' + className + '(\\s|$)') != -1);
}

function hasValue(object, value) {
	if (!object) return false;
	return (object.search('(^|\\s)' + value + '(\\s|$)') != -1);
}

function removeClass(object,className) {
	if (!object) return;
	object.className = object.className.replace(new RegExp('(^|\\s)'+className+'(\\s|$)'), RegExp.$1+RegExp.$2);
}

function addClass(object,className) {
	if (!object || hasClass(object, className)) return;
	if (object.className) {
		object.className += ' '+className;
	} else {
		object.className = className;
	}
}

function toggle(obj) {
	var el = document.getElementById(obj);
	if ( el.style.display != 'none' ) {
		el.style.display = 'none';
	}
	else {
		el.style.display = '';
	}
}

function externalLinks() {
	if (!document.getElementsByTagName)
		return;
	var anchors = document.getElementsByTagName("a");
	for ( var i = 0; i < anchors.length; i++) {
		var anchor = anchors[i];
		if (anchor.getAttribute("href")
				&& anchor.getAttribute("rel") == "external") {
			anchor.target = "_blank";
		}
	}
}