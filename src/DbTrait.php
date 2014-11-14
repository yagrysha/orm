<?php
namespace Yagrysha\ORM;

trait DbTrait
{
    /**
     * @param $where
     * @return string
     */
    public static function count($where = '')
    {
        return Db::count(self::TABLE, $where);
    }

    /**
     * @param $select
     * @param array|string $where
     * @return string
     */
    public static function cell($select, $where = '')
    {
        return Db::cell(self::TABLE, $select, $where);
    }

    /**
     * @param array|string $where
     * @param string $select
     * @return array
     */
    public static function row($where = '', $select = '')
    {
        return Db::row(self::TABLE, $select, $where);
    }

    /**
     * @param $id
     * @param string $select
     * @return array
     */
    public static function rowId($id, $select = '')
    {
        return Db::row(self::TABLE, $select, [self::PRK => $id]);
    }

    /**
     * @param array|string $where
     * @param string $select
     * @param string $limit
     * @param bool $plain_array
     * @return array
     */
    public static function rows($where = '', $select = '', $limit = '', $plain_array = false)
    {
        return Db::rows(self::TABLE, $select, $where, $limit, $plain_array);
    }

    /**
     * @param $params
     * @return array|bool
     */
    public static function select(array $params)
    {
        return Db::select(self::TABLE, $params);
    }

    /**
     * @param $params
     * @return bool
     */
    public static function selectOne(array $params)
    {
        return Db::selectOne(self::TABLE, $params);
    }

    /**
     * @param $params
     * @return bool
     */
    public static function selectCell(array $params)
    {
        return Db::selectCell(self::TABLE, $params);
    }

    /**
     * @param $where
     * @return bool
     */
    public static function del($where)
    {
        return Db::delete(self::TABLE, $where);
    }

    /**
     * @param $id
     * @return bool
     */
    public static function delId($id)
    {
        return Db::delete(self::TABLE, [self::PRK => $id]);
    }

    /**
     * @param $set
     * @param $where
     * @return bool
     */
    public static function upd($set, $where)
    {
        return Db::update(self::TABLE, $set, $where);
    }

    /**
     * @param $id
     * @param $set
     * @return bool
     */
    public static function updId($id, $set)
    {
        return Db::update(self::TABLE, $set, [self::PRK => $id]);
    }

    /**
     * @param $set
     * @param bool $replace
     * @return int
     */
    public static function ins(array $set, $replace = false)
    {
        return Db::insert(self::TABLE, $set, $replace);
    }
} 