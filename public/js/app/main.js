(function() {
	
/* -------- Объект, управляющий функционалом  основного модуля  ----------------*/
var Main = function() {};
Main.prototype = {
	/**
	 * Функциии, вызывающиеся в момент $(document).ready для разделов
	 */
	inits :
	{
		/**
		 * Гостиницы
		 */
		hotel :
		{
			// Стартовая страница: форма поиска
			index : function()
			{
				this.hotelsSearch = new this.HotelsSearch(this.searchReq);
			},
			// Форма заказа
			order : function()
			{
				this.hOrderForm = new this.HOrderForm();
			}
		},
		/**
		 * Заявка
		 */
		application :
		{
			// Информация по заказу
			summary : function()
			{
				
			},
			// Контактная информация
			contacts : function()
			{
				this.contactsForm  = new this.ContactsForm('#contacts-cont');
			},
			// Результирующая информация по заказу
			result : function()
			{
				this.onlineChecker = new this.OnlineChecker(this.appId, this.onlineOrders);
			}
		},
		/**
		 * Услуги (транспорт, экскурсии)
		 */
		service : 
		{
			// Заказ
			order : function()
			{
				var c = $('#services-cont'),
					selfObj = this;
				// Элементы интерфейса
				c.find('.prices-tabs').tabs();
				c.find('#serv-city').selectmenu({
					'appendToParent' : true
				});
				c.find('#serv-city').on('change', function(){
					$('.city-swith-b form').submit();
				});
				// Действие, выполняемое при успешной обработке данных по персонам
				var updPers = function(otherOrderForm)
				{
					// Выполняем метод базового объекта
					selfObj.OrderForm.prototype.personsSuccess.apply(this);
					// Обновляем список персон в форме заказа экскурсий
					if (typeof selfObj[otherOrderForm] != 'undefined'
						&& typeof this.newPersons == 'object'
						&& !module.funcs.isEmpty(this.newPersons))
					{
						selfObj[otherOrderForm].personsSel.addPersons(this.newPersons);
					}
				}
				// Скрытие/отображение блоков заказа
				this.appInterface.soBlockToggle(
					c.find('#tr-orders-b'),
					function()
					{
						selfObj.TOrderForm.prototype.personsSuccess = function(){
							updPers.apply(this, ['eOrderForm'])
						};
						selfObj.tOrderForm = new selfObj.TOrderForm();
					}
				);
				if (this.openTr)
				{
					c.find('#tr-orders-b .toggle-closed').trigger('click');
				}
				this.appInterface.soBlockToggle(
					c.find('#exc-orders-b'),
					function()
					{
						selfObj.EOrderForm.prototype.personsSuccess = function(){
							updPers.apply(this, ['tOrderForm'])
						};
						selfObj.eOrderForm = new selfObj.EOrderForm();
					}
				);
				if (this.openExc)
				{
					c.find('#exc-orders-b .toggle-closed').trigger('click');
				}
			}
		},
		/**
		 * Запрос цен
		 */
		request :
		{
			// Форма запроса 
			index : function()
			{
				this.requestForm  = new this.RequestForm('#preq-cont');
			},
			// подтверждение заказа
			confirm : function()
			{
				this.confForm = new this.ConfForm('#reqconf-cont');
			}
		}
	},
	/**
	 * Автозагрузка для указанного раздела
	 */
	sectionInit : function(section)
	{
		var sectionParts = section.split('.');
		if (sectionParts && sectionParts.length > 1)
		{
			this.inits[sectionParts[0]][sectionParts[1]].call(this);
		}
	}
};


/**
 * Передаём в глобальную область видимости объект
 */
window.module = new Main();

/* -------------------------  $(document).ready для всех разделов -----------------------*/
$(document).ready(function(){
	window.module.appInterface.init();
	
});
/* ------------------------------------------------------------------------------------- */

})(window);


