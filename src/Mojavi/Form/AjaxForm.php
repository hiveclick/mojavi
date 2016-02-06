<?php
namespace Mojavi\Form;
/**
 * AjaxForm contains methods to work with ajax requests
 */
class AjaxForm extends CommonForm {

	private $hide_pagination;
	private $hide_meta;
	
	protected $result;
	protected $record;
	protected $entries;
	protected $insert_id;
	protected $rows_affected;

	/**
	 * Returns the _result
	 * @return string
	 */
	function getResult() {
		if ($this->getErrors()->isEmpty()) {
			return 'SUCCESS';
		} else {
			return 'FAILED';
		}
	}

	/**
	 * Returns the _record
	 * @return Form
	 */
	function getRecord() {
		if (is_null($this->record)) {
			$this->record = array();
		}
		return $this->record;
	}

	/**
	 * Sets the _record
	 * @param Form $arg0
	 */
	function setRecord($arg0) {
		$this->record = $arg0;
		return $this;
	}

	/**
	 * Returns the _entries
	 * @return array
	 */
	function getEntries() {
		if (is_null($this->entries)) {
			$this->entries = array();
		}
		return $this->entries;
	}

	/**
	 * Sets the _entries
	 * @param $arg0 array
	 */
	function setEntries($arg0) {
		$this->entries = $arg0;
		return $this;
	}

	/**
	 * Returns the _insert_id
	 * @return integer
	 */
	function getInsertId() {
		if (is_null($this->insert_id)) {
			$this->insert_id = 0;
		}
		return $this->insert_id;
	}

	/**
	 * Sets the _insert_id
	 * @param $arg0 integer
	 */
	function setInsertId($arg0) {
		$this->insert_id = $arg0;
		return $this;
	}

	/**
	 * Returns the _rows_affected
	 * @return integer
	 */
	function getRowsAffected() {
		if (is_null($this->rows_affected)) {
			$this->rows_affected = 0;
		}
		return $this->rows_affected;
	}

	/**
	 * Sets the _rows_affected
	 * @param $arg0 integer
	 */
	function setRowsAffected($arg0) {
		$this->rows_affected = $arg0;
		return $this;
	}
	
	/**
	 * Returns the hide_pagination
	 * @return boolean
	 */
	function getHidePagination() {
		if (is_null($this->hide_pagination)) {
			$this->hide_pagination = false;
		}
		return $this->hide_pagination;
	}
	
	/**
	 * Sets the hide_pagination
	 * @var boolean
	 */
	function setHidePagination($arg0) {
		$this->hide_pagination = $arg0;
		return $this;
	}
	
	/**
	 * Returns the hide_meta
	 * @return boolean
	 */
	function getHideMeta() {
		if (is_null($this->hide_meta)) {
			$this->hide_meta = false;
		}
		return $this->hide_meta;
	}
	
	/**
	 * Sets the hide_meta
	 * @var boolean
	 */
	function setHideMeta($arg0) {
		$this->hide_meta = $arg0;
		return $this;
	}

	/**
	 * Override default toArray functionality so we don't add too much to the request
	 * @param boolean $deep
	 */
	function toArray($deep = false, $use_null_for_blank = true, $preserve_object_ids = false) {
		$ret_val = array();
		$ret_val['result'] = $this->getResult();
		$ret_val['errors'] = $this->getErrors()->toArray();
		if (!$this->getHideMeta()) {
			$ret_val['meta']['insert_id'] = $this->getInsertId();
			$ret_val['meta']['rows_affected'] = $this->getRowsAffected();
		}
		if (!$this->getHidePagination()) {
			$ret_val['pagination']['draw'] = $this->getDraw();
			$ret_val['pagination']['page'] = $this->getPage();
			$ret_val['pagination']['items_per_page'] = $this->getItemsPerPage();
			$ret_val['pagination']['page_count'] = $this->getPageCount();
			$ret_val['pagination']['total_rows'] = $this->getTotal();
		}
		if (is_object($this->getRecord())) {
			$ret_val['record'] = $this->getRecord()->toArray($deep, $use_null_for_blank, $preserve_object_ids);
		} else {
			$ret_val['record'] = $this->getRecord();
		}
		if (is_object($this->getEntries())) {
			foreach ($this->getEntries() as $entry) {
				if (is_object($entry)) {
					$ret_val['entries'][] = $entry->toArray($deep, $use_null_for_blank, $preserve_object_ids);
				} else if (is_array($entry)) {
					$ret_val['entries'][] = $entry;
				}
			}
		} else if (is_array($this->getEntries())) {
			foreach ($this->getEntries() as $entry) {
				if (is_object($entry)) {
					$ret_val['entries'][] = $entry->toArray($deep, $use_null_for_blank, $preserve_object_ids);
				} else if (is_array($entry)) {
					$ret_val['entries'][] = $entry;
				}
			}
		} else {
			$ret_val['entries'] = $this->getEntries();
		}
		return $ret_val;

	}
}
