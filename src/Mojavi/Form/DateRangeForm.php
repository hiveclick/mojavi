<?php
namespace Mojavi\Form;

use Mojavi\Error\Error as Error;

/**
 * DateRangeForm takes care of formatting a date range (start and end dates).  It is
 * useful for reports.
 **/

class DateRangeForm extends PageListForm {

	const DATE_RANGE_LAST_30_DAYS = 1;
	const DATE_RANGE_LAST_7_DAYS = 2;
	const DATE_RANGE_THIS_WEEK = 3;
	const DATE_RANGE_YESTERDAY = 4;
	const DATE_RANGE_TODAY = 5;
	const DATE_RANGE_LAST_48_HOURS = 6;
	const DATE_RANGE_LAST_24_HOURS = 7;
	const DATE_RANGE_LAST_12_HOURS = 8;
	const DATE_RANGE_LAST_4_HOURS = 9;
	const DATE_RANGE_LAST_1_HOURS = 10;
	const DATE_RANGE_LAST_10_MIN = 11;
	const DATE_RANGE_MONTH = 12;
	const DATE_RANGE_MTD = 13;
	const DATE_RANGE_LAST_MONTH = 14;
	const DATE_RANGE_LAST_2_DAYS = 15;
	const DATE_RANGE_CUSTOM = 16;
	
	const DATE_FORMAT_MDY = "m/d/Y";
	const DATE_FORMAT_FULL = "m/d/Y g:i:s a";
	const DATE_FORMAT_TIME = "g:i:s a";
	const DATE_FORMAT_MYSQL = "Y-m-d";

	private $dateRange;
	private $startDate;
	private $startTime;
	private $endDate;
	private $endTime;
	private $dateFormat;
	private $noEnd;
	private $tz_modifier;

	/**
	 * Returns an array of date formats
	 * @return multitype:string
	 */
	static public function retrieveDateRanges()
	{
		return array(
			self::DATE_RANGE_LAST_30_DAYS => 'Last 30 Days',
			self::DATE_RANGE_LAST_7_DAYS => 'Last 7 Days',
			self::DATE_RANGE_LAST_2_DAYS => 'Last 2 Days',
			self::DATE_RANGE_THIS_WEEK => 'This Week',
			self::DATE_RANGE_YESTERDAY => 'Yesterday',
			self::DATE_RANGE_TODAY => 'Today',
			self::DATE_RANGE_MTD => 'Month To Date',
			self::DATE_RANGE_MONTH => 'Month',
			self::DATE_RANGE_LAST_MONTH => 'Last Month',
			self::DATE_RANGE_LAST_48_HOURS => 'Last 48 Hours',
			self::DATE_RANGE_LAST_24_HOURS => 'Last 24 Hours',
			self::DATE_RANGE_LAST_12_HOURS => 'Last 12 Hours',
			self::DATE_RANGE_LAST_4_HOURS => 'Last 4 Hours',
			self::DATE_RANGE_LAST_1_HOURS => 'Last Hour',
			self::DATE_RANGE_LAST_10_MIN => 'Last 10 Minutes',
			self::DATE_RANGE_CUSTOM => 'Custom'
		);
	}
	
	/**
	 * Returns the dateRange
	 * @return integer
	 */
	function getDateRange() {
		if (is_null($this->dateRange)) {
			$this->dateRange = self::DATE_RANGE_CUSTOM;
		}
		return $this->dateRange;
	}
	
	/**
	 * Sets the dateRange
	 * @var integer
	 */
	function setDateRange($arg0) {
		$this->dateRange = $arg0;
		return $this;
	}
	
	/**
	 * Returns the tz_modifier
	 * @return string
	 */
	function getTzModifier() {
		if (is_null($this->tz_modifier)) {
			$this->tz_modifier = date_default_timezone_get();
		}
		return $this->tz_modifier;
	}
	
	/**
	 * Sets the tz_modifier
	 * @var string
	 */
	function setTzModifier($arg0) {
		$this->tz_modifier = $arg0;
		return $this;
	}
	
	/**
	 * Returns the timezone object
	 * @return \DateTimeZone
	 */
	function getTimezone() {
		return new \DateTimeZone($this->getTzModifier());
	}
	
