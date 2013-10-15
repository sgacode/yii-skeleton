<?php
class BaseFormModel extends CFormModel
{
	
	/**
	 * Проверка captcha
	 * @param string $attribute
	 * @param mixed $params 
	 */
	public function checkCaptcha($attribute, $params)
	{
		if (isset($this->keystring) && !$this->hasErrors('keystring') && 
			$this->keystring != Yii::app()->session->get('keystring'))
		{
			$this->addError($attribute, BsformModule::t('Введёно неверное число'));
		}
	}
	
	/**
	 * Проверка корректности времени
	 * @param string $attribute
	 * @param array $params 
	 */
	public function checkTime($attribute, $params)
    {
		if ($this->hasErrors($attribute))
		{
			return;
		}
		$timeObj = DateTime::createFromFormat('H:i', $this->$attribute);
		if ((int) $timeObj->format('H') > 23 || (int) $timeObj->format('i') > 59)
		{
			$this->addErrors(array($attribute => BsformModule::t('Пожалуйста, укажите корректное время.')));
		}
	}
}
