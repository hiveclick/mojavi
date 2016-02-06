<?php
namespace Mojavi\Form;
/**
 * GoogleChart contains methods to work with the Google Chart API v2
 */
class GoogleChart extends CommonForm {
	
	protected $cols;
	protected $slices;
	protected $rows;
	protected $series;
	
	private $_row_data;
	private $_col_data;
	private $_series_data;
		
	/**
	 * Adds a row
	 * @param $row_key
	 * @param $values
	 */
	function addRow($date, $values) {
		$tmp_array = $this->getRowData();
		$tmp_array[$date] = array('c' => $values);
		$this->setRowData($tmp_array);	
	}
	
	/**
	 * Adds a piece of data to a row
	 * @param $col_key
	 * @param $row_key
	 * @param $value
	 */
	function addData($date, $col_key, $value, $formatted_value = null) {
		$tmp_array = $this->getRowData();
		$header_cols = $this->getCols();
		if ($date instanceof \DateTime) {
			$date_key = $date->format('m/d/Y H:00:00');
		} else {
			$date_key = $date;
		}
		if (isset($tmp_array[$date_key])) {
			foreach ($header_cols as $key => $header_col) {
				if ($header_col['label'] == $col_key) {
					$tmp_array[$date_key]['c'][$key] = array('v' => $value, 'f' => $formatted_value);
				}
			}
			$this->setRowData($tmp_array);	
		} else {
			$row_data = array();
			$col_counter = 0;
			foreach ($this->getColumnData() as $key => $column) {
				if ($col_counter == 0) {
					if ($date instanceof \DateTime) {
					   $row_data[0] = array('v' => 'Date(' . $date->format('Y') . ',' . ($date->format('m') - 1) . ',' . $date->format('d,H,0,0,0') . ')');
					} else {
						$row_data[0] = array('v' => $date, 'f' => null);
					}
				} else if ($col_key == $column['label']) {
					$row_data[$col_counter] = array('v' => $value, 'f' => $formatted_value);
				} else {
					$row_data[$col_counter] = array('v' => 0, 'f' => null);
				}
				$col_counter++;
			}
			$this->addRow($date_key, $row_data);
		}
	}
	
	/**
	 * Adds a column
	 * @param $id
	 * @param $name
	 * @param $type
	 */
	function addColumn($id, $name, $type = 'string', $role = null, $color = null, $add_to_series = true) {
		$tmp_array = $this->getColumnData();
		$tmp_array[$name] = array('id' => $id, 'label' => $name, 'pattern' => '', 'type' => $type, 'role' => $role);
		$this->setColumnData($tmp_array);
		
		if ($add_to_series && ($name != 'Date')) {
			$this->addSeries($name, $color);
		}
	}
	
	/**
	 * Adds a column
	 * @param $id
	 * @param $name
	 * @param $type
	 */
	function addSeries($name, $color = null) {
		if (trim($color) == '') {
			$color = null;	
		}
		$tmp_array = $this->getSeriesData();
		$tmp_array[$name] = array('name' => $name, 'color' => $color, 'orig_color' => $color);
		$this->setSeriesData($tmp_array);
	}
	
	/**
	 * Returns the series
	 * @return array
	 */
	function getSeries() {
		return array_values($this->getSeriesData());
	}
	
	/**
	 * Returns the rows
	 * @return array
	 */
	function getCols() {
		return array_values($this->getColumnData());
	}
	
	/**
	 * Returns the rows
	 * @return array
	 */
	function getRows() {
		return array_values($this->getRowData());
	}
	
	/**
	 * Fills the chart data with blank values for the date range
	 * @param $num_of_days integer
	 * @return boolean
	 */
	function fillChartData($start_date, $end_date) {
		$tmp_start_date = date("Ymd", strtotime($start_date));
		while ($tmp_start_date < date("Ymd", strtotime($end_date))) {
			$row_data = array();
			$col_counter = 0;
			foreach ($this->getColumnData() as $key => $column) {
				if ($col_counter == 0) {
					$row_data[0] = array('v' => date('d', strtotime($start_date)), 'f' => null);
				} else {
					$row_data[$col_counter] = array('v' => 0, 'f' => null);
				}
				$col_counter++;
			}
			$this->addRow(date('d', strtotime($tmp_start_date)), $row_data);
			$start_date = date("m/d/Y", strtotime($start_date . ' + 1 day'));
			$tmp_start_date = date("Ymd", strtotime($start_date));
		}
	}	
	
	/********************************************/
	/*			 Private functions			*/
	/********************************************/
	/**
	 * Returns the _col_data
	 * @return array
	 */
	private function getColumnData() {
		if (is_null($this->_col_data)) {
			$this->_col_data = array();
		}
		return $this->_col_data;
	}
	
	/**
	 * Sets the _col_data
	 * @var array
	 */
	private function setColumnData($arg0) {
		$this->_col_data = $arg0;
		return $this;
	}
	
	/**
	 * Returns the _row_data
	 * @return array
	 */
	private function getRowData() {
		if (is_null($this->_row_data)) {
			$this->_row_data = array();
		}
		return $this->_row_data;
	}
	
	/**
	 * Sets the _row_data
	 * @var array
	 */
	private function setRowData($arg0) {
		$this->_row_data = $arg0;
		return $this;
	}
	
	/**
	 * Returns the _series_data
	 * @return array
	 */
	private function getSeriesData() {
		if (is_null($this->_series_data)) {
			$this->_series_data = array();
		}
		return $this->_series_data;
	}
	
	/**
	 * Sets the _series_data
	 * @var array
	 */
	private function setSeriesData($arg0) {
		$this->_series_data = $arg0;
		return $this;
	}
	
}