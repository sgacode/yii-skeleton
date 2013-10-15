<?php
/**
 * Базовый класс для всех шлюзов таблиц.
 * Реализует паттерн Table Data Gateway. Для работы с БД использует Yii DAO.
 */
abstract class GwBase extends CComponent 
{
	/**
	 * Название используемого соединения с БД
	 * @var string 
	 */
	protected $_dbConnName;

	/**
	 * Объект соединения с БД
	 * @var CDbConnection 
	 */
	protected $_dbConnection;
	
	/**
	 * Название таблицы, к которой реализован шлюз
	 * @var string 
	 */
	protected $_tableName;
	
	/**
	 * Системный язык - влияет на поля для выборки
	 * @var string 
	 */
	protected $_lang;
	
	/**
	 * Карта сопоставлений, определяющая наборы полей (для каждого языка) для каждого поля
	 * @var array 
	 */
	protected $_langDepFieldsMap;
	
	/**
	 * Массив с полями для выборки, установленными в зависимости от системного языка
	 * @var array 
	 */
	protected $_langDepFields;
	
	
	public function __construct()
	{
		// Установка подключения к БД
		if (!isset($this->_dbConnName))
		{
			$this->_dbConnName = 'db';
		}
		if (!Yii::app()->hasComponent($this->_dbConnName))
		{
			throw new CDbException('Connection with name ' . $this->_dbConnName . ' not initialized');
		}
		$this->_dbConnection = Yii::app()->getComponent($this->_dbConnName);
		// Название таблицы должно быть установлено
		if (!isset($this->_tableName))
		{
			throw new CDbException('Table name for gateway ' . __CLASS__ . ' not set');
		}
		// Получаем язык приложения
		$this->_lang = Yii::app()->getLanguage();
		// Устанавливаем поля для выборки в зависимости от языка
		$this->_setLangDepFields();
	}
	
	/**
	 * Поулчение названия поля, зависящего от языка
	 * @param string $fieldName 
	 */
	public function getLangDepField($fieldName)
	{
		if (isset($this->_langDepFields[$fieldName]))
		{
			return $this->_langDepFields[$fieldName];
		}
		return NULL;
	}
	
	/**
	 * Установка полей для выборки в зависимости от языка
	 */
	protected function _setLangDepFields()
	{
		if (is_array($this->_langDepFieldsMap) && sizeof($this->_langDepFieldsMap) > 0)
		{
			$langIndex = 1;
			if ($this->_lang == 'ru')
			{
				$langIndex = 0;
			}
			foreach ($this->_langDepFieldsMap as $field=>$langsFields)
			{
				$this->_langDepFields[$field] = $langsFields[$langIndex];
			}				
		}
	}
	

	/**
	 * Преобразование ассоциативного массива для последующего использования в
	 * построителе запросов
	 * @param array $arr
	 * @return array 
	 */
	protected function _prepArrForBuilder($arr)
	{
		if (!is_array($arr) || empty($arr))
		{
			return;
		}
		$arrForBuilder = array();
		foreach ($arr as $alias => $expr)
		{
			$fieldStr = $expr;
			if (!is_int($alias))
			{
				$fieldStr .= ' AS ' . $alias;
			}
			$arrForBuilder[] = $fieldStr;
		}
		return $arrForBuilder;
	}
	
	/**
	 * Получение данных их кэша
	 * @param string $cacheId
	 * @return mixed
	 */
	protected function _cacheGet($cacheId)
	{
		$cache = Yii::app()->cache;
		if (is_null($cache))
		{
			return FALSE;
		}
		return $cache->get($cacheId);
	}
	
	/**
	 * Сохранение данных в кэш
	 * @param string $cacheId
	 * @param mixed $value
	 * @return mixed 
	 */
	protected function _cacheSet($cacheId, $value)
	{
		$cache = Yii::app()->cache;
		if (is_null($cache))
		{
			return FALSE;
		}
		return $cache->set($cacheId, $value);
	}
}