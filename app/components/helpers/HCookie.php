<?php
class HCookie
{
	/**
	 * Установка cookie
	 * @param string $name
	 * @param mixed $value 
	 * @param int $lf
	 */
	public static function setCookie($name, $value, $lf)
	{
		$existCookie = Yii::app()->request->cookies[$name];
		if (is_null($existCookie) || $existCookie->value != $value)
		{
			if (isset(Yii::app()->request->cookies[$name]))
			{
				unset(Yii::app()->request->cookies[$name]);
			}
			$cookie = new CHttpCookie($name, $value);
			$cookie->expire = time() + ($lf);
			$cookie->path = '/';
			Yii::app()->request->cookies->add($name, $cookie);
		}
	}
}