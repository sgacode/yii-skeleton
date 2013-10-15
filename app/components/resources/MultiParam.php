<?php
/**
 * Базовый класс для компонентов, описывающий мульти-параметры системы
 * (мультиязычность, мультивалютность)
 */
abstract class MultiParam extends CComponent
{
	/**
	 * Параметр в url, отвечающий за смену параметра 
	 * @var string 
	 */
	public $urlParamSw;
	
	/**
	 * Имя куки для параметра
	 * @var string 
	 */
	public $cookieName;
	
	/**
	 * Время жизни куки для параметра
	 * @var int 
	 */
	public $cookieLifetime = 31104000;
	
	/**
	 * Поля в зависимости от языка
	 * @var type 
	 */
	protected $_langDepFields = array();

	/**
	 * Действия по смене параметра
	 * @param string $value 
	 */
	protected function _paramSwitch($value)
	{
		$request = Yii::app()->getRequest();
		// Ставим куку
		$this->_setCookie($value);
		// Делаем редирект на тот же url, но без параметра смены url
		$curUrl = $request->getUrl();
		$curUrl = HelpFunctions::deleteUrlParam(
				$curUrl, 
				$this->urlParamSw
		);
		$request->redirect($curUrl);
	}
	
	/**
	 * Установка куки
	 * @param string $cookieVal 
	 */
	protected function _setCookie($cookieVal)
	{
		HCookie::setCookie(
			$this->cookieName, $cookieVal, $this->cookieLifetime
		);
	}
	
}