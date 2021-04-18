<?php
/**
 * Julian Date class
 * 
 * JulianDate is pure date only, using julian days. JulianDate is immutable, ie.
 * no function changes the internal state of an JulianDate instance, but returns
 * a new instance of JulianDate instead.
 *
 * @author Claus-Christoph Küthe
 * @copyright (c) 2020, Claus-Christoph Küthe
 */
class JulianDate {
	const DAY = 1;
	const WEEK = 2;
	const MONTH = 3;
	const YEAR = 4;
	/** @var int Date as Julian days */
	private $numeric;
	/**
	 * 
	 * @param int $year Year
	 * @param int $month Month
	 * @param int $day Day
	 */
	function __construct(int $year=NULL, int $month=NULL, int $day=NULL) {
		if($year==NULL)	{
			$now = time();
			$day = date("d", $now);
			$month = date("m", $now);
			$year = date("Y", $now);
		}
		$this->testRange($year, $month, $day);
		$this->numeric = gregoriantojd($month, $day, $year);
	}
	
	/**
	 * Tests if months and days are valid; doesn't allow for dates like
	 * 2020-06-31 or 2021-02-29.
	 * @param int $year Year
	 * @param int $month Month
	 * @param int $day Day
	 * @throws RangeException
	 */
	private function testRange(int $year, int $month, int $day) {
		if($month<1 || $month>12) {
			throw new RangeException("month is out of range");
		}
		if($day<1) {
			throw new RangeException("day is out of range");
		}
		if($day<=28) {
			return;
		}
		$date = new JulianDate($year, $month, 1);
		if($date->getFormat("t")<$day) {
			throw new RangeException("day is out of range");
		}
	}
	
	/**
	 * Creates a date from an ISO 6801 compliant string (YYYY-MM-DD)
	 * @param String $string Date as ISO 6801
	 * @return JulianDate
	 * @throws InvalidArgumentException
	 */
	static function fromString(String $string): JulianDate {
		$tmp = explode("-", $string);
		if(!preg_match("/[0-9]*-[0-9]{2}-[0-9]{2}/", $string)) {
			throw new InvalidArgumentException("invalid isodate, must be YYYY-MM-DD");	
		}
		if(!preg_match("/[0-9]+-[0-9]{2}-[0-9]{2}/", $string)) {
			throw new InvalidArgumentException("invalid isodate, must be YYYY-MM-DD");	
		}
	return new JulianDate($tmp[0], $tmp[1], $tmp[2]);
	}
	
	/**
	 * Constructs instance of JulianDate directly from julian days.
	 * 
	 * @param int $julian
	 * @return JulianDate
	 */
	static function fromNumeric(int $julian): JulianDate {
		$date = new JulianDate();
		$date->numeric = $julian;
	return $date;
	}
	
	/**
	 * To Numeric
	 * 
	 * Return julian days as integer.
	 * @return int
	 */
	function toNumeric(): int {
		return $this->numeric;
	}
	
	/**
	 * 
	 * @param int $unit
	 * @throws InvalidArgumentException
	 */
	private function allowedUnit(int $unit) {
		if(!in_array($unit, array(self::DAY, self::WEEK, self::MONTH, self::YEAR))) {
			throw new InvalidArgumentException("\$unit does not contain an allowed unit");
		}
	}

	/**
	 * Get first day of a given unit.
	 * @param int $unit
	 * @return \JulianDate
	 */
	function getFirstOf(int $unit): JulianDate {
		if($unit==self::DAY) {
			return $this;
		}
		if($unit==self::WEEK) {
			$days = $this->numeric-($this->getFormat("N")-1);
		return JulianDate::fromNumeric($days);
		}

		if($unit==self::MONTH) {
			$array = cal_from_jd($this->numeric, CAL_GREGORIAN);
		return new JulianDate($array["year"], $array["month"], 1);
		}

		if($unit==self::YEAR) {
			$array = cal_from_jd($this->numeric, CAL_GREGORIAN);
		return new JulianDate($array["year"], 1, 1);
		}
	}
	
	/**
	 * Get last day of a given unit
	 * @param int $unit
	 * @return \JulianDate
	 */
	function getLastOf(int $unit): JulianDate {
		if($unit==self::DAY) {
			return $this;
		}
		if($unit==self::WEEK) {
			$days = $this->numeric+(7-$this->getFormat("N"));
		return JulianDate::fromNumeric($days);
		}

		if($unit==self::MONTH) {
			$array = cal_from_jd($this->numeric, CAL_GREGORIAN);
			return new JulianDate($array["year"], $array["month"], (int)$this->getFormat("t"));
		}

		if($unit==self::YEAR) {
			$array = cal_from_jd($this->numeric, CAL_GREGORIAN);
			return new JulianDate($array["year"], 1, 1);
		}
	}
	
	/**
	 * Get date in custom format.
	 * @param string $format
	 * @return string
	 */
	function getFormat(string $format): string {
		$time = strtotime($this->__toString());
	return date($format, $time);
	}

	/**
	 * Adds a specific Unit
	 * @param int $amount
	 * @param int $unit
	 * @return \JulianDate
	 */
	function addUnit(int $amount, int $unit): JulianDate {
		if($amount==0) {
			return $this;
		}
		$this->allowedUnit($unit);
		if($unit==self::WEEK) {
			return JulianDate::fromNumeric($this->numeric+($amount*7));
		}
		if($unit==self::MONTH) {
			return $this->addMonths($amount);
		}
		if($unit==self::YEAR) {
			return new JulianDate($this->getFormat("Y")+$amount, $this->getFormat("n"), $this->getFormat("j"));
		}
	return JulianDate::fromNumeric($this->numeric+$amount);
	}
	/**
	 * 
	 * @param int $amount Amount of Months to add.
	 * @return \JulianDate
	 */
	private function addMonths(int $amount): JulianDate {
		if($amount>0) {
			$years = floor($amount/12)+$this->getFormat("Y");
		} else {
			$years = ceil($amount/12)+$this->getFormat("Y");
		}
		$months = ($amount%12)+$this->getFormat("n");
	return new JulianDate($years, $months, $this->getFormat("j"));
	}
	
	/**
	 * Returns date as string (YYYY-MM-DD).
	 * @return string
	 */
	function __toString(): string {
		$array = cal_from_jd($this->numeric, CAL_GREGORIAN);
	return sprintf("%d-%02d-%02d", $array["year"], $array["month"], $array["day"]);
	}
	
	/**
	 * Get Isodate
	 * 
	 * Returns date as isodate. Basically the same as __toString()
	 * @return string 
	 */
	function getIsodate(): string {
		return $this->__toString();
	}
}