<?php
namespace Yagrysha\ORM;

abstract class Record implements \ArrayAccess
{
    protected $_fields = [];
    protected $_changed = [];
    protected $_loaded = false;
    const PRK = 'id';

    public function __construct($loaded = false)
    {
        $this->_loaded = $loaded;
    }

    public function save(){
		return $this->replace();
	}

    public function delete()
    {
        return Db::delete(static::TABLE, $this->getPkArray());
    }

    public function replace()
    {
        if (false === Db::insert(static::TABLE, $this->_fields, true)) {
            return false;
        }
        $this->_changed = [];
        $this->_loaded = true;
        return true;
    }

    public function insert()
    {
        $ret = Db::insert(static::TABLE, $this->_fields);
        if (false === $ret) {
            return false;
        }
        $this->_changed = [];
        $this->_loaded = true;
        return $ret===0?true:$ret;
    }

    public function update()
    {
        $condition = $this->getPkArray();
        if (false == Db::update(
                static::TABLE,
                array_intersect_key(array_diff_key($this->_fields, $condition), $this->_changed),
                $condition
            )
        ) {
            return false;
        }
        $this->_changed = [];
        $this->_loaded = true;
        return true;
    }

    public function getId()
    {
        return $this->get(static::PRK);
    }

    /**
     * @param $value
     */
    public function setId($value)
    {
        $this->set(static::PRK, $value);
    }

    /**
     * @return array
     */
    public function getPkArray()
    {
        return [static::PRK => $this->getId()];
    }

    /**
     * @return string
     */
    public function getIdString()
    {
        $id = $this->getId();
        if (is_array($id)) {
            return implode('-', $id);
        }
        return (string)$id;
    }

    /**
     * @param array $data
     * @param bool $loaded
     * @return bool
     */
    public function init(array $data, $loaded = true)
    {
        $this->_changed = [];
        $this->_fields = $data;
        $this->_loaded = $loaded;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param $name
     * @param $value
     */
    public function set($name, $value)
    {
        if (!isset($this->_fields[$name]) || $this->_fields[$name] !== $value) {
            $this->_changed[$name] = true;
        }
        $this->_fields[$name] = $value;
    }

    /**
     * @param array $params
     */
    public function setArray(array $params)
    {
        foreach ($params as $k => $v) {
            $this->set($k, $v);
        }
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        if (isset($this->_fields[$name])) {
            return $this->_fields[$name];
        }
        return null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->_fields[$name]);
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->_fields;
    }

    public function getChangedFields()
    {
        return array_intersect_key($this->_fields, $this->_changed);
    }

    /**
     * @param $name
     */
    public function __unset($name)
    {
        $this->offsetUnset($name);
    }

    public function __clone()
    {
        $this->setId(null);
        $this->_loaded = false;
    }

    public function reset()
    {
        $this->init([], false);
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->_loaded;
    }

    /**
     * @param null $name
     * @return bool
     */
    public function isChanged($name = null)
    {
        if (null === $name) {
            return !empty($this->_changed);
        }
        return (isset($this->_changed[$name]) && $this->_changed[$name]);
    }

    public function isChangedPk()
    {
        if ($this->isChanged()) {
            return !empty($this->_changed[static::PRK]);
        }
        return false;
    }

    /**
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->_fields[$offset]);
    }

    /**
     * @param $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param $offset
     * @param $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @param $offset
     */
    public function offsetUnset($offset)
    {
        if ($this->offsetExists($offset)) {
            unset($this->_fields[$offset]);
        }
    }
}