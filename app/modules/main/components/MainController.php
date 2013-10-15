<?php
/**
 * Базовый контроллер модуля main
 */
class MainController extends BaseController
{

	/**
	 * Заголовок страниц по умолчанию
	 * @var string
	 */
	public $pageTitle = 'title';
	

	/**
	 * Базовый url
	 * @var string 
	 */
	protected $_baseUrl;
	
	/**
	 * Текущий язык
	 * @var string 
	 */
	protected $_lang;

	
	/**
	 * Проверка наличия и корректности id заявки в сесии, обработка параметров
	 * @param mixed $action 
	 */
	public function beforeAction($action)
	{
		// Обработка параметров запроса, установка нужных параметров 
		$this->_processParams();
		return parent::beforeAction($action);
	}

	/**
	 * Обработка параметров запроса, установка нужных параметров 
	 */
	protected function _processParams()
	{
		// Определяем baseUrl
		$baseUrl = (Yii::app()->theme ? Yii::app()->theme->baseUrl : Yii::app()->request->baseUrl);
		$this->_baseUrl = $baseUrl;
		// Общие параметры для view
		$this->_lang = Yii::app()->multilang->getCurLang();
	}
	
	/**
	 * Получение параметра из url/cookie
	 * @param string $param 
	 * @return mixed
	 */
	protected function _getParam($param)
	{
		$paramVal = Yii::app()->request->getParam($param);
		if (is_null($paramVal))
		{
			$paramVal = Yii::app()->request->cookies[$param];
		}
		return $paramVal;
	}

}
