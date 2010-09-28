<?php

class Ifrond_Model_Calendar {

	public $haveLinks = 0;
	protected $_showCalendarTitle = true;
	protected $_showWeekDaysTitles = true;
	protected $_showTbodyOnly = true;
	protected $_tableMonthClass = 'month';
	protected $_tableYearClass = 'year';
	protected $_dateLinks = array();
	
	public function __construct() {	}
	
	public function setShowCalendarTitle($flag) {
		$this->_showCalendarTitle = $flag;
	}
	
	public function getShowCalendarTitle() {
		return $this->_showCalendarTitle;
	}
	
	public function setShowWeekDaysTitles($flag) {
		$this->_showWeekDaysTitles = $flag;
	}
	
	public function getShowWeekDaysTitles() {
		return $this->_showWeekDaysTitles;
	}
	
	public function setShowTbodyOnly($flag) {
		$this->_showTbodyOnly = $flag;
	}
	
	public function getShowTbodyOnly() {
		return $this->_showTbodyOnly;
	}
	
	public function setTableMonthClass($class) {
		$this->_tableMonthClass = $class;
	}
	
	public function getTableMonthClass() {
		return $this->_tableMonthClass;
	}
	
	public function setTableYearClass($class) {
		$this->_tableYearClass = $class;
	}
	
	public function getTableYearClass() {
		return $this->_tableYearClass;
	}
	
	public function setLinkForDate($link, $date) {
		$date = toFormatDate($date, 'Y-m-d');		
		$this->_dateLinks[$date] = $link;
	}
	
	public function getLinkForDate($date) {	
		$date = toFormatDate($date, 'Y-m-d');
		if (!isset($this->_dateLinks[$date])) return false;		
		if ($this->_dateLinks[$date] == '') return false;
		return $this->_dateLinks[$date];
	}
	
	public function getAllLinks() {
		return $this->_dateLinks;
	}
	
	public function getMonthTitle($num_month) {
		$num_month=intval($num_month);
		$calMes["1"] = "Январь";
		$calMes["2"] = "Февраль";
		$calMes["3"] = "Март";
		$calMes["4"] = "Апрель";
		$calMes["5"] = "Май";
		$calMes["6"] = "Июнь";
		$calMes["7"] = "Июль";
		$calMes["8"] = "Август";
		$calMes["9"] = "Сентябрь";
		$calMes["10"] = "Октябрь";
		$calMes["11"] = "Ноябрь";
		$calMes["12"] = "Декабрь";
		return $calMes[$num_month];
	}

	public function getYearCalendar($year) {
		$htmlStr='<table class="'.$this->getTableYearClass().'"><tbody><tr>';
		for($i = 1; $i<=12; $i++) {
			if ($i==4 or $i==7 or $i==10) $htmlStr.='</tr><tr>';
			$htmlStr.='<td class="'.$this->getTableMonthClass().'Wrap">'.$this->getMonthCalendar($i, $year).'</td>';
		}
		$htmlStr.='</tr></tbody></table>';
		return $htmlStr;
	}

	public function getWeekDaysTitles($year) {
		$htmlStr = '<thead><tr>';
		$htmlStr .= '<th>Пн</th><th>Вт</th><th>Ср</th><th>Чт</th><th>Пт</th><th>Сб</th><th>Вс</th>';
		$htmlStr .= '</tr></thead>';
		return $htmlStr;
	}	
	public function getCalendarTitle($month) {
		$htmlStr .= '<tr class="asCaption">			
				<td colspan="6">'.$this->getMonthTitle($month).'</td>
				</tr>';	
		return $htmlStr;
	}
	public function getNextMonth($month, $year) {				
		$nextMonth = $month + 1;
		if ($nextMonth > 12) {
			$nextMonth = 1;
			$nextYear = $year + 1;
		} else {
			$nextYear = $year;
		}
		$result = array();
		$result['month'] = $nextMonth;
		$result['year'] = $nextYear;
		return $result;
	}
	public function getPrevMonth($month, $year) {
		$prevMonth = $month - 1;
		if ($prevMonth < 1) {
			$prevMonth = 12;
			$prevYear = $year - 1;
		} else {
			$prevYear = $year;
		}
		$result = array();
		$result['month'] = $prevMonth;
		$result['year'] = $prevYear;
		return $result;
	}	
	
	public function getMonthCalendar($month, $year = 0) {		
		if ($year == 0) $year = date("Y");
		$today_ts = mktime(0,0,0,date("n"),date("d"),date("Y"));		 
		$firstday_month_ts = mktime(0,0,0,$month,1,$year); 
		if ($firstday_month_ts == false) return false;
		$lastday_month_ts = mktime(0,0,0,$month+1,0,$year);  
		if ($lastday_month_ts == false) return false;
		
		$numYear = $year;
		$numMonth = $month;
		$daysInMonth = date("t",$firstday_month_ts);

		$dayMonth_start = date("w",$firstday_month_ts);
		if ($dayMonth_start==0) { $dayMonth_start=7;}

		$dayMonth_end = date("w",$lastday_month_ts);
		if ($dayMonth_end == 0) { 
			$dayMonth_end = 7; 
		}
		
		$htmlStr = '';

		if ($this->getShowTbodyOnly() == false) {
			$htmlStr = '<table class="'.$this->getTableMonthClass().'">';
			if ($this->getShowCalendarTitle()) {
				$htmlStr .= $this->getCalendarTitle($numMonth);	
			}
			if ($this->getShowWeekDaysTitles()) {
				$htmlStr .= $this->getWeekDaysTitles();
			}
			$htmlStr .= '<tbody>';
		}		
		$htmlStr .= '<tr>';		
		for ($k = 1; $k < $dayMonth_start; $k++) {
			$htmlStr .= '<td>&nbsp;</td>';
		}		
		for ($i=1; $i<=$daysInMonth; $i++) {
			
			$day_i_ts=mktime(0,0,0,date("n",$firstday_month_ts),$i,date("Y",$firstday_month_ts));
			$day_i = date("w",$day_i_ts);
			
			if ($day_i==0) { $day_i=7;}
			
			$d2_i = date("d",$day_i_ts);
			if (intval($numMonth)<10) $month='0'.intval($numMonth);
			$fullDate = $numYear.'-'.$month.'-'.$d2_i;

			if ($i<10) $link_i='0'.$i; else  $link_i=$i;
			$link = $this->getLinkForDate($fullDate);
			if ($link) {
				$link_i = '<a href="'.$link.'">'.$link_i.'</a>';
				$isLink=true;
			} else {
				$isLink=false;
			}
			if ($today_ts==$day_i_ts) {
				$htmlStr .= '<td class="today">'.$link_i.'</td>';
			}
			else {
				$htmlStr .= '<td>'.$link_i.'</td>';
			}
			if ($day_i==7 && $i<$daysInMonth) {
				$htmlStr .= '</tr><tr>';
			}
			else if ($day_i==7 && $i==$daysInMonth) {
				$htmlStr .= '</tr>';
			}
			else if ($i==$daysInMonth) {
				for ($h=$dayMonth_end; $h<7; $h++) {
					$htmlStr .= '<td>&nbsp;</td>';
				}
				$htmlStr .= '</tr>';
			}
		} 
		if ($this->getShowTbodyOnly() == false) {
			$htmlStr .= '</tbody>';
			$htmlStr .= '</table>';	
		}
		
		return $htmlStr;

	} 

} 
