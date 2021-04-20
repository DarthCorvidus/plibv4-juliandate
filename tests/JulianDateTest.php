<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
/**
 * @copyright (c) 2021, Claus-Christoph Küthe
 * @author Claus-Christoph Küthe <plibv4@vm01.telton.de>
 * @license LGPLv2.1
 */

/**
 * Description of JulianDateTest
 *
 * Unit Test for JulianDate
 */

final class JulianDateTest  extends TestCase {
	
	/**
	 * Test from Int
	 * 
	 * Test to create JulianDate from an integer, whereas the integer is a date
	 * in Julian days.
	 */
	public function testfromInt() {
		$date = JulianDate::fromInt(2451545);
		$this->assertEquals("2000-01-01", $date->getFormat("Y-m-d"));
	}

	/**
	 * Test to Int
	 * 
	 * Return internal integer value of JulianDate, which is of course the
	 * date as Julian days.
	 */
	public function testToInt() {
		$date = new JulianDate(2000, 1, 1);
		$this->assertEquals(2451545, $date->toInt());
	}
	
	/**
	 * Test from String Sane
	 * 
	 * Create Instance of JulianDate from a correct ISO-8601 style date, without
	 * time.
	 */
	public function testFromStringSane() {
		$this->assertInstanceOf(JulianDate::class,	JulianDate::fromString("2020-06-12"));
	}

	/**
	 * Test from string antiquity
	 * 
	 * Test to create an instance from a date far in the past.
	 */
	public function testFromStringAntiquity() {
		$this->assertInstanceOf(JulianDate::class, JulianDate::fromString("202-03-06"));
	}

	/**
	 * Test from string Dune
	 * 
	 * Test to create an instance from a date far in the future, when the
	 * known universe is ruled by the Padishah Emperor Shaddam IV.
	 */
	public function testFromStringDune() {
		$this->assertInstanceOf(JulianDate::class, JulianDate::fromString("10191-03-06"));
	}

