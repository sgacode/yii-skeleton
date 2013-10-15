<?php
/**
 * Компонент для реализации мультиязычности 
 */
class Multilang extends MultiParam
{
	
	/**
	 * Параметр в url, отвечающий за язык 
	 * @var string 
	 */
	public $urlParam = 'lang';
	
	/**
	 * Параметр в url, отвечающий за смену параметра 
	 * @var string 
	 */
	public $urlParamSw = 'setlang';
	
	/**
	 * @see MultiParam
	 * @var string 
	 */
	public $cookieName = 'lang';

	/**
	 * Язык по умолчанию
	 * @var string 
	 */
	protected $_defLang = 'ru';
	
	/**
	 * Доступные языки
	 * @var array 
	 */
	protected $_avLangs;
	
	/**
	 * Коды доступных языков
	 * @var array 
	 */
	protected $_avLangsCodes;

	public function __construct()
	{
		// Загружаем список доступных языков
		$this->_loadAvLangs();
	}

	public function init()
	{
		// Если идёт смена языка (передан соответствующий параметр в url, меняем язык)
		$request = Yii::app()->getRequest();
		$langToSet = $request->getParam($this->urlParamSw);
		if (!is_null($langToSet) && in_array($langToSet, $this->_avLangsCodes))
		{
			$this->_paramSwitch($langToSet);
		}
		// Устанавливаем системный язык
		$this->_setSysLang();
	}
	
	/**
	 * $_defLang getter
	 * @return string 
	 */
	public function getDefLang()
	{
		return $this->_defLang;
	}

	/**
	 * $_defLang setter
	 * @param string $defLang 
	 */
	public function setDefLang($defLang)
	{
		$this->_defLang = $defLang;
		
	}
	
	/**
	 * Получение списка доступных языков  
	 * @return array
	 */
	public function getLangsList()
	{
		return $this->_avLangs;
	}
	
	/**
	 * Получение текущего языка
	 * @return array 
	 */
	public function getCurLang()
	{
		$curLang = Yii::app()->language;
		return array(
			'code' => $curLang, 'title' => $this->_avLangs[$curLang]
		);
	}

	/**
	 * Загрузка списка доступных языков 
	 */
	protected function _loadAvLangs()
	{
		$langsGw = new GwLanguages();
		$langs = $langsGw->getLangs();
		if (is_null($langs))
		{
			throw new CException('System languages data not found');
		}
		foreach ($langs as $lang)
		{
			$this->_avLangs[$lang['code']] = $lang['title'];
		}
		$this->_avLangsCodes = array_keys($this->_avLangs);
	}
	
	/**
	 * Установка системного языка 
	 */
	protected function _setSysLang()
	{
		$request = Yii::app()->getRequest();
		// Получаем значение языка из всех возможных мест
		$sysLang = $this->_defLang;
		$langCookie = $request->cookies[$this->cookieName];
		$paramsLang = $request->getParam($this->urlParam);
		$partnerLang = NULL;
		if (Yii::app()->hasComponent('partner'))
		{
			$partnerLang =  Yii::app()->partner->getParam('lang');
		}
		// Если язык есть в переданных параметрах
		if (!is_null($paramsLang))
		{
			$sysLang = $paramsLang;
		}
		// Если установлена языковая кука
		elseif (!is_null($langCookie))
		{
			$sysLang = $langCookie->value;
		}
		// Если язык есть в параметрах партнёра
		elseif (!is_null($partnerLang))
		{
			$sysLang = $partnerLang;
		}
		if (!in_array($sysLang, $this->_avLangsCodes))
		{
			$sysLang = $this->_defLang;
		}
		// Устанавливаем текущий язык
		Yii::app()->setLanguage($sysLang);
		// Ставим куку (кроме ajax запросов)
		$this->_setCookie($sysLang);
	}


}
