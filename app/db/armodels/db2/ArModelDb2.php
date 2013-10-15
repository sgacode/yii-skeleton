<?php
class ArModelDb2 extends ArModelBase
{
	public function getDbConnection()
	{
		return Yii::app()->db2;
	}
}
