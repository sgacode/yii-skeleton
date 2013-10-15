<?php
/**
 * Базовый AR-класс приложения 
 */
abstract class ArModelBase extends CActiveRecord
{
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
	
	public function init()
	{
		// Получаем язык приложения
		$this->_lang = Yii::app()->getLanguage();
		// Устанавливаем поля для выборки в зависимости от языка
		$this->_setLangDepFields();
	}
	
	/**
	 * Действия, выполняемые до добавления/изменения
	 */
	public function beforeSave()
	{
		if ($this->isNewRecord)
		{
			$this->beforeAdd();
		}
		else
		{
			$this->beforeEdit();
		}
		return parent::beforeSave();
	}
	
	/**
	 * Действия, выполняемые после добавления/изменения
	 */
	public function afterSave()
	{
		if ($this->isNewRecord)
		{
			$this->afterAdd();
		}
		else
		{
			$this->afterEdit();
		}
		return parent::afterSave();
	}
	
	/**
	 * Действия, выполняемые при добавлении 
	 */
	public function beforeAdd() {}
	
	/**
	 * Действия, выполняемые при изменении
	 */
	public function beforeEdit() {}
	
	/**
	 * Действия, выполняемые после добавления
	 */
	public function afterAdd() {}
	
	/**
	 * Действия, выполняемые после изменения
	 */
	public function afterEdit() {}

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
}