	/**
	 * returns the noEnd
	 * @return boolean
	 */
	function isNoEnd() {
		if (is_null($this->noEnd)) {
			$this->noEnd = false;
		}
		return $this->noEnd;
	}

	/**
	 * sets the noEnd
	 * @param boolean $arg0
	 */
	function setNoEnd($arg0) {
		$this->noEnd = $arg0;
		return $this;
	}

	/**
	* Returns the formatted start date based on the values in getStartDate() and getDateFormat().
	* @return string
	*/
	function getFormattedStartDate() {
		return date($this->getDateFormat(), strtotime($this->getStartDate()));
	}

	/**
	* Returns the formatted start date based on the values in getStartDate() and getDateFormat().
	* @return string
	*/
	function getFormattedStartDateOnly() {
		return date(self::DATE_FORMAT_MDY, strtotime($this->getStartDate()));
	}

	/**
	* Returns the formatted start date based on the values in getStartDate() and getDateFormat().
	* @return string
	*/
	function getFormattedEndDateOnly() {
		return date(self::DATE_FORMAT_MDY, strtotime($this->getEndDate()));
	}

	/**
	* Returns the formatted start date based on the values in getStartDate() and getDateFormat().
	* @return string
	*/
	function getFormattedStartTime() {
		return date(self::DATE_FORMAT_TIME, strtotime($this->getStartDate()));
	}

	/**
	* Returns the formatted end date based on the values in getStartDate() and getDateFormat().
	* @return string
	*/
	function getFormattedEndTime() {
		return date(self::DATE_FORMAT_TIME, strtotime($this->getEndDate()));
	}

