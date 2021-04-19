<?php
declare(strict_types=1);
use PHPUnit\Framework\TestCase;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JulianDateTest
 *
 * @author hm
 */

final class JulianDateTest  extends TestCase {
	public function testfromInt() {
		$date = JulianDate::fromInt(2451545);
		$this->assertEquals("2000-01-01", $date->getFormat("Y-m-d"));
	}

	public function testToInt() {
		$date = new JulianDate(2000, 1, 1);
		$this->assertEquals(2451545, $date->toInt());
	}
	
	public function testFromStringSane() {
		$this->assertInstanceOf(JulianDate::class,	JulianDate::fromString("2020-06-12"));
	}

	public function testFromStringAntiquity() {
		$this->assertInstanceOf(JulianDate::class, JulianDate::fromString("202-03-06"));
	}

	public function testFromStringDune() {
		$this->assertInstanceOf(JulianDate::class, JulianDate::fromString("10191-03-06"));
	}

	public function testFromStringBogus() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("Bogus");
	}

	public function testFromStringMissingYear() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("-06-12");
	}

	public function testFromStringMissingMonth() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("2020--12");
	}

	public function testFromStringMissingDay() {
		$this->expectException(InvalidArgumentException::class);
		JulianDate::fromString("2020-06-");
	}

	public function testFromStringRange() {
		$this->expectException(RangeException::class);
		JulianDate::fromString("2020-06-31");
	}

	public function testConstruct() {
		$this->assertInstanceOf(JulianDate::class, new JulianDate(2020, 6, 12));
	}

	public function testConstructRange() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 6, 31);
	}

	public function testConstructMonthZero() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 0, 31);
	}

	public function testConstructMonthOOR() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 13, 31);
	}

	public function testConstructDayZero() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 6, 0);
	}

	public function testConstructDayOOR() {
		$this->expectException(RangeException::class);
		new JulianDate(2020, 6, 31);
	}

	public function testConstructLeap() {
		$date = JulianDate::fromString("2020-02-29");
		$this->assertEquals("2020-02-29", $date->__toString());
	}

	public function testConstructLeapWrong() {
		$this->expectException(RangeException::class);
		new JulianDate(2019, 2, 29);
	}
	
	public function testFormat() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("5", $date->getFormat("N"));
	}

	public function testToIsodate() {
		$date = new JulianDate(2020, 4, 18);
		$this->assertEquals("2020-04-18", $date->getIsodate());
	}
	
	public function testGetFirstDay() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("2020-06-12", $date->getFirstOf(JulianDate::DAY));
	}

	public function testGetFirstWeek() {
		for($i=8;$i<=14;$i++) {
			$date = new JulianDate(2020, 6, $i);
			$this->assertEquals("2020-06-08", $date->getFirstOf(JulianDate::WEEK));
		}
	}

	public function testGetFirstMonth() {
		for($i=1;$i<=12;$i++) {
			$date = new JulianDate(2020, $i, 8);
			$this->assertEquals("2020-".str_pad((string)$i, 2, "0", STR_PAD_LEFT)."-01", $date->getFirstOf(JulianDate::MONTH));
		}
	}

	public function testGetFirstYear() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("2020-01-01", $date->getFirstOf(JulianDate::YEAR));
	}

	public function testGetLastDay() {
		$date = JulianDate::fromString("2020-06-12");
		$this->assertEquals("2020-06-12", $date->getLastOf(JulianDate::DAY));
	}

	public function testGetLastWeek() {
		for($i=8;$i<=14;$i++) {
			$date = new JulianDate(2020, 6, $i);
			$this->assertEquals("2020-06-14", $date->getLastOf(JulianDate::WEEK));
		}
	}
	
	public function testGetLastMonthStandard() {
		$array = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		foreach($array as $key => $value) {
			$date = new JulianDate(2019, $key+1, 12);
			$this->assertEquals("2019-".str_pad((string)($key+1), 2, "0", STR_PAD_LEFT)."-".$value, $date->getLastOf(JulianDate::MONTH));
		}
	}

	public function testGetLastMonthLeap() {
		$array = array(31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		foreach($array as $key => $value) {
			$date = new JulianDate(2020, $key+1, 12);
			$this->assertEquals("2020-".str_pad((string)($key+1), 2, "0", STR_PAD_LEFT)."-".$value, $date->getLastOf(JulianDate::MONTH));
		}
	}
	
	public function testAddDays() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-06-17", $date->addUnit(5, JulianDate::DAY));
	}
	
	public function testAddWeeks() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-06-26", $date->addUnit(2, JulianDate::WEEK));
	}

	public function testAddMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-09-12", $date->addUnit(3, JulianDate::MONTH));
	}
	
	public function testAddManyMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2023-08-12", $date->addUnit(38, JulianDate::MONTH));
	}
	
	public function testAddMonthsRange() {
		$date = new JulianDate(2020, 5, 31);
		$this->expectException(RangeException::class);
		$date->addUnit(1, JulianDate::MONTH);
	}

	
	public function testAddYears() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2027-06-12", $date->addUnit(7, JulianDate::YEAR));
	}
	
	public function testAddYearsRange() {
		$date = new JulianDate(2020, 2, 29);
		$this->expectException(RangeException::class);
		$date->addUnit(1, JulianDate::YEAR);
	}
	
	public function testSubDays() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-06-07", $date->addUnit(-5, JulianDate::DAY));
	}
	
	public function testSubWeeks() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-05-29", $date->addUnit(-2, JulianDate::WEEK));
	}
	
	public function testSubMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2020-03-12", $date->addUnit(-3, JulianDate::MONTH));
	}

	public function testSubManyMonths() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2017-04-12", $date->addUnit(-38, JulianDate::MONTH));
	}

	public function testSubYears() {
		$date = new JulianDate(2020, 6, 12);
		$this->assertEquals("2017-06-12", $date->addUnit(-3, JulianDate::YEAR));
	}
}