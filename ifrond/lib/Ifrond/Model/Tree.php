<?php
class Ifrond_Model_Tree
{
    /* @var _mapper Ifrond_Model_Mapper */
    protected $_mapper;
    /* @var _mapper string */
    protected $_fieldNames = array('isCat' => 'is_cat' , 'parentKey' => 'pid' , 'title' => 'title' , 'subtitle' => 'subtitle' , 'isActive' => 'is_active' , 'date' => 'date_add' , 'id' => 'id' , 'shortcut' => 'shortcut');
    protected $_order = array('rang ASC', 'date_add ASC');
    protected $_casheFile = 'itemtree.xml';
    protected $_tree = false;
    public function __construct ()
    {}
    public function getMapper ()
    {
        return $this->_mapper;
    }
    public function setMapper (Ifrond_Model_Mapper $mapper)
    {        
    	$this->_mapper = $mapper;
    }
    public function getFieldName ($field)
    {
        return $this->_fieldNames[$field];
    }
    public function getField ($field, $row)
    {
        return $row[$this->getFieldName($field)];
    }
    public function parseTree ($id = 0, $activeOnly = false)
    {        
    	$select = $this->getMapper()->getDBTable()->select();
        $select->where('`' . $this->getFieldName('isCat') . '` = ?', 1)->where('`' . $this->getFieldName('parentKey') . '` = ?', $id)->order($this->_order);
        if ($activeOnly) {
            $select->where('`' . $this->getFieldName('isActive') . '` = ?', 1);
        }
        $items = $this->getMapper()->getDBTable()->fetchAll($select);
        $xmlTreeStr = "";
        foreach ($items as $t) {
            $md5sc = md5($this->getField('shortcut', $t));
            $xmlTreeStr .= '<item_' . $md5sc . ' ';
            foreach ($this->_fieldNames as $k => $tt) {
                $xmlTreeStr .= $this->getFieldName($k) . '="' . htmlspecialchars($this->getField($k, $t)) . '" ';
            }
            $xmlTreeStr .= ' >';
            $xmlTreeStr .= $this->parseTree($t['id'], $activeOnly);
            $xmlTreeStr .= '</item_' . $md5sc . '>';
        }
        return $xmlTreeStr;
    }
    public function refreshTree ()
    {
        $xmlStr = $this->parseTree(0);
        $xmlStr = '<?xml version="1.0" encoding="utf-8"?><itemtree>' . $xmlStr . '</itemtree>';
        $doc = new DOMDocument('1.0', 'utf-8');
        $doc->preserveWhiteSpace = false;
        $doc->loadXML($xmlStr);
        $doc->formatOutput = true;
        $doc->save(PATH_TEMP . '/' . $this->_casheFile);
    }
    public function getTree ()
    {
        if ($this->_tree == false) {
	    	if (! is_file(PATH_TEMP . '/' . $this->_casheFile))
	            $this->refreshTree();        
	        $this->_tree = simplexml_load_file(PATH_TEMP . '/' . $this->_casheFile);
        }
        return $this->_tree;
    }
	public function flushCache ()
    {
        $f = PATH_TEMP . '/' . $this->_casheFile;
    	if (is_file($f)) unlink($f);
        return $this;
    }
    public function find ($path)
    {
        if (! $this->_tree) {
            $this->getTree();
        }
        $pathParts = explode('/', $path);
        $newPathParts = array();
        foreach ($pathParts as $tP) {
            $tP = trim($tP);
            if ($tP != '')
                $newPathParts[] = 'item_' . md5($tP);
        }        
        $newPathStr = implode('/', $newPathParts);
        if ($newPathStr == '') return false;
        
        $result = $this->_tree->xpath($newPathStr);
        //_p($result);
        if (!$result) return false;
        if (sizeof($result) == 0) {
            return false;
        } else {
            //echo '<pre>'; print_r($result[0]->attributes()); echo '</pre>'; die();
            $row = $this->attrToArray($result[0]);
            //echo '<pre>'; print_r($row); echo '</pre>'; die();
            return $row;
        }
    }
    public function getTreePathTo ($id)
    {
        $id = intval($id);
        $parents = $this->getParents($id);
        if (sizeof($parents) == null)
            return '';
        $parentsArr = array();
        if (is_array($parents) && sizeof($parents) > 0) {
        	//_p($parents, false);
	        foreach ($parents as $p) {
	            $parentsArr[] = $p['shortcut'];
	        }
	        $path = implode('/', array_reverse($parentsArr));
        	return $path . '/';
        } else {
        	return false;
        }        
    }
    public function attrToArray ($element)
    {
        $tArr = array();
        foreach ($element->attributes() as $key => $t) {
            $tArr[$key] = (string) $t;
        }
        return $tArr;
    }
    public function getParents ($id)
    {
        if (! $this->_tree)
            $this->getTree();
        if ($id > 0) {
            $arrs = array();
            $t = $this->_tree->xpath('//*[@id=' . $id . ']');
            if (!isset($t[0])) return false;           
            $arrs[] = $this->attrToArray($t[0]);
            $item = $t[0];
            $t = $item->xpath('..');
            $parent = $t[0];
            while ($parent) {
                if ($parent['id'] > 0) {
                    $arrs[] = $this->attrToArray($parent);
                    $t = $parent->xpath('..');
                    $parent = $t[0];
                } else {
                    $parent = false;
                }
            }
            return $arrs;
        } else
            return null;
    }
}