<?php
namespace Yagrysha\ORM;

abstract class Item extends Record
{
    /**
     * insert or update table row
     * @return bool
     */
    public function save()
    {
        if (!$this->isChanged()) {
            return false;
        }
        if (null === $this->getId()) {
            $id = $this->insert();
            if(false===$id) return false;
            $this->setId($id);
        } else {
            if(false===$this->update()) return false;
        }
        return true;
    }

    public function delete()
    {
        if (null !== $this->getId()) {
            parent::delete();
        }
    }
}
