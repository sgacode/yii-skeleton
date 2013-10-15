(function() {
	
/* ------------------------------ Lang ------------------------------------------------------- */
/**
 * Объект, содержащий данные о системных языках
 */
var Langs = function() {
	/**
	 * Исходный язык сообщения
	 */
	this.sourceLang = 'ru',
	this.activeLang = this.sourceLang;
	this.avLangs = [];
	this.langsData = [];
};
Langs.prototype = 
{

	/**
	 * Добавление данных о языке
	 */
	addLang : function(lang, data)
	{
		this.avLangs.push(lang);
		this.langsData[lang] = data;
	},
	/**
	 * Получение данных о языке
	 */
	getLang : function(lang)
	{
		if ($.inArray(lang, this.avLangs) != -1)
		{
			return this.langsData[lang];
		}
		return null;
	},
	/**
	 * Установка активного языка
	 */
	setLang : function(lang)
	{
		if ($.inArray(lang, this.avLangs) != -1)
		{
			this.activeLang = lang;
		}
	},
	/**
	 * Получение активного языка
	 */
	getActLang : function()
	{
		return this.activeLang;
	},
	/**
	 * Перевод сообщений
	 */
	t : function(value)
	{
		if (this.activeLang == null || this.activeLang == this.sourceLang)
		{
			return value;
		}
		var source = this.langsData[this.activeLang].messages;
		if (source && source[value] != 'undefined')
		{
			return source[value];
		}
		return value;
	}
	
}
langs = new Langs();

// Добавляем данные по языкам

/* ------------------------------ RU ------------------------------------------------------- */
langs.addLang('ru', {
	uiLang : 'ru',
	messages : null
});
/* ------------------------------ EN ------------------------------------------------------- */
langs.addLang('en', {
	uiLang : 'en-GB',
	messages :
	{
		'тест' : 'test'
	}
});


// Сохраняем экземпляр объекта в область видимости основного объекта модуля
window.module.langs = langs;

})(window);