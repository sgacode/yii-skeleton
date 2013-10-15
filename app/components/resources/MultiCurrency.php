<?php
/**
 * Компонент для реализации мультивалютности
 */
class MultiCurrency extends MultiParam
{
	
	/**
	 * Параметр в url, отвечающий за валюту 
	 * @var string 
	 */
	public $urlParam = 'curr';
	
	/**
	 * Параметр в url, отвечающий за смену параметра 
	 * @var string 
	 */
	public $urlParamSw = 'setcurr';
	
	/**
	 * @see MultiParam
	 * @var string 
	 */
	public $cookieName = 'curr';

	/**
	 * Валюта, относительно которой идёт пересчёт курса (основная валюта)
	 * @var string 
	 */
	protected $_mainCurr = 'RUB';
	
	/**
	 * Доступные валюты
	 * @var array 
	 */
	protected $_avCurr;
	
	/**
	 * Коды доступных валют
	 * @var array 
	 */
	protected $_avCurrCodes;
	
	/**
	 * Текущая валюта
	 * @var type 
	 */
	protected $_curCurrency = null;

	public function __construct()
	{
		// Загружаем список доступных валют
		$this->_loadAvCurr();
		// Загружаем данные по курсам
		$this->_loadCurrRates();
	}
	
	public function init()
	{
		// Если идёт смена валюты (передан соответствующий параметр в url, меняем валюту)
		$request = Yii::app()->getRequest();
		$currToSet = $request->getParam($this->urlParamSw);
		if (!is_null($currToSet) && in_array($currToSet, $this->_avCurrCodes))
		{
			$this->_paramSwitch($currToSet);
		}
		// Устанавливаем текущую валюту
		$this->_setCurCurrency();
	}
	
	/**
	 * Получение текущей валюты
	 * @return array 
	 */
	public function getActiveCurr()
	{
		return $this->_avCurr[$this->_curCurrency];
	}
	
	/**
	 * Получение списка доступных валют
	 * @return array 
	 */
	public function getAvCurr()
	{
		return $this->_avCurr;
	}
	
	/**
	 * Конвертация валюты из основной валюты в указанную
	 * @param type $amount
	 * @param type $currCode
	 * @return int 
	 */
	public function convert($amount, $currCode)
	{
		if ($currCode == $this->_mainCurr)
		{
			return $amount;
		}
		if (!isset($this->_avCurrCodes[$currCode]))
		{
			return 0;
		}
		return round($amount / $this->_avCurr[$currCode]->rate);
	}
	
	/**
	 * $_mainCurr getter
	 * @return string 
	 */
	public function getMainCurr()
	{
		return $this->_mainCurr;
	}
	
	/**
	 * $_mainCurr setter
	 * @param string $_mainCurr 
	 */
	public function setMainCurr($_mainCurr)
	{
		$this->_mainCurr = $_mainCurr;
	}
	
	/**
	 * Получение кода текущей валюты
	 * @return string 
	 */
	public function getCurrCurrency()
	{
		return $this->_curCurrency;
	}

	/**
	 * Загрузка списка доступных валют 
	 */
	protected function _loadAvCurr()
	{
		$currGw = new GwCurrency();
		$currs = $currGw->getCurrency();
		if (is_null($currs))
		{
			throw new CException('System currency data not found');
		}
		foreach ($currs as $curr)
		{
			$this->_avCurr[$curr['code']] = $curr;
			$this->_avCurrCodes[] = $curr['code'];
		}
	}
	
	/**
	 * Загрузка данных по курсам валют 
	 */
	protected function _loadCurrRates()
	{
		// Получаем курсы
		$ratesGw = new GwCurrencyRates();
		$rates = $ratesGw->getRates();
		$rates = HelpFunctions::listToKeysArray($rates, 'cur_alias');
		// Распределяем курсы по валютам
		foreach ($this->_avCurr as $code => $curr)
		{
			if ($curr['code'] == $this->_mainCurr)
			{
				$this->_avCurr[$code]['rate'] = 1;
				continue;
			}
			if (isset($rates[$curr['welt_alias']]))
			{
				$this->_avCurr[$code]['rate'] = $rates[$curr['welt_alias']]['rate'];
			}
			else
			{
				$this->_avCurr[$code]['rate'] = 0;
			}
		}
	}
	
	/**
	 * Установка текущей валюты
	 */
	protected function _setCurCurrency()
	{
		// По умолчанию - основная валюта
		$curCurrency = $this->_mainCurr;
		// Получаем значение языка из всех возможных мест
		$request = Yii::app()->getRequest();
		$currCookie = $request->cookies[$this->cookieName];
		$paramsCurr = $request->getParam($this->urlParam);
		$partnerCurr =  Yii::app()->partner->getParam('currency');
		// Если валюта есть в переданных параметрах
		if (!is_null($paramsCurr))
		{
			$curCurrency = $paramsCurr;
		}
		// Если установлена кука
		elseif (!is_null($currCookie))
		{
			$curCurrency = $currCookie->value;
		}
		// Если валюта есть в параметрах партнёра
		elseif (!is_null($partnerCurr))
		{
			$curCurrency = $partnerCurr;
		}
		if (!in_array($curCurrency, $this->_avCurrCodes))
		{
			$curCurrency = $this->_mainCurr;
		}
		// Устанавливаем текущую валюту
		$this->_curCurrency = $curCurrency;
		// Ставим куку
		$this->_setCookie($curCurrency);
	}
	
}
