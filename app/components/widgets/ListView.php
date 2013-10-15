<?php
Yii::import('zii.widgets.CListView');
class ListView extends CListView
{
	public function renderKeys()
	{
		return;
	}
	
	public function renderEmptyText()
	{
		$emptyText = $this->emptyText===null ? Yii::t('zii','No results found.') : $this->emptyText;
		echo CHtml::tag('div', array('class' => 'empty infomsg infomsg32'), $emptyText);
	}
}