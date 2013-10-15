<?php
/**
 * Базовый контроллер приложения
 */
class BaseController extends CController
{
	public function init()
	{
		parent::init();
		$this->_iePlug();
	}
	
	protected function _iePlug()
	{
		if(Yii::app()->browser->isBrowser(Browser::BROWSER_IE)
			&& (Yii::app()->browser->getVersion() == '6.0' || Yii::app()->browser->getVersion() == '5.5'))
		{
			header ('Location: /ie6/ie6.html');
		}
	}
	
	/**
	 * Выдача результата в json
	 * @param array $data
	 * @param int $status
	 * @param array $messages 
	 */
	protected function _jsonOutput(array $data, $status = 1, $msgs = array(), $mergeMsgs = TRUE)
	{
		// При необходимости объединяем сообщения об оишибках для всех полей формы
		if (is_array($msgs) && !empty($msgs) && $mergeMsgs == TRUE)
		{
			$mergedMsgs = array();
			foreach ($msgs as $fieldMsgs)
			{
				if (!is_array($fieldMsgs) || empty($fieldMsgs))
				{
					continue;
				}
				foreach ($fieldMsgs as $fieldMsg)
				{
					$mergedMsgs[] = $fieldMsg;
				}
			}
			$msgs = $mergedMsgs;
		}
		// Выдаём ответ в json
		$result = array(
			'data' => $data,
			'status' => $status,
			'msgs' => $msgs,
		);
		header('Content-type: application/json');
		echo CJSON::encode($result);
		Yii::app()->end();
	}
}