	/**
	 * Test from string bogus
	 * 
	 * Tries to create an instance of JulianDate from clear bogus.
	 */
	public function testFromStringBogus() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("Bogus");
	}

	/**
	 * Test from string missing year
	 * 
	 * Tries to create an instance of JulianDate from an Iso date without a
	 * year.
	 */
	public function testFromStringMissingYear() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("-06-12");
	}

	/**
	 * Test from string missing month
	 * 
	 * Tries to create an instance of JulianDate from an Iso date without a
	 * month
	 */
	public function testFromStringMissingMonth() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("2020--12");
	}

	/**
	 * Test from string missing day
	 * 
	 * Tries to create an instance of JulianDate from an Iso date without a
	 * day.
	 */
	public function testFromStringMissingDay() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("2020-06-");
	}

	/**
	 * Test from string invalid range
	 * 
	 * Try to instantiate an instance of JulianDate from an invalid date, ie
	 * from more days than a month has.
	 */
	public function testFromStringInvalidRange() {
		$this->expectException(RangeException::class);
		JulianDate::fromString("2020-06-31");
	}

	/**
	 * Test construct empty
	 * 
	 * Constructs a new instance which points to "now".
	 */
	public function testConstructEmpty() {
		$now = date("Y-m-d");
		$julian = new JulianDate();
		$this->assertEquals($now, $julian->getIsodate());
	}
	
	/**
	 * Test construct filled
	 * 
	 * COnstruct an instance of JulianDate to a specific Gregorian date.
	 */
	public function testConstructFilled() {
		$julian = new JulianDate(2020, 6, 12);
		$this->assertInstanceOf(JulianDate::class, $julian);
		$this->assertEquals("2020-06-12", $julian->getIsodate());
	}

	/**
	 * Test construct month zero
	 * 
	 * Throws RangeException if month is zero.
	 */
	public function testConstructMonthZero() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 0, 31);
	}

	/**
	 * Test construct month out of range
	 * 
	 * Throws RangeException if month is greater than 12.
	 */
	public function testConstructMonthOutOfRange() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 13, 31);
	}

	/**
	 * Test construct day zero
	 * 
	 * Throws RangeException if day is zero.
	 */
	public function testConstructDayZero() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 6, 0);
	}

	/**
	 * Test construct day out of range
	 * 
	 * Throws RangeException if day is larger than the amount of days the month
	 * has. 
	 */
	public function testConstructDayOOR() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 6, 31);
	}

	/**
	 * Test construct leap year
	 * 
	 * Test if 29 days for February are allowed in a leap year.
	 */
	public function testConstructLeapYear() {
		$date = new JulianDate(2020, 2, 29);
		$this->assertEquals("2020-02-29", $date->__toString());
	}

	/**
	 * Test construct leap wrong
	 * 
	 * Test if 29 days for February in a non leap year throws a RangeEception.
	 */
	public function testConstructLeapWrong() {
		$this->expectException(RangeException::class);
		new JulianDate(2019, 2, 29);
	}
	
	/**
	 * Test format
	 * 
	 * Test if getFormat() works the same way as date.
	 */
	public function testFormat() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("5", $date->getFormat("N"));
	}

	/**
	 * Test get isodate
	 * 
	 * Test if getIsodate returns an isodate. Basically the same as __toString,
	 * for readability.
	 */
	public function testToIsodate() {
		$date = new JulianDate(2020, 4, 18);
		$this->assertEquals("2020-04-18", $date->getIsodate());
	}
	
	/**
	 * Test get first day
	 * 
	 * Test to get the first day of period "DAY", which doesn't really make
	 * sense.
	 */
	public function testGetFirstDay() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("2020-06-12", $date->getFirstOf(JulianDate::DAY));
	}

	/**
	 * Test get first week day
	 * 
	 * Test to get the first day of week for a given date. 
	 */
	public function testGetFirstWeekDay() {
		for($i=8;$i<=14;$i++) {
			$date = new JulianDate(2020, 6, $i);
			$this->assertEquals("2020-06-08", $date->getFirstOf(JulianDate::WEEK)->getIsodate());
		}
	}

	/**
	 * Test get first month day
	 * 
	 * Test to get the first day of a month.
	 */
	public function testGetFirstMonthDay() {
		for($i=1;$i<=12;$i++) {
			$date = new JulianDate(2020, $i, 8);
			$this->assertEquals("2020-".str_pad((string)$i, 2, "0", STR_PAD_LEFT)."-01", $date->getFirstOf(JulianDate::MONTH)->getIsodate());
		}
	}

	/**
	 * Test get first year day
	 * 
	 * Test to get the first day of a year
	 */
	public function testGetFirstYearDay() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("2020-01-01", $date->getFirstOf(JulianDate::YEAR)->getIsodate());
	}

	/**
	 * Test get last day
	 * 
	 * Test to get the last day of a day, which makes no sense.
	 */
	public function testGetLastDay() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("2020-06-12", $date->getLastOf(JulianDate::DAY)->getIsodate());
	}

	/**
	 * Test get last week day
	 * 
	 * Test to get the last day of a week.
	 */
	public function testGetLastWeekDay() {
		for($i=8;$i<=14;$i++) {
			$date = new JulianDate(2020, 6, $i);
			$this->assertEquals("2020-06-14", $date->getLastOf(JulianDate::WEEK)->getIsodate());
		}
	}
	
	/**
	 * Test get last month day standard
	 * 
	 * Test to get the last day of a month in a standard year.
	 */
	public function testGetLastMonthDayStandard() {
		$array = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		foreach($array as $key => $value) {
			$date = new JulianDate(2019, $key+1, 12);
			$this->assertEquals("2019-".str_pad((string)($key+1), 2, "0", STR_PAD_LEFT)."-".$value, $date->getLastOf(JulianDate::MONTH));
		}
	}

	/**
	 * Test get last month day leap
	 * 
	 * Test to get the last day of all months of a leap year.
	 */
	public function testGetLastMonthDayLeap() {
		$array = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		foreach($array as $key => $value) {
			$date = new JulianDate(2020, $key+1, 12);
			$this->assertEquals("2020-".str_pad((string)($key+1), 2, "0", STR_PAD_LEFT)."-".$value, $date->getLastOf(JulianDate::MONTH)->getIsodate());
		}
	}

	/**
	 * Test add Days
	 * 
	 * Test to add five days to a Julian date.
	 */
	public function testAddDays() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-06-17", $date->addUnit(5, JulianDate::DAY));
	}
	
	/**
	 * Test add weeks
	 * 
	 * Test to add two weeks to a Julian date
	 */
	public function testAddWeeks() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-06-26", $date->addUnit(2, JulianDate::WEEK));
	}

	/**
	 * Test add months
	 * 
	 * Test to add three months to a Julian date
	 */
	public function testAddMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-09-12", $date->addUnit(3, JulianDate::MONTH));
	}
	
	/**
	 * Test add many months
	 * 
	 * Test to add a large amount of months (38) to a Julian date
	 */
	public function testAddManyMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2023-08-12", $date->addUnit(38, JulianDate::MONTH));
	}
	
	/**
	 * Test add months Range Exception
	 * 
	 * addUnit is supposed to throw an exception if the result of an addition is
	 * out of range, ie 2020-05-31 +1 Month cannot be satisfied as 2020-06-31 is
	 * out of range.
	 */
	public function testAddMonthsRangeException() {
		$date = new JulianDate(2020, 5, 31);
		$this->expectException(RangeException::class);
		$date->addUnit(1, JulianDate::MONTH);
	}

	/**
	 * Test add years
	 * 
	 * Test to add seven years to a Julian date.
	 */
	public function testAddYears() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2027-06-12", $date->addUnit(7, JulianDate::YEAR));
	}
	
	/**
	 * Test add years Range Exception
	 * 
	 * Throws an exception if addition of years results in an invalid date (only
	 * possible for leap years)
	 */
	public function testAddYearsRangeException() {
		$date = new JulianDate(2020, 2, 29);
		$this->expectException(RangeException::class);
		$date->addUnit(1, JulianDate::YEAR);
	}

	/**
	 * Test sub days
	 * 
	 * Subtracting days from a Julian date
	 */
	public function testSubDays() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-06-07", $date->addUnit(-5, JulianDate::DAY));
	}

	/**
	 * Test sub weeks
	 * 
	 * Subtracting weeks from a Julian date
	 */
	public function testSubWeeks() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-05-29", $date->addUnit(-2, JulianDate::WEEK));
	}

	/**
	 * Test sub months
	 * 
	 * Subtracting months from a Julian date
	 */
	public function testSubMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-03-12", $date->addUnit(-3, JulianDate::MONTH));
	}

	/**
	 * Test sub many months
	 * 
	 * Subtracting a larger amount of months from a Julian date
	 */
	public function testSubManyMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2017-04-12", $date->addUnit(-38, JulianDate::MONTH));
	}

	/**
	 * Test sub years
	 * 
	 * Subtract years from Julian date
	 */
	public function testSubYears() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2017-06-12", $date->addUnit(-3, JulianDate::YEAR));
	}

	/*
	 * Test sub month Range Exception
	 * 
	 * Expect RangeException if day is out of range after subtracting one month.
	 */
	public function testSubMonthRangeException() {
		$date = new JulianDate(2020, 5, 31);
		$this->expectException(RangeException::class);
		$date->addUnit(-1, JulianDate::MONTH);
	}
	
	/**
	 * Test sub year Range Exception
	 * 
	 * Expect RangeException if day is out of range after subtracting one year.
	 */
	public function testSubYearsRangeException() {
		$date = new JulianDate(2020, 2, 29);
		$this->expectException(RangeException::class);
		$date->addUnit(-1, JulianDate::YEAR);
	}

}