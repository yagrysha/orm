<?php
namespace Yagrysha\ORM;

abstract class Row extends Record
{
    //array
    protected $_prk = null;

    public function __clone()
    {
        $this->_loaded = false;
        if (is_array($this->_prk)) {
            foreach ($this->_prk as $k) {
                $this->_fields[$k] = null;
            }
        } else {
            $this->setId(null);
        }
    }

    public function getId()
    {
        if (is_array($this->_prk)) {
            return array_intersect_key($this->_fields, array_flip($this->_prk));
        }
        return parent::getId();
    }

    public function setId($value)
    {
        if (is_array($this->_prk)) {
            foreach($value as $k=>$v){
                parent::set($k,$v);
            }
        } else {
            parent::setId($value);
        }
    }

    public function isChangedPk()
    {
        if ($this->isChanged()) {
            if (is_array($this->_prk)) {
                foreach ($this->_prk as $key) {
                    if (!empty($this->_changed[$key])) {
                        return true;
                    }
                }
            } else {
                return !empty($this->_changed[static::PRK]);
            }
        }
        return false;
    }

    public function getPkArray()
    {
        if (is_array($this->_prk)) {
            return $this->getId();
        } else {
            return parent::getPkArray();
        }
    }

    /**
     * insert or update table row
     * @return bool
     */
    public function save()
    {
        if (!$this->isChanged()) {
            return false;
        }
        if ($this->isLoaded() && !$this->isChangedPk()) {
            if (false === $this->update()) {
                return false;
            }
        } else {
            if (false === $this->replace()) {
                return false;
            }
        }
        return true;
    }

    public function delete()
    {
        if ($this->isLoaded() || $this->isChangedPk()) {
           parent::delete();
        }
    }
}