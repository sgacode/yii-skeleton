(function() {
	

/* ------------------------------ Funcs ------------------------------------------------------- */
/**
 * Объект, содержащий функции общего назначения
 */
var Funcs = function(){};
Funcs.prototype = {
	detectIe6 : function()
	{
		var browser = navigator.appName;
		if (browser == "Microsoft Internet Explorer")
		{
			var b_version = navigator.appVersion;
			var re = /\MSIE\s+(\d\.\d\b)/;
			var res = b_version.match(re);
			if (res[1] <= 6)
			{
				return true;
			}
		}
		return false;
	},
	/**
	 * Проверка на IE9
	 */
	detectIe9 : function()
	{
		var browser = navigator.appName;
		if (browser == "Microsoft Internet Explorer")
		{
			var b_version = navigator.appVersion;
			var re = /\Trident\/5\.0/;
			var res = re.test(b_version);
			if (res)
			{
				return true;
			}
		}
		return false;
	},
	/**
	 * Получение параметров из url
	 */
	parseUrl: function(url)
	{
		if (typeof url == 'undefined')
		{
			url = window.location.href;
		}
		var a =  document.createElement('a');
		a.href = url;
		return {
			source: url,
			protocol: a.protocol.replace(':',''),
			host: a.hostname,
			port: a.port,
			query: a.search,
			params: (function(){
				var ret = {},
				seg = a.search.replace(/^\?/,'').split('&'),
				len = seg.length, i = 0, s;
				for (;i<len;i++) {
					if (!seg[i]) {
						continue;
					}
					s = seg[i].split('=');
					ret[s[0]] = s[1];
				}
				return ret;
			})(),
			file: (a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
			hash: a.hash.replace('#',''),
			path: a.pathname.replace(/^([^\/])/,'/$1'),
			relative: (a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
			segments: a.pathname.replace(/^\//,'').split('/')
		};
	},
	/**
	 * Получение указанного параметра из url
	 */
	getUrlParam: function(name, url)
	{
		if (typeof url == 'undefined')
		{
			url = window.location.href;
		}
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, '\\\]');
		var regexS = '[\\?&#]' + name + '=([^&#]*)';
		var regex = new RegExp(regexS);
		var results = regex.exec(url);
		if(results != null)
		{
			return decodeURIComponent(results[1].replace(/\+/g, ' '));
		}
		return null;
	},
	/**
	 * Проверка на массив
	 */
	isArray : function(variable)
	{
		return typeof(variable)=='object' && (variable instanceof Array);
	},
	/**
	 * Проверка на пустоту
	 */
	isEmpty : function(obj)
	{
		for (var k in obj)
			return false
		return true
	},
	/**
	 * Функция для реализации наследования
	 */
	extend : function(Child, Parent)
	{
		var F = function() { };
		F.prototype = Parent.prototype;
		Child.prototype = new F();
		Child.prototype.constructor = Child;
		Child.parent = Parent.prototype;
	},
	/**
	* Проверка формата даты (dd.mm.yyyy)
	* @param string dateStr
	*/
	checkDateFormat : function(dateStr)
	{
		var dateReg = /^\d{2}.\d{2}.\d{4}$/;
		return dateReg.test(dateStr); 
	},
	/**
	 * Удаление пробелов
	 */
	trim : function(str)
	{
		return str.replace(/(^\s+)|(\s+$)/g, "");
	},
	str_replace : function(search, replace, subject)
	{
		if(!(replace instanceof Array)){
			replace=new Array(replace);
			if(search instanceof Array){//If search	is an array and replace	is a string, then this replacement string is used for every value of search
				while(search.length>replace.length){
					replace[replace.length]=replace[0];
				}
			}
		}
		if(!(search instanceof Array))search=new Array(search);
		while(search.length>replace.length){//If replace	has fewer values than search , then an empty string is used for the rest of replacement values
			replace[replace.length]='';
		}
		if(subject instanceof Array){//If subject is an array, then the search and replace is performed with every entry of subject , and the return value is an array as well.
			for(k in subject){
				subject[k]=str_replace(search,replace,subject[k]);
			}
			return subject;
		}
		for(var k=0; k<search.length; k++){
			var i = subject.indexOf(search[k]);
			while(i>-1){
				subject = subject.replace(search[k], replace[k]);
				i = subject.indexOf(search[k],i);
			}
		}
		return subject;
	}


}

// Сохраняем экземпляр объекта в область видимости основного объекта модуля
window.module.funcs = new Funcs();

})(window, $);