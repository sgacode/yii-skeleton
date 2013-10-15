<?php
/**
 * Базовый AR-класс для таблиц основной БД 
 */
abstract class ArModelDb extends ArModelBase
{

	public function getDbConnection()
	{
		return Yii::app()->db;
	}

}
