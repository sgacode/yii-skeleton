<?php
class MainModule extends CWebModule
{
	/**
	 *  Категория перевода для модуля по умолчанию 
	 */
	const DEF_TRANSLATE_CAT = 'MainModule.main';
	
	/**
	 * Параметр, отвечающий за тему 
	 */
	const THEME_PARAM = 'theme';
	
	/**
	 * Время жизни куки с темой 
	 */
	const THEME_COOKIE_LF = 864000; // 10 days

	/**
	 * Используемый шаблон
	 * @var string 
	 */
	public $layout = 'main';
	
	/**
	 * Preload components
	 * @var array  
	 */
	public $preload = array('multilang');

	/**
	 * Набор возможных тем для модуля
	 * @var array 
	 */
	protected static $_avThemes = array('default');

	public function init()
	{
		$n = $this->name;
		// Имопорт необходимых классов
		Yii::app()->setImport(array(
			$n . '.models.*',
			$n . '.components.*',
			$n . '.forms.*',
		));
		// Конфигурация компонентов
		Yii::app()->setComponents(array(
			// Реализация мультиязычности
			'multilang' => array(
				'class' => 'Multilang',
				'defLang' => 'ru',
			),
			'errorHandler'=>array(
				'errorAction' => $n . '/common/error',
			),
		));
		// Иницализация компонентов
		foreach ($this->preload as $preload)
		{
			Yii::app()->preload[] = $preload;
		}
		Yii::app()->preloadComponents();
		// Устанавливаем тему
		$this->_setTheme();
		// Специальный заголовок для ie для корректной работы с cookie
		header('P3P: CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
	}
	
	/**
	 * Перевод сообщения
	 * @see YiiBase::t()
	 */
	public static function t($message, array $params=array(), string $source=NULL, string $language=NULL, $category = NULL)
	{
		if (is_null($category))
		{
			$category = self::DEF_TRANSLATE_CAT;
		}
		return Yii::t($category, $message, $params, $source, $language);
	}

	/**
	 * Установка темы приложения
	 */
	protected function _setTheme()
	{
		$curTheme = NULL;
		// Определяем наличие темы в различных местах
		$paramTheme = Yii::app()->request->getParam(self::THEME_PARAM);
		$patnerTheme = Yii::app()->partner->getParam(self::THEME_PARAM);
		$cookieTheme = Yii::app()->request->cookies[self::THEME_PARAM];
		// Если тема передана в параметре
		if (!is_null($paramTheme))
		{
			$curTheme = $paramTheme;
		}
		// Если тема есть в настройках агента
		elseif (!is_null($patnerTheme))
		{
			$curTheme = $patnerTheme;
		}
		// Если тема есть в сессии
		elseif (!is_null(Yii::app()->session[self::THEME_PARAM])) 
		{
			$curTheme = Yii::app()->session[self::THEME_PARAM];
		}
		// Если тема есть в куке
		elseif (!is_null($cookieTheme))
		{
			$curTheme = $cookieTheme->value;
		}
		// Устанавливаем тему приложения
		if (!is_null($curTheme) && $curTheme != '' && in_array($curTheme, self::$_avThemes))
		{
			Yii::app()->setTheme($curTheme);
			// Сохраняем тему в сессию
			Yii::app()->session[self::THEME_PARAM] = $curTheme;
			// Сохраняем тему в куку
			HCookie::setCookie(
				self::THEME_PARAM, $curTheme, self::THEME_COOKIE_LF
			);
		}
	}
	
}
