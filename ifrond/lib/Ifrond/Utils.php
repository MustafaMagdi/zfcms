<?php

function clearStr($str) {
	$str = trim ( strip_tags ( $str ) );
	$str = stripslashes ( $str );
	$str = str_replace ( "'", '', $str );
	$str = str_replace ( '"', '', $str );
	$str = addslashes ( $str );
	return $str;
}

function sort2d ($array, $index, $order='asc', $natsort=FALSE, $case_sensitive=FALSE)
{
	if(is_array($array) && count($array)>0)
	{
		foreach(array_keys($array) as $key)
		$temp[$key]=$array[$key][$index];
		if(!$natsort)
		($order=='asc')? asort($temp) : arsort($temp);
		else
		{
			($case_sensitive)? natsort($temp) : natcasesort($temp);
			if($order!='asc')
			$temp=array_reverse($temp,TRUE);
		}
		foreach(array_keys($temp) as $key)
		(is_numeric($key))? $sorted[]=$array[$key] : $sorted[$key]=$array[$key];
		return $sorted;
	}
	return $array;
}

function toFormatDate($date, $mask = false)
{
	$timestamp = strtotime($date);
	if ($mask !== false) {
		$date = date($mask, $timestamp);
	} else {
		$date = $timestamp;
	}
	return $date;
}

function toMonthRu($month, $declination = true)
{
	$ruMonth = array('', 'январь', 'февраль', 'март', 'апрель', 'май', 'июнь',
						'июль', 'август', 'сентябрь', 'октябрь', 'ноябрь', 'декабрь');
	$ruMonthDecl = array('', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня',
						'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');	
	$month = intval($month);
	if ($declination) {
		$month = $ruMonthDecl[$month];
	} else {
		$month = $ruMonth[$month];
	}
	return $month;
}

function analyseUrl($url)
{
	$urlParts = explode('?', $url);
	$parts = explode('/', $urlParts[0]);
	$result = array();
	foreach ($parts as $t) {
		$t = trim($t);
		if ($t != '') $result[] = $t;
	}
	return $result;
}

function _p($var, $die = true) {
	Zend_Debug::dump($var);
	if ($die) die();
}

function _l($txt) {
	$file = PATH_TEMP.'/log.txt';
	file_put_contents($file, "\r\n".$txt."\n--\n\n", FILE_APPEND);
}