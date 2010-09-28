<?php
class Ifrond_Model_Item
{
    /* @var _row array */
    protected $_row;
    /* @var _mapper Ifrond_Model_Mapper */
    protected $_mapper;
    public function __construct (array $options = null)
    {
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }
    public function __set ($name, $value)
    {
        $method = 'set' . $name;
        $methodList = get_class_methods($this);
        if (('mapper' == $name) || ! in_array($method, $methodList)) {
            $this->_row[$name] = $value;
        } else {
            return $this->$method($value);
        }
        return $this;
    }
    public function __get ($name)
    {
        $method = 'get' . $name;
        $methodList = get_class_methods($this);
        if (('mapper' == $name) || ! in_array($method, $methodList)) {
            if (isset($this->_row[$name])) return $this->_row[$name];
            else return null;
        }
        return $this->$method();
    }
    public function __call ($name, $args)
    {
        if (substr($name, 0, 3) !== 'get') {
            $name = 'get' . ucfirst($name);
        }
        $methodList = get_class_methods($this);
        if (in_array($name, $methodList)) {
            return $this->$name($args);
        } else {
            return $this->getMapper()->$name($args);
        }
        return $this->$method();
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
    public function getMapper ()
    {
        return $this->_mapper;
    }
    public function setMapper (Ifrond_Model_Mapper $mapper)
    {
        $this->_mapper = $mapper;
    }
    public function setRow (array $row)
    {        
        $this->_row = $row;
        return $this;
    }
    public function getRow ()
    {
        return $this->_row;
    }
    public function update ()
    {
        $row = $this->getMapper()->find($this->id);
        $this->setRow($row->getRow());
        return $this;
    }
    public function save ()
    {
        $row = $this->getRow();
        $row = $this->getMapper()->save($row);
        $this->setRow($row);
        return $this;
    }
    public function delete ()
    {
        $row = $this->getRow();        
        return $this->getMapper()->delete($row['id']);
    }
    public function copy ()
    {
        return $this->getMapper()->createNew($this->getRow());
    }
}