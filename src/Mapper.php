<?php
namespace Yagrysha\ORM;

class Mapper
{
    /**
     * @var array
     */
    private static $map = [];

    /**
     * @param dbItem|string $class
     * @return dbItem|null
     */
    public static function findAll($class)
    {
        $data = Db::rows($class::TABLE);
        if (empty($data)) {
            return null;
        }
        return self::buildMultiple($class, $data);
    }

    /**
     * @param dbItem|string $class
     * @param string|array $sql
     * @return dbItem|null
     */
    public static function findBySql($class, $sql)
    {
        $data = Db::row($class::TABLE, '', $sql);
        if (empty($data)) {
            return null;
        }
        return self::build($class, $data);
    }

    /**
     * @param dbItem|string $class
     * @param string|array $sql
     * @param int $limit
     * @return null|dbItem
     */
    static function findBySqlAll($class, $sql, $limit = 9999)
    {
        $data = Db::rows($class::TABLE, '', $sql, $limit);
        if (empty($data)) {
            return null;
        }
        return self::buildMultiple($class, $data);
    }

    /**
     * @param $class
     * @param $params
     * @return array|null
     */
    static function findByParamsAll($class, array $params)
    {
        $data = Db::select(isset($params['from']) ? $params['from'] : $class::TABLE, $params);
        if (empty($data)) {
            return null;
        }
        return self::buildMultiple($class, $data);
    }

    /**
     * @param $class
     * @param $params
     * @return null|dbItem
     */
    static function findByParams($class, array $params)
    {
        $data = Db::selectOne(isset($params['from']) ? $params['from'] : $class::TABLE, $params);
        if (empty($data)) {
            return null;
        }
        return self::build($class, $data);
    }

    /**
     * @param dbItem|string $class
     * @param string $pk
     * @param string|int $id
     * @return null|dbItem
     */
    static function findByPk($class, $id, $pk = 'id')
    {
        if (isset(self::$map[$class][$id])) {
            return self::$map[$class][$id];
        }
        $data = Db::row($class::TABLE, [$pk => $id]);
        if (empty($data)) {
            return null;
        }
        $obj = self::build($class, $data);
        self::$map[$class][$id] = $obj;
        return $obj;
    }

    static function findOrCreate($class, $id, $pk = 'id')
    {
        if (isset(self::$map[$class][$id])) {
            return self::$map[$class][$id];
        }
        $data = Db::row($class::TABLE, [$pk => $id]);
        if (empty($data)) {
            $obj = new $class;
            $obj->$pk = $id;
        } else {
            $obj = self::build($class, $data);
        }
        self::$map[$class][$id] = $obj;
        return $obj;
    }

    /**
     * @param $class
     * @param $data
     * @return array
     */
    public static function buildMultiple($class, array $data)
    {
        $ret = [];
        foreach ($data as $row) {
            $obj = new $class();
            $obj->init($row);
            if ($obj instanceof Record) {
                $ret[$obj->getIdString()] = $obj;
            } else {
                $ret[] = $obj;
            }
        }
        return $ret;
    }

    /**
     * @param $class
     * @param $data
     * @return mixed
     */
    public static function build($class, array $data)
    {
        $obj = new $class();
        $obj->init($data);
        return $obj;
    }
}