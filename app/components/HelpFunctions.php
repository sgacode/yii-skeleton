<?php
class HelpFunctions {
	
	/**
	 * Проверка даты на корректность
	 * @param string $date
	 * @param string format
	 * @param bool $checkNotInPast
	 * @return 
	 */
	public static function checkDate($date, $format = 'd.m.Y', $checkNotInPast = TRUE)
	{
		// Корректный формат
		if ($format == 'd.m.Y' && !preg_match('/^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$/', $date))
		{
			return false;
		}
		// Дата не в прошлом
		if ($checkNotInPast === TRUE)
		{
			$dateObj = DateTime::createFromFormat($format, $date);
			//Добавляем к проверяемой дате минимально возможные час и минуту (для корректного сравнения дат)
			$dateObj->setTime(0, 0, 0);
			$curDate = new DateTime();
			if ($dateObj <= $curDate)
			{
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Проверка периода на корректность
	 * @param string $startDate
	 * @param string $endDate
	 * @param string format
	 * @param bool $checkNotInPast
	 * @return 
	 */
	public static function checkPeriod($startDate, $endDate, $format = 'd.m.Y', $checkNotInPast = TRUE)
	{
		// Даты корректные
		if (!self::checkDate($startDate, $format, $checkNotInPast)
			|| !self::checkDate($endDate, $format, $checkNotInPast))
		{
			return false;
		}
		// Дата начала меньше даты окончания;
		$startDate = DateTime::createFromFormat($format, $startDate);
		$endDate = DateTime::createFromFormat($format, $endDate);
		if ($startDate >= $endDate)
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Текущая дата для записи в mysql
	 * @return 
	 */
	public static function getDateTimeMysql()
	{
		$curDate = new DateTime();
		return $curDate->format('Y-m-d H:i:s');
	}
	
	/**
	 * Преобразование даты для записи в mysql
	 * @param string $date
	 * @return string
	 */
	public static function dateToMysql($date, $format = 'd.m.Y')
	{
		$curDate = DateTime::createFromFormat($format, $date);
		return $curDate->format('Y-m-d');
	}
	
	
	/**
	 * Конвертирует дату в unix-формат
	 * @param string $date Дата в формате dd.mm.yyyy
	 * @return 
	 */
	public static function convertDateUnix($date) 
	{
		list($day,$month,$year) = explode('.', $date);
		$time = mktime(0, 0, 0, $month, $day, $year);
		return $time;	
	}
	
	/**
	 * Преобразование массива в строку с перечислением элементов для использования в запросе вида
	 * SELECT field FROM table WHERE id IN(?)
	 * @param array $arr
	 * @return string
	 */
	public static function arrayToSqlWhere($arr)
	{
		$arraySize = sizeof($arr);
		$arrStr = '';
		//Если в массиве 1 элемент
		if ($arraySize == 1)
		{
			reset($arr);
			$firstItemIndex = key($arr);
			if (is_int($arr[$firstItemIndex]))
			{
				$arrStr = $arr[$firstItemIndex];
			}
			else
			{
				$arrStr = "'" . $arr[$firstItemIndex] . "'";
			}
		}
		else
		{
			$i = 0;
			//Если в массиве несколько элементов
			foreach ($arr as $key=>$item)
			{
				$arrStr .= self::arrayToSqlWhere(array(0=>$arr[$key]));
				if ($i != ($arraySize-1))
				{
					$arrStr .= ',';
				}
				$i++;
			}
		}
		return $arrStr;
	}
	
	
	/**
	 * Преобразование набора строк/списка в ассоциативный массив, ключами которого
	 * будут уникальные значения указанного поля
	 * @param array $rows 
	 * @param string $keyField
	 * @param bool $araysInRes Если установлено в TRUE, то все элементы в результирующем массиве будут также массивами
	 * @param int $pushMode Режим формирования массива.
	 *				1 - создавать массив из значений в случае одинаковых ключей
	 *				2 - не создавать массив, заменять последним из значений
	 */
	public static function listToKeysArray($list, $keyField, $araysInRes = FALSE, $pushMode = 1)
	{
		$arr = array();
		foreach ($list as $item)
		{
			$key = $item[$keyField];
			if (!isset($arr[$key]))
			{
				if ($araysInRes === FALSE)
				{
					$arr[$key] = $item;
				}
				else
				{
					$arr[$key] = array(0 => $item);
				}
			}
			elseif ($pushMode == 1 || $araysInRes === TRUE)
			{
				if (!is_array($arr[$key]))
				{
					$firstVal = $arr[$key];
					$arr[$key] = array(0 => $firstVal);
				}
				$arr[$key][] = $item;
			}
		}
		return $arr;
	}
	
	/**
	 * Форматирование суммы для вывода
	 * @param mixed $sum
	 * @return 
	 */
	public static function sumFormat($sum)
	{
		return number_format($sum, 2, ',', ' ');
	}
	
	
	/**
	 * Проверка на администратора по ip и вывод информации в firebug по необходимости
	 * @param mixed $data
	 * @param string $target
	 * @return mixed
	 */
	public static function showForAdmin($data, $target = 'console')
	{
		if ($_SERVER['REMOTE_ADDR'] != Yii::app()->params['adminIp'])
		{
			return;
		}
		if ($target != 'console')
		{
			CVarDumper::dump($data, 20, TRUE);
		}
		else
		{
			Yii::log($data);
		}
	}
	
	/**
	 * Проверка ip на соответствие ip администратора
	 * @param string $ip
	 * @return bool 
	 */
	public static function isAdminIp($ip = '')
	{
		if (!isset($ip) || $ip == '')
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		$adminIp = Yii::app()->params['adminIp'];
		if ($ip == $adminIp)
		{
			return true;
		}
		return false;
	}
	
	
	/**
	 * Определение браузера
	 * @param string $agent
	 * @return 
	 */
	public static function userBrowser($agent) 
	{
	preg_match("/(MSIE|Opera|Firefox|Chrome|Version|Opera Mini|Netscape|Konqueror|SeaMonkey|Camino|Minefield|Iceweasel|K-Meleon|Maxthon)(?:\/| )([0-9.]+)/", $agent, $browser_info); // регулярное выражение, которое позволяет отпределить 90% браузеров
        list(,$browser,$version) = $browser_info; // получаем данные из массива в переменную
        if (preg_match("/Opera ([0-9.]+)/i", $agent, $opera)) return 'Opera '.$opera[1]; // определение _очень_старых_ версий Оперы (до 8.50), при желании можно убрать
        if ($browser == 'MSIE') { // если браузер определён как IE
                preg_match("/(Maxthon|Avant Browser|MyIE2)/i", $agent, $ie); // проверяем, не разработка ли это на основе IE
                if ($ie) return $ie[1].' based on IE '.$version; // если да, то возвращаем сообщение об этом
                return 'IE '.$version; // иначе просто возвращаем IE и номер версии
        }
        if ($browser == 'Firefox') { // если браузер определён как Firefox
                preg_match("/(Flock|Navigator|Epiphany)\/([0-9.]+)/", $agent, $ff); // проверяем, не разработка ли это на основе Firefox
                if ($ff) return $ff[1].' '.$ff[2]; // если да, то выводим номер и версию
        }
        if ($browser == 'Opera' && $version == '9.80') return 'Opera '.substr($agent,-5); // если браузер определён как Opera 9.80, берём версию Оперы из конца строки
        if ($browser == 'Version') return 'Safari '.$version; // определяем Сафари
        if (!$browser && strpos($agent, 'Gecko')) return 'Browser based on Gecko'; // для неопознанных браузеров проверяем, если они на движке Gecko, и возращаем сообщение об этом
        return $browser.' '.$version; // для всех остальных возвращаем браузер и версию
    }
	
	/**
	 * Преобразование первого символа в строке в верхний регистр. Была написана, т.к. ucfirst некорректно работает с
	 * русскими символами даже с установленной локалью. 
	 * @param string $str
	 * @return 
	 */
	public static function mbUtfUcfirst($str)
	{
		preg_match('/^(.{1})(.*)$/us', $str, $matches);
		$str = mb_convert_case($matches[1], MB_CASE_UPPER, "UTF-8").$matches[2];
		return $str;
	}
	
	/**
	 * strtolower
	 * @param string $str
	 * @return string 
	 */
	public static function strtolower($str)
	{
		return mb_strtolower($str, 'UTF-8');
	}

	/**
	 * Объединение массивов с заменой элементов
	 * @param array $a
	 * @param array $b
	 */
	public static function arrsMerge(&$a, $b)
	{
		foreach($b as $child => $value)
		{
			if(isset($a[$child]))
			{ 
				if(is_array($a[$child]) && is_array($value))
				{
					self::arrsMerge($a[$child], $value);
				}
			}
			else
			{
				$a[$child] = $value;
			}
		}
	}
	
	/**
	 * Удаление параметра из url
	 * @param string $url
	 * @param string $param 
	 * @return string
	 */
	public static function deleteUrlParam($url, $param)
	{
		$queryParams = array();
		list($host, $queryStr) = explode('?', $url);
		if ($queryStr == '')
		{
			return;
		}
		parse_str($queryStr, $queryParams);
		if (isset($queryParams[$param]))
		{
			unset($queryParams[$param]);
		}
		$queryStr = http_build_query($queryParams);
		$url = implode('?', array($host, $queryStr));
		return $url;
	}
	
	/**
	 * Замена/добавление параметра в url
	 * @param string $url
	 * @param string $param ,
	 * @param mixed $value
	 * @return string
	 */
	public static function addUrlParam($url, $param, $value)
	{
		// Удаляем параметр, если он уже есть в url
		$url = self::deleteUrlParam($url, $param);
		$urlLen = strlen($url);
		// Добавляем параметр в url
		if ($url[$urlLen - 1] != '?')
		{
			$url .= '&';
		}
		$url .= $param . '=' . $value;
		return $url;
	}
	
	/**
	 * Транслитерация
	 * @param string $value
	 * @return string
	 */
	public static function translit($value)
	{
		// Массив символов
		$letters = array(
			"а" => "a", "б" => "b", "в" => "v", "г" => "g", "д" => "d", "е" => "e",
			"ё" => "e", "ж" => "zh", "з" => "z", "и" => "i", "й" => "j", "к" => "k",
			"л" => "l", "м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
			"с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h", "ц" => "c",
			"ч" => "ch", "ш" => "sh", "щ" => "sh", "ы" => "i", "ь" => "", "ъ" => "",
			"э" => "e", "ю" => "yu", "я" => "ya",
			"А" => "A", "Б" => "B", "В" => "V", "Г" => "G", "Д" => "D", "Е" => "E",
			"Ё" => "E", "Ж" => "ZH", "З" => "Z", "И" => "I", "Й" => "J", "К" => "K",
			"Л" => "L", "М" => "M", "Н" => "N", "О" => "O", "П" => "P", "Р" => "R",
			"С" => "S", "Т" => "T", "У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "C",
			"Ч" => "CH", "Ш" => "SH", "Щ" => "SH", "Ы" => "I", "Ь" => "", "Ъ" => "",
			"Э" => "E", "Ю" => "YU", "Я" => "YA"
		);

		// Проходим по массиву и заменяем каждый символ фильтруемого значения
		foreach ($letters as $letterVal => $letterKey)
		{
			$value = str_replace($letterVal, $letterKey, $value);
		}

		return $value;
	}
	
	/**
	 * Перевод массива сообщений
	 * @param array $msgs
	 * @param string $trCat
	 * @return array
	 */
	public static function trMsgs($msgs, $trCat)
	{
		if (empty($msgs))
		{
			return $msgs;
		}
		foreach ($msgs as $key => $msg)
		{
			if (is_string($msg))
			{
				$msgs[$key] = Yii::t($trCat, $msg);
			}
		}
		return $msgs;
	}
	
	/**
	 * Получение короткого id по полному
	 * @param string $fullId
	 * @return string 
	 */
	public static function shortIdByFull($fullId)
	{
		return mb_substr($fullId, 0, 13);
	}

}
