<?php
namespace Yagrysha\ORM;

trait MapperTrait
{
    /**
     * @param int $id
     * @return Item|null
     */
    public static function findByPk($id)
    {
        return Mapper::findByPk(__CLASS__, $id, self::PRK);
    }

    /**
     * @param int $id
     * @return Item|null
     */
    public static function findById($id)
    {
        return Mapper::findByPk(__CLASS__, $id, self::PRK);
    }

    /**
     * @param array|string $where
     * @return Item[]|null
     */
    public static function findAll($where)
    {
        return Mapper::findBySqlAll(__CLASS__, $where);
    }

    /**
     * @param $where
     * @return Item|null
     */
    public static function find($where)
    {
        return Mapper::findBySql(__CLASS__, $where);
    }

    /**
     * @param $params
     * @return Item|null
     */
    public static function findByParams(array $params)
    {
        return Mapper::findByParams(__CLASS__, $params);
    }

    /**
     * @param $params
     * @return Item[]|null
     */
    public static function findByParamsAll(array $params)
    {
        return Mapper::findByParamsAll(__CLASS__, $params);
    }

    public static function findByField($name, $value)
    {
        return Mapper::findBySql(__CLASS__, [$name => $value]);
    }

    public static function findByFieldAll($name, $value)
    {
        return Mapper::findBySqlAll(__CLASS__, [$name => $value]);
    }

    /**
     * find or create (!save)
     * @param $id
     * @return self|null
     */
    public static function getById($id)
    {
        $pk = self::PRK;
        $obj = Mapper::findByPk(__CLASS__, $id, $pk);
        if (!$obj) {
            $obj = new self();
            $obj->$pk = $id;
        }
        return $obj;
    }

    /**
     * create and NO save
     * @param array $params
     * @return Item
     */
    public static function create($params = [])
    {
        $obj = new self();
        $obj->setArray($params);
        return $obj;
    }

    public static function findOrCreate($id)
    {
        return Mapper::findOrCreate(__CLASS__, $id, self::PRK);
    }
}