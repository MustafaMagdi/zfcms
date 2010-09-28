<?php

class Ifrond_Model_Db_Table extends Zend_Db_Table_Abstract
{
    /** Table name */
    protected $_name = 'page';
    protected $_prefix = null;
    protected function _setupTableName ()
    {        
    	parent::_setupTableName();
        if ($this->_prefix !== null) {
            $this->_name = $this->_prefix . $this->_name;
        } else {
            if (defined('_DBTABLE_PREFIX')) {
                $this->_name = _DBTABLE_PREFIX . $this->_name;
            }
        }
    }
    public function getTableName() {
    	return $this->_name;
    }
}