	/**
	* Returns the start date
	* @return string
	*/
	function getStartDate() {
		switch ($this->getDateRange()) {
			case self::DATE_RANGE_LAST_30_DAYS:
				$this->startDate = new \DateTime('30 days ago', $this->getTimezone());
				$this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_LAST_7_DAYS:
				$this->startDate = new \DateTime('7 days ago', $this->getTimezone());
				$this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_LAST_2_DAYS:
				$this->startDate = new \DateTime('yesterday', $this->getTimezone());
				$this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_THIS_WEEK:
				$this->startDate = new \DateTime('last monday midnight', $this->getTimezone());
				$this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_YESTERDAY:
				$this->startDate = new \DateTime('yesterday', $this->getTimezone());
				$this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_TODAY:
				$this->startDate = new \DateTime('today', $this->getTimezone());
				$this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_MONTH:
				$this->startDate = new \DateTime('today', $this->getTimezone());
				$this->startDate->setTimestamp(strtotime($this->startDate->format('m/1/Y')));
				$this->startDate = $this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_MTD:
				$this->startDate = new \DateTime('today', $this->getTimezone());
				$this->startDate->setTimestamp(strtotime($this->startDate->format('m/1/Y')));
				$this->startDate = $this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_LAST_MONTH:
				$this->startDate = new \DateTime('today - 1 month', $this->getTimezone());
				$this->startDate->setTimestamp(strtotime($this->startDate->format('m/1/Y')));
				$this->startDate = $this->startDate->setTime(0,0,0);
				break;
			case self::DATE_RANGE_LAST_48_HOURS:
				$this->startDate = new \DateTime('now - 48 hours', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_24_HOURS:
				$this->startDate = new \DateTime('now - 24 hours', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_12_HOURS:
				$this->startDate = new \DateTime('now - 12 hours', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_4_HOURS:
				$this->startDate = new \DateTime('now - 4 hours', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_1_HOURS:
				$this->startDate = new \DateTime('now - 1 hours', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_10_MIN:
				$this->startDate = new \DateTime('now - 10 minutes', $this->getTimezone());
				break;
			case self::DATE_RANGE_CUSTOM:
				$this->startDate = new \DateTime($this->startDate, $this->getTimezone());
				break;
		}
		
		return $this->startDate->getTimestamp();
	}
	
	/**
	 * Returns the start date
	 * @return string
	 */
	function getEndDate() {
		switch ($this->getDateRange()) {
			case self::DATE_RANGE_LAST_30_DAYS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				$this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_LAST_7_DAYS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				$this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_LAST_2_DAYS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				$this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_THIS_WEEK:
				$this->endDate = new \DateTime('next sunday midnight', $this->getTimezone());
				$this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_YESTERDAY:
				$this->endDate = new \DateTime('yesterday', $this->getTimezone());
				$this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_TODAY:
				$this->endDate = new \DateTime('today', $this->getTimezone());
				$this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_MONTH:
				$this->endDate = new \DateTime('today', $this->getTimezone());
				$this->endDate->setTimestamp(strtotime($this->endDate->format('m/t/Y')));
				break;
			case self::DATE_RANGE_MTD:
				$this->endDate = new \DateTime('today', $this->getTimezone());
				$this->endDate = $this->endDate->setTime(23,59,59);
				break;
			case self::DATE_RANGE_LAST_MONTH:
				$this->endDate = new \DateTime('today - 1 month', $this->getTimezone());
				$this->endDate->setTimestamp(strtotime($this->endDate->format('m/t/Y')));
				break;
			case self::DATE_RANGE_LAST_48_HOURS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_24_HOURS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_12_HOURS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_4_HOURS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_1_HOURS:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				break;
			case self::DATE_RANGE_LAST_10_MIN:
				$this->endDate = new \DateTime('now', $this->getTimezone());
				break;
			case self::DATE_RANGE_CUSTOM:
				$this->endDate = new \DateTime($this->endDate, $this->getTimezone());
				break;
		}
	
		return $this->endDate->getTimestamp();
	}

	/**
	* Sets the start date
	* @param string $arg0
	*/
	function setStartDate($arg0) {
		$this->startDate = $arg0;
		return $this;
	}

	/**
	* Returns the start time
	* @return string
	*/
	function getStartTime() {
		if (is_null($this->startTime)) {
			$this->startTime = "now";
		}
		if (strlen($this->startTime) == 0) {
			$this->startTime = "now";
		}
		return $this->startTime;
	}

	/**
	* Sets the start time
	* @param string $arg0
	*/
	function setStartTime($arg0) {
		$this->startTime = $arg0;
		$this->setStartDate(date(self::DATE_FORMAT_MDY, strtotime($this->getStartDate())) . " " . date(self::DATE_FORMAT_TIME, strtotime($this->getStartTime())));
		return $this;
	}

	/**
	* Returns the formatted end date based on the values in getEndDate() and getDateFormat().
	* @return string
	*/
	function getFormattedEndDate() {
		return date($this->getDateFormat(), strtotime($this->getEndDate()));
	}

	/**
	* Sets the end date
	* @param string $arg0
	*/
	function setEndDate($arg0) {
		$this->endDate = $arg0;
		return $this;
	}

	/**
	* Returns the end time
	* @return string
	*/
	function getEndTime() {
		if (is_null($this->endTime)) {
			$this->endTime = "now";
		}
		if (strlen($this->endTime) == 0) {
			$this->endTime = "now";
		}
		return $this->endTime;
	}

	/**
	* Sets the end time
	* @param string $arg0
	*/
	function setEndTime($arg0) {
		$this->endTime = $arg0;
		$this->setEndDate(date(self::DATE_FORMAT_MDY, strtotime($this->getEndDate())) . " " . date(self::DATE_FORMAT_TIME, strtotime($this->getEndTime())));
		return $this;
	}

	/**
	* Returns the date format
	* @return string
	*/
	function getDateFormat() {
		if (is_null($this->dateFormat)) {
			$this->dateFormat = DateRangeForm::DATE_FORMAT_MDY;
		}
		return $this->dateFormat;
	}

	/**
	* Sets the date format
	* @param string $arg0
	*/
	function setDateFormat($arg0) {
		$this->dateFormat = $arg0;
		return $this;
	}

	/**
	* Validates the input
	* @return void
	*/
	function validate() {
		parent::validate();
		if (!$this->isNoEnd()) {
			if (strtotime($this->getStartDate()) > strtotime($this->getEndDate())) {
				$this->getErrors()->addError("start_date", new Error("The start date must be before the end date."));
			}
		}
	}
}

