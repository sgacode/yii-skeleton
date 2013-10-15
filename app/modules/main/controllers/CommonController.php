<?php
class CommonController extends MainController
{
	/**
	 * Сообщение об ошибке 
	 */
	public function actionError()
	{
		// Нужно для корректного отображения файлов темы
		$baseUrl = (Yii::app()->theme ? Yii::app()->theme->baseUrl : Yii::app()->request->baseUrl);
		$this->_baseUrl = $baseUrl;
		if($error = Yii::app()->errorHandler->error)
		{
			switch ($error['code'])
			{
				case 404 : $msg = 'Страница не найдена'; break;	
				default : $msg = 'Ошибка приложений'; break;
			}
			$this->pageTitle = $this->module->t('Произошла ошибка');
			$this->render('error', array('error' => $error, 'msg' => $msg));
		}
	}
	
	/**
	 * Kcaptcha
	 */
	public function actionCaptcha()
	{
		Yii::import('application.components.other.kcaptcha.Kcaptcha');
		//require_once('Kcaptcha.php');
   	 	header('Content-type: image/jpeg');
		$capctha = new Kcaptcha();
		$capctha->setKeyString();
	}
}