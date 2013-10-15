<?php
/**
 * Класс, реализующий сортировку многомерного массива по указанному параметру 
 */
class ArrSorter
{
	/**
	 * Название параметра, по которому идёт сортировка
	 * @var string 
	 */
	protected static $_sortParam;
	
	/**
	 * Тип сортировки
	 * @var string 
	 */
	protected static $_sortMode;

	/**
	 * Сортировка массива ассоциативных массивов  по указанному параметру
	 * @param array $arr
	 * @param string $sortParam
	 * @param string $sortMode
	 * @return array
	*/
	public static function arrSortByParam($arr, $sortParam, $sortMode = 'ASC')
	{
		self::$_sortParam = $sortParam;
		self::$_sortMode = $sortMode;
		$size = sizeof($arr);
		//Массив из элементов, для которых указанный параметр = 0 - помещаются в конец
		$orderEnd = array();
		$k = 0;
		for($i = 0; $i < $size; $i++)
		{
			$tmpArr = $arr[$i];
			//Если указанный параметр равен нулю или отсутствует то помещаем элементы в отдельный массив
			if (!isset($arr[$i][self::$_sortParam]) || $arr[$i][self::$_sortParam] == 0)
			{
				$orderEnd[$k] = $tmpArr;
				$k++;
				unset($arr[$i]);
			}
		}
		//Сортируем массив
		usort($arr, array(__CLASS__, 'cmpByParam'));
		//Если были элементы, для которых указанный параметр = 0 , то добавляем их в конец массива
		if (sizeof($orderEnd)) 
		{
			$arr = array_merge($arr, $orderEnd);
		}
		return $arr;
	}
	
	
	/***
	 * Функция для пользовательской сортировки массива ассоциативных массивов
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function cmpByParam($a ,$b)
	{
		//Если элементы равны
        if($a[self::$_sortParam] == $b[self::$_sortParam])
		{
            return 0;
        }
		//Если идёт сортировка по убыванию
		if (self::$_sortMode == 'DESC')
		{
			$res = ($a[self::$_sortParam] > $b[self::$_sortParam]) ? -1 : 1;
		}
		//Если идёт сортировка по возрастанию
        else
		{
			$res = ($a[self::$_sortParam] < $b[self::$_sortParam]) ? -1 : 1;
		}
		return $res;
    }
}