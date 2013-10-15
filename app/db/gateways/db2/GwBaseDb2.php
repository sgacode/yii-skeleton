<?php
/**
 * Базовый класс шлюза для таблиц БД welt 
 */
abstract class GwBaseDb2 extends GwBase
{
	/**
	 * Название используемого соединения с БД
	 * @var string 
	 */
	protected $_dbConnName = 'db2';
	
}
