<?php
class LangSelect extends CWidget
{
	/**
	 * Данные по языкам
	 * @var array 
	 */
	public $langsData;
	
	/**
	 * Текущий язык
	 * @var string 
	 */
	public $activeLang = NULL;
	
	/**
	 * Атрибут name для тега select
	 * @var string 
	 */
	public $selectName = 'setlang';
	
	/**
	 * Id элемента form
	 * @var string 
	 */
	public $formId = 'lang-form';

	/**
	 * Html атрибуты для тега select
	 * @var array 
	 */
	public $selectHtmlParams = array();
	
	/**
	 * Шаблон для формы виджета
	 */
	public $widgetTpl = '{form}';

	public function run()
	{
		$formContent = '';
		$formContent .= CHtml::form('', 'get', array('id' => $this->formId));
		if (is_null($this->activeLang))
		{
			$this->activeLang = Yii::app()->language;
		}
		$this->selectHtmlParams = array_merge(
			array('onchange' => '$("#' . $this->formId . '").submit();'),
			$this->selectHtmlParams
		);
		$formContent .= $langsList = CHtml::dropDownList(
			$this->selectName, $this->activeLang, $this->langsData, $this->selectHtmlParams
		);
		$formContent .= CHtml::endForm();
		$this->widgetTpl = str_replace('{form}', $formContent, $this->widgetTpl);
		echo $this->widgetTpl;
	}
}