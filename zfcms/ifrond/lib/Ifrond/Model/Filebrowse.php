<?php
class Ifrond_Model_Filebrowse
{

	protected $_dir = null;
	protected $_disallowedExtentions = array('php', 'pl');
	protected $_requiredExtentions = array();
	protected $_files;
	protected $_subDirs = array();
	protected $_dirPath = null;
	protected $_sortField = 'date';
	protected $_sortOrder = 'desc';

	public function __construct (array $options = null)
	{
		if (is_array($options)) {
			$this->setOptions($options);
		}
	}

	public function setOptions (array $options)
	{
		$methods = get_class_methods($this);
		foreach ($options as $key => $value) {
			$method = 'set' . ucfirst($key);
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		return $this;
	}

	public function setDir ($dir)
	{
		$this->_dirPath = $this->checkDir($dir);
		$this->_dir = dir($this->_dirPath);
	}

	public function getDirPath ()
	{
		return $this->_dirPath;
	}

	public function setSortField ($sort)
	{
		$this->_sortField = $sort;
	}

	public function setSortOrder ($order)
	{
		$this->_sortOrder = $order;
	}

	public function setDisallowedExtentions($v) {
		$this->_disallowedExtentions = $v;
	}

	public function setRequiredExtentions($v) {
		$this->_requiredExtentions = $v;
	}

	public function isAllowedFile ($file) {
		if (is_dir($file)) return false;
		$path_parts = pathinfo($file);
		if (isset($path_parts['extension'])) {
			$extension = strtolower($path_parts['extension']);
		} else {
			$extension = '';
		}
		if (sizeof($this->_requiredExtentions) > 0) {
			if (!in_array($extension, $this->_requiredExtentions)) return false;
			else return true;
		}
		if (sizeof($this->_disallowedExtentions) > 0) {
			if (in_array($extension, $this->_disallowedExtentions)) return false;
			else return true;
		}
		return false;
	}

	public function getFiles ($refresh = false)
	{
		if (sizeof($this->_files) > 0 && $refresh != false) {
			return $this->_files;
		}
		$d = $this->_dir;
		$filesList = array();
		while (false !== ($entry = $d->read())) {
			if ($entry != ".." && $entry != ".") {
				$f = $d->path.'/'.$entry;
				if ($this->isAllowedFile($f)) {
					$row = array();
					$row['date'] = filectime($f);
					$row['size'] = filesize($f)/(1024*1024);
					$row['name'] = $entry;
					$row['path'] = $f;
					$filesList[] = $row;
				}
			}
		}
		$d->close();
		$this->_files = $filesList;
		$this->sort();
		return $this->_files;
	}

	public function sort ()
	{
		$natsort = FALSE;
		$case_sensitive = FALSE;
		$this->_files = sort2d($this->_files, $this->_sortField, $this->_sortOrder, $natsort, $case_sensitive);
		return $this->_files;
	}

	public function getSubdirs ($refresh = true)
	{
		if (sizeof($this->_subDirs) > 0 && $refresh != false) {
			return $this->_subDirs;
		}
		$d = $this->_dir;
		$dirList = array();
		while (false !== ($entry = $d->read())) {
			if ($entry != ".." && $entry != ".") {
				$f = $video->path.$entry;
				if (is_dir($f)) {
					$row = array();
					$row['date'] = filectime($f);
					$row['name'] = $entry;
					$dirList[] = $row;
				}
			}
		}
		$d->close();
		$this->_subDirs = $dirList;
	}

	public function checkDir ($dir) {
		$dir = realpath($dir);
		if (!is_dir($dir)) {
			$dir = PATH_WEBROOT.'/'.$dir;
			$dir = realpath($dir);
			if (!is_dir($dir)) {
				return false;
			}
		}
		return $dir;
	}

}