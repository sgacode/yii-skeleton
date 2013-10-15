(function() {

/* ------------------------------ Interface ------------------------------------------------------- */
/**
 * Объект, отвечающий за функционирование элементов интерфейса
 */
var Interface = function(){
	// Настройки для элементов интерфейса
	this.conf = {
		datepicker :
		{
			showOn : 'button',
			buttonImage : '/img/calendar.gif',
			buttonImageOnly : true,
			buttonText : '',
			dateFormat : 'dd.mm.yy',
			changeMonth : true,
			changeYear : true,
			numberOfMonths: 2,
			minDate : new Date()
		},
		sm : 
		{
			
		}
	};
	this.printVer = null;
};
Interface.prototype = {
	/**
	 * Инициализация элементов интерфейса
	 */
	init : function()
	{
		// Контролы на странице печати
		if ($('#print-controls').length && $('#print-controls').is(':visible'))
		{
			var printControls = $('#print-controls');
			printControls.find('.print').click(function(){
				window.print();
			});
			printControls.find('.close').click(function(){
				window.close();
			});
		}
		// Обработка ajax-ответов в случаем отсутвия сессии
		$(document).ajaxComplete(function(e, xhr, settings){
			if (xhr && xhr.responseText && xhr.responseText != '' && xhr.responseText.indexOf('NO_SESSION_ERR') != -1)
			{
				window.location.href = window.location.href;
				return false;
			}
		});
	},
	/**
	 * Инициализация кадендаря
	 */
	datepickerInit : function (selector, params)
	{
		if (this.isPrintVer()) return; //Заглушка для печатной версии
		// Параметры по умолчанию
		if (!params)
		{
			var curDate = new Date();
			var curYear = curDate.getFullYear();
			params = {
				'minYear' : curYear - 150,
				'maxYear' : curYear + 150
			};
		}
		// Локализация datepicker
		var appLang = module.langs.getLang(module.langs.getActLang());
		$.datepicker.setDefaults($.datepicker.regional[appLang['uiLang']]);
		// Параметры календаря
		var widgetOpts = {};
		var curWidgetOpts = {
			yearRange: params['minYear'] + ':' + params['maxYear']
		};
		$.extend(widgetOpts, this.conf['datepicker'], curWidgetOpts);
		var datepicker = $(selector).datepicker(widgetOpts);
		// Вызов календаря при клике на поле input
		$(selector).click(function(){
			$(this).datepicker('show');
		});
		return datepicker;
	},
	/**
	 * Обновление виджета selectmenu для списка
	 */
	smRefresh : function(list, conf)
	{
		if (!list) return;
		if (!conf)
		{
			conf = this.conf.sm;
		}
		list.selectmenu('destroy').selectmenu(conf);
	},
	/**
	 * Контроль диапазона при выборе дат
	 */
	dpRangeControl : function(sdateEl, edateEl)
	{
		$(sdateEl).datepicker('option', {
			onSelect: function(selectedDate)
			{
				// Меняем дату окончания при выборе дипазона
				var dateRe = /(\d+).(\d+).(\d+)/;		  	
				var curSdate = new Date(selectedDate.replace(dateRe, '$3/$2/$1'));
				var curEdate = $(edateEl).val();
				if (curEdate)
				{
					curEdate = new Date(curEdate.replace(dateRe, '$3/$2/$1'));
				}
				if (!curEdate || curEdate.getTime() <= curSdate.getTime())
				{
					var newEdate = new Date(curSdate.getTime() + (1 * 24 * 60 * 60 * 1000));
					$(edateEl).datepicker('setDate', newEdate);
				}
			}
		});
	},
	/**
	 * Инициализация виджета toggle
	 */
	widgetToogleInit : function(widgetElem, cSaveState)
	{
		if (!cSaveState)
		{
			cSaveState = false;
		}
		widgetElem.find('.wheader .toggle').on('click', function(){
			if ($(this).hasClass('toggle-opened'))
			{
			   widgetElem.find('.wbody, .wfooter').hide();
			   widgetElem.find('.widget-wrapper').removeClass('opened').addClass('closed');
			}
			else
			{
			   widgetElem.find('.wbody, .wfooter').show();
			   widgetElem.find('.widget-wrapper').removeClass('closed').addClass('opened');
		   }
		});
	},
	/**
	 * Инициализация виджета со свёртывающимся контентом
	 */
	collapsibleInit : function(elem, cSaveState)
	{
		if (!cSaveState)
		{
			cSaveState = false;
		}
		elem.find('.header .toggle').on('click', function(){
			var toggleControl = $(this);
			if (toggleControl.hasClass('toggle-opened'))
			{
				toggleControl.removeClass('toggle-opened').addClass('toggle-closed');
				elem.find('.body').hide();
			}
			else
			{
				toggleControl.removeClass('toggle-closed').addClass('toggle-opened');
				elem.find('.body').show();
			}
		});
	},
	/**
	 * Очистка полей формы
	 */
	clearForm : function(selector)
	{
		var elem = $(selector);
		elem.find('input[type!="radio"][type!="checkbox"]').each(function(){
			$(this).val('');
		});
		elem.find('select').each(function(){
			$(this).find('option').removeAttr('selected');
			$(this).find('option:first').attr('selected','selected');
		});
	},
	/**
	 * Fix для перевода текста кнопок в диалогах. Применяется, так как не было найдено способа динамичесски установить текст для кнопки (jqueryui bug).
	 * При этом, текст для кнопок берётся из блока с классом ui-fix-buttons, который располагается внутри блока, для которого создаётся диалог. 
	 * По умолчанию текст для кнопок задаётся в виде "btn#N", где #N - порядковый номер кнопки
	 */
	uiDialogButtonsFix : function (dialogCont)
	{
		// Получаем тексты для кнопок и формируем из них массив
		var i=0;
		var buttons = {};
		$(dialogCont).find('.ui-fix-buttons span').each(function(){
			buttons[i] = $(this).text();
			i++;
		});
		var j=0;
		// Проходимся по кнопкам в диалоге и заменяем их текст
		$(dialogCont).next().find('button.ui-button span').each(function(){
			$(this).text(buttons[j]);
			j++;
		});	
	},
	/**
	 * Открытие ноого окна с заданным url и параметрами
	 */
	openNewWin : function(url, paramsStr)
	{
		if (!paramsStr)
		{
			paramsStr = '';
		}
		var newWin = window.open(url, 'newWin', paramsStr);
		newWin.focus();
	},
	/**
	 * Выделение строк при наведении
	 */
	rowsHover : function (selector)
	{
		var table = $(selector);
		if (this.isPrintVer()) return; //Заглушка для печатной версии
		// Если в таблице мало строк
		if (table.find('tbody tr').not('.no-hover').length < 4) return;
		table.on('mouseenter', 'tbody tr', function(){
			if ($(this).hasClass('no-hover')) return;
			$(this).addClass('hover'); 
		});
		table.on('mouseleave', 'tbody tr', function(){
			if ($(this).hasClass('no-hover')) return;
			$(this).removeClass('hover'); 
		});
	},
	/**
	 * Отображение/скрытие блока заказа услуг
	 */
	soBlockToggle : function(bCont, fOpenEvent)
	{
		var cLink = bCont.find('.toggle-closed'),
			oLink = bCont.find('.toggle-opened'),
			orderBody = bCont.find('.orders-b-b');		
		//Отображение скрытие формы заказа услуг
		bCont.find('.orders-b-h .link').on('click', function(){
			var firstOpen = bCont.data('f-open');
			//Если скрытый блок свёрнут
			if (cLink.is(':visible'))
			{
				cLink.hide();
				oLink.show();
				orderBody.show();
				// При первом открытие блока вызываем функцию, если она передана
				if (typeof firstOpen == 'undefined' && fOpenEvent)
				{
					bCont.data('f-open', false);
					fOpenEvent();
				}
			}
			else
			{
				cLink.show();
				oLink.hide();
				orderBody.hide();
			}
			$(window).trigger('contentResize');
			// IE fix
			bCont.find('.order-form').toggleClass('base-bg');
		});
	},
	/**
	 * Определение, отображается ли печатная версия или нет
	 */
	isPrintVer : function()
	{
		if (this.printVer == null)
		{
			if (document.location.href.indexOf('print=1') != -1)
			{
				this.printVer = true;
			}
			this.printVer = false;
		}
		return this.printVer;
	},
	/**
	 * Обёртка для вызовов fancybox
	 */
	initFancybox : function(selector, images, params)
	{
		if (selector && $(selector).length)
		{
			$(selector).fancybox(params);
		}
		else
		{
			if (images != null)
			{
				$.fancybox(images, params);
			}
			else
			{
				$.fancybox(params);
			}
		}
	}
};



/* ------------------------------ ProjectDialog --------------------------------------------------- */

/**
 * Базовый класс диалога
 */
var ProjectDialog = function(container, options) {
	if (!container && !options)
	{
		return;
	}
	this.init(container, options);
};
ProjectDialog.prototype = {
	/**
	 * Функция конструктор - может вызываться как из текущего объекта, так и из дочерних
	 */
	init : function(container, options)
	{
		var selfObj = this;
		this.container	= $(container);
		// Опции для диалога
		this.dialogOptions = {
			width 		:	600,
			height		:	'auto',
			position 	:	['center', 130],
			modal		:	true,
			resizable 	:	false,
			draggable 	:	true,
			title		: 	this.container.find('.dialog-title').text(),
			create: function(event, ui)
			{
				selfObj.buttonsFix();
			}
		};
		if (options)
		{
			$(this.dialogOptions).extend(this.dialogOptions, options);
		}
		// Создаём объект ui dialog
		return this.initUiInst();
		
	},
	initUiInst : function()
	{
		this.uiDialogInst = this.container.dialog(this.dialogOptions);
		return this.uiDialogInst;
	},
	/**
	 * Получение объекта ui dialog 
	 */
	getUiDialogInst : function()
	{
		return this.uiDialogInst;
	},
	/**
	 * Получение названия контейнера диалога
	 */
	getContainer : function()
	{
		return this.container;
	},
	/**
	 * Fix для корретного отображения надписей на кнопках диалога
	 */
	buttonsFix : function()
	{
		window.module.appInterface.uiDialogButtonsFix(this.container);
	}
};


/* ------------------------------ uiPersSel widget --------------------------------------------------- */
$.widget('app.uiPersSel', $.ui.combobox, {
	options : {
		namespace  :'module',
		persDataVar : 'persons',
		persAvVar : 'avPersons',
		allowCustomValues : true
	},
	_create : function() {
		$.ui.combobox.prototype._create.call(this);
		var self = this,
			select = this.element;
		// Удаляем выбранную персону при ручном вводе теста в поля
		this.input.on('keypress', function(){
			self.unsetRowPerson();
		});
		this.input.val('');
		this.input.placeholderEnhanced();
	},
	_initAutocomp : function(elem)
	{
		var self = this,
			select = this.element,
			o = this.options;
		elem.autocomplete({
			delay: 0,
			minLength: 0,
			source: function( request, response ) {
				var avPersons = [];
				for (var id in window[o.namespace][o.persAvVar])
				{
					avPersons.push(parseInt(id));
				}
				var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
				response( select.children( "option" ).map(function() {
				var text = $( this ).text();
				if ( this.value && $.inArray(parseInt(this.value), avPersons) != -1 && ( !request.term || matcher.test(text) ) )
						return {
							label: text.replace(
								new RegExp(
								"(?![^&;]+;)(?!<[^<>]*)(" +
								$.ui.autocomplete.escapeRegex(request.term) +
									")(?![^<>]*>)(?![^&;]+;)", "gi"
								), "<strong>$1</strong>" ),
							value: text,
							option: this
					};
				}) );
			},
			select: function( event, ui ) {
				event.preventDefault();
				// Добавляем в доступные для выбора персону, которая была выбрана ранее
				var prevVal = select.find('option:selected').val();
				if (prevVal != '')
				{
					console.log(prevVal);
					self._enablePerson(prevVal);
				}
				// Выбираем option
				ui.item.option.selected = true;
				self._trigger('selected', event, {
					item: ui.item.option
				});
				// Ставим для строки флаг, что персона выбрана
				var num = select.data('num'),
					curPerson = null,
					curVal = ui.item.option.value,
					curRow = select.closest('tr[data-num="' + num +'"]');
				if (typeof window[o.namespace][o.persDataVar][curVal] != 'undefined')
				{
					curPerson = window[o.namespace][o.persDataVar][curVal];
				}
				if (curPerson == null)
				{
					return;
				}
				// Заполняем поля для персон
				curRow.data('person', curPerson.id);
				curRow.find('td.fname input').val(curPerson.fname).removeClass('placeholder');
				curRow.find('td.lname input').val(curPerson.lname).removeClass('placeholder');
				var visaVal = 0;
				if (parseInt(curPerson.visa) == 1)
				{
					visaVal = 1;
				}
				curRow.find('td.visa select').selectmenu('value', visaVal);
				// Удаляем выбранную персону из списка доступных для выбора
				self._disablePerson(curVal);
			},
			change: function( event, ui ) {
				// Если изменение произошло при выборе, ничего не делаем
				if (ui.item != null)
				{
					return;
				}
				// Если изменение произошло при ручном вводе данных
				else
				{
					var num = select.data('num'),
						curRow = select.closest('tr[data-num="' + num +'"]');
					// Добавляем персону в список доступных
					self._enablePerson(curRow.data('person'));
					// Ставим для строки флаг, что персона не выбрана
					curRow.data('person', '');
					select.val('');
				}			
			}
		});
	},
	_disablePerson : function(personId)
	{
		var o = this.options;
		delete window[o.namespace][o.persAvVar][personId];
	},
	_enablePerson : function(personId)
	{
		var o = this.options;
		window[o.namespace][o.persAvVar][personId] = $.extend(
			true, {}, window[o.namespace][o.persDataVar][personId]
		);
	},
	/**
	 * Удаление выбранной персоны для строки, в которой находится combobox
	 */
	unsetRowPerson : function(clearInputs)
	{
		var self = this,
			select = this.element;
		var num = select.data('num'),
			curRow = select.closest('tr[data-num="' + num +'"]'),
			curPersonId = curRow.data('person');
		// Если нужно очистить поля ввода
		if (clearInputs)
		{
			curRow.find('.ui-combobox-input').val('').blur();
		}
		// Если персона была выбрана
		if (curPersonId != '')
		{
			// Добавляем персону в список доступных
			self._enablePerson(curPersonId);
			// Ставим для строки флаг, что персона не выбрана
			curRow.data('person', '');
			select.val('');
		}
	}
});


/* ------------------------------ Selectmenu widget --------------------------------------------------- */
$.widget('ui.selectmenu', $.ui.selectmenu, {
	options : {
		'appendToParent' : true
	},
	/**
	 * Создание option
	 */
	genOption : function(value, label)
	{
		return '<option value="' + value + '">' + label + '</option>';
	},
	/**
	 * Добавление элемента
	 */
	addItem : function(value, label)
	{
		var opt = this.genOption(value, label);
		this.element.append(opt);
		this.element.selectmenu(this.options);
	},
	/**
	 * Обновление списка на основе переданных данных
	 */
	updFromData : function(data, valField, labelField, selected, safeFirst)
	{
		if (!data.length)
		{
			return;
		}
		var list = this.element,
			opts = '';
		for (var i = 0; i < data.length; i++)
		{
			opts += this.genOption(data[i][valField], data[i][labelField]);
		}
		if (safeFirst)
		{
			list.find('option:gt(0)').remove();
			list.append(opts);
		}
		else
		{
			list.html(opts);
		}
		if (selected)
		{
			list.val(selected).attr('selected', 'selected');
		}
		list.selectmenu(this.options);
	},
	/**
	 * Обновление списка на основе переданной строки
	 */
	updFromCont : function(cont, selected, safeFirst)
	{
		var list = this.element;
		if (safeFirst)
		{
			list.find('option:gt(0)').remove();
			list.append(cont);
		}
		else
		{
			list.html(cont);
		}
		if (selected)
		{
			list.val(selected).attr('selected', 'selected');
		}
		list.selectmenu(this.options);
	},
	/**
	 * Обновление (перерисовка) списка
	 */
	refreshList : function()
	{
		this.element.selectmenu(this.options);
	},
	/**
	 * Установка значения для списка
	 */
	setListVal : function(value)
	{
		var list = this.element;
		list.val(value).attr('selected', 'selected');
		list.selectmenu('value', value).selectmenu('change');
	}
});


/* ------------------------------ Fixed cols table --------------------------------------------------- */
var FixColsTable = function(selector, opts)
{
	this.cont = $(selector);
	this.opts = {
		fixedColGap : 20,
		scrlGap : 17,
		wAndCHeightGap : 250,
		heightDiff : 25
	};
	$.extend(this.opts, opts);
	var c = this.cont;
	this.elems = {
		scrollCont : c.find('.scroll-content'),
		fixedCol : c.find('.fixed-col'),
		fixedHead : c.find('.fixed-header')
	};
	this.init();
}
FixColsTable.prototype =  {
	/**
	 * Инициализация
	 */
	init : function()
	{
		var selfObj = this,
			e = this.elems;
		// Установка ширины ячеек
		this.fixedTableAdjust();
		e.fixedHead.scrollLeft(0);
		e.scrollCont.scrollLeft(0);
		e.fixedCol.scrollTop(0);
		e.scrollCont.scrollTop(0);
		// Скролл содержимого таблицы
		e.scrollCont.on('scroll', function(){
			selfObj.tableFixedScroll();
		});
	},
	/**
	 * Сколл содержимого таблицы
	 */
	tableFixedScroll : function()
	{
		var e = this.elems;
		e.fixedHead.scrollLeft(e.scrollCont.scrollLeft());
		e.fixedCol.scrollTop(e.scrollCont.scrollTop());
	},
	/**
	 * Установка ширины ячеек в таблице с фиксированным центром
	 */
	fixedTableAdjust : function()
	{
		var c = this.cont,
			e = this.elems;
		// Ширина фиксированных и скролящегося блоков
		var fixColWidth = e.fixedCol.find('td:first').width() + this.opts['fixedColGap'];
		var fixContWidth = c.width() - fixColWidth;
		c.find('.first-td div').width(fixColWidth);
		e.fixedHead.width(fixContWidth);
		// Добавляем зазор между фиксированным центром и его правым скроллбаром
		fixContWidth += this.opts['scrlGap'];
		e.scrollCont.width(fixContWidth);
		// Fix для случая, когда всего 2 столбца и таблица в скролящемся блоке сужается
		if (e.scrollCont.find('tr:first .content-cell').length < 3)
		{
			var newWidth = e.fixedHead.find('table tr:eq(1) td:first').width();
			e.scrollCont.find('table tr').each(function(){
				$(this).find('td:gt(0)').width(newWidth);
			});
		}
		// Устанавливаем высоту фиксированной колонки и фиксированного блока относительно высоты окна
		var relHeight = $(window).height() - this.opts['wAndCHeightGap'];
		e.scrollCont.height(relHeight);
		e.fixedCol.height(relHeight - this.opts['scrlGap']);
		// Высота скролящегося блока относительно высоты контейнера
		var fixedHeight = e.fixedCol.height();
		var hallsTableHeight = e.fixedCol.find('table').height();
		// Если высота блока со скролящимся контентом больше высоты самого контента
		if ((fixedHeight - hallsTableHeight) > this.opts['heightDiff'])
		{
			//Меняем высоту блока с фиксированным столбцом
			var newFixedHeight =  hallsTableHeight + this.opts['heightDiff'];
			e.fixedCol.css('height', newFixedHeight);
			//Для ie6 просто меняем высоту блока
			if (module.funcs.detectIe6())
			{
				e.scrollCont.css('height', newFixedHeight);
			}
			//Для нормальных браузеров применяем анимацию
			else
			{
				e.scrollCont.animate(
					{height: newFixedHeight}, 500, function() {}
				);
			}
		}
	}
}

/* ------------------------------ BaseForm --------------------------------------------------- */

var BaseForm = function() {};

BaseForm.prototype = {
	/**
	 * Инициализация
	 */
	init : function(sel)
	{
		this.cont = $(sel);
		this.fErrElem = null;
		this.cont.find('.selectmenu-apply').selectmenu({
			'appendToParent' : true
		});
	},
	/**
	 * Инициализация обработчика отправки формы
	 */
	initSmbt : function()
	{
		var selfObj = this,
			c = this.cont;
		// Валидация формы
		this.cont.find('.smbt-btn').on('click', function(e){
			e.preventDefault();
			// Если ошибок нет, отправляем форму
			if (selfObj.validate())
			{
				c.find('form').submit();
			}
			// Если ошибки есть, скролл к первой ошибке
			else
			{
				if (selfObj.fErrElem != null)
				{
					$(selfObj.fErrElem).focus();
				}
			}		
		});
	},
	/**
	 * Проверка данных формы
	 */
	validate : function()
	{
		var selfObj = this,
			c = this.cont;
		var valid = true;
		selfObj.fErrElem = null;
		c.find('.req-line').each(function(){
			var rowErrors = false;
			var errMsg = $(this).find('.errorMessage');
			var rowElem = null;
			if ($(this).find('input:text').length)
			{
				rowElem = $(this).find('input:text');
				if (rowElem.val() == '')
				{
					rowErrors = true;
				}
			}
			else if ($(this).find('input:checkbox').length)
			{
				rowElem = $(this).find('input:checkbox');
				if (!rowElem.prop('checked'))
				{
					rowErrors = true;
				}
			}
			if (rowErrors == true)
			{
				valid = false;
				errMsg.show();
				$(window).trigger('contentResize');
				if (selfObj.fErrElem == null)
				{
					selfObj.fErrElem = rowElem;
				}
			}
			else
			{
				errMsg.hide();
			}
		});
		return valid;
	}
};


// Сохраняем экземпляры объектов в область видимости основного объекта модуля
window.module.appInterface = new Interface();
window.module.appInterface.ProjectDialog = ProjectDialog;
window.module.appInterface.FixColsTable = FixColsTable;
window.module.appInterface.BaseForm = BaseForm;
})(window);