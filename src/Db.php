<?php

namespace Yagrysha\ORM;

use \mysqli;

class Db
{

    /**
     * @var mysqli
     */
    private static $db;
    private static $debug = false;
    private static $params = [
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'dbname' => '',
        'charset' => 'utf8'
    ];

    /**
     * @param array $params
     * @param bool $connect
     * @return array
     */
    public static function init($params = [], $connect = false)
    {
        if (!empty($params)) {
            self::$params = array_merge(self::$params, $params);
        }
        if ($connect) {
            self::connect();
        }
        return self::$params;
    }

    /**
     * @throws Exception
     * @return \mysqli
     */
    public static function connect()
    {
        if (null == self::$db) {
            $db = new mysqli(self::$params['host'], self::$params['user'], self::$params['password'], self::$params['dbname']);
            if ($db->connect_error) {
                throw new Exception('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error);
            }
            $db->set_charset(self::$params['charset']);
            self::$db = $db;
        }
        return self::$db;
    }

    public static function close(){
        self::$db->close();
        self::$db=null;
    }

    /**
     * @param bool $set
     * @return bool
     */
    public static function debug($set = true)
    {
        return self::$debug = $set;
    }

    /**
     *
     * @param bool $mode =TRUE
     * @return bool
     */
    public static function autocommit($mode = true)
    {
        return self::connect()->autocommit($mode);
    }

    /**
     *
     * @return bool
     */
    public static function commit()
    {
        return self::$db->commit();
    }

    /**
     *
     * @return bool
     */
    public static function rollback()
    {
        return self::$db->rollback();
    }

    /**
     * autocommit(FALSE)
     * @return bool
     */
    public static function transaction()
    {
        return self::connect()->autocommit(false);
    }

    /**
     * real_escape_string
     * @param string $string
     * @return string
     */
    public static function esc($string)
    {
        return self::connect()->real_escape_string($string);
    }

    /**
     * @param string $select
     * @return string
     */
    private static function getSelect($select)
    {
        if (empty($select)) {
            $select = '*';
        }
        return 'SELECT ' . $select;
    }

    /**
     *
     * @param array|string $where
     * @return string
     */
    private static function getWhere($where)
    {
        if (empty($where)) {
            return '';
        }
        if (is_array($where)) {
            foreach ($where as $k => &$v) {
                if (!is_int($k)) {
                    $v = self::esc($k) . '=\'' . self::esc($v) . '\'';
                }
            }
            $where = implode(' AND ', $where);
        }
        return ' WHERE ' . $where;
    }

    /**
     * @param array $params
     * @return string
     */
    private static function getOrder($params)
    {
        if (empty($params)) {
            return '';
        }
        if (is_array($params)) {
            foreach ($params as $k => &$v) {
                $v = $k . ' ' . $v;
            }
            $params = implode(', ', $params);
        }
        return ' ORDER BY ' . $params;
    }

    /**
     * @param array|int $limit
     * @return string
     */
    private static function getLimit($limit)
    {
        if (empty($limit)) {
            return '';
        }
        if (is_array($limit)) {
            $to = (int)$limit['to'];
            if (empty($limit['page'])) {
                $from = empty($limit['from']) ? 0 : $limit['from'];
            } else {
                $from = --$limit['page'] * $to;
            }
            return ' LIMIT ' . $from . ',' . $to;
        }
        return ' LIMIT ' . (int)$limit;
    }

    /**
     * @param string $sql
     * @throws Exception
     * @return \mysqli_result|bool
     */
    public static function query($sql)
    {
        if (self::$debug) {
            echo $sql . "<br>\n";
        }
        $result = self::connect()->query($sql);
        if ($result) {
            return $result;
        }
        if (self::$debug) {
            echo self::$db->error . "<br>\n";
        }
        return false;
    }

    /**
     * @param array $params
     * @return string
     */
    public static function buildQuery(array $params)
    {
        return self::getSelect(isset($params['select']) ? $params['select'] : '') .
        ' FROM ' . $params['from'] .
        (isset($params['where']) ? self::getWhere($params['where']) : '') .
        (isset($params['_xtra']) ? ' ' . $params['_xtra'] : '') .
        (isset($params['order']) ? self::getOrder($params['order']) : '') .
        (isset($params['limit']) ? self::getLimit($params['limit']) : '');
    }

    /**
     * @param $table
     * @param $params
     * @return array|bool
     */
    public static function select($table, array $params = [])
    {
        $params['from'] = $table;
        if (!$result = self::query(self::buildQuery($params))) {
            return false;
        }
        $return = [];
        if (isset($params['index'])) {
            while ($row = $result->fetch_assoc()) {
                $return[$row[$params['index']]] = $row;
            }
        } else {
            $return = $result->fetch_all(MYSQLI_ASSOC);
        }
        $result->free();
        return $return;
    }

    /**
     * @param $table
     * @param $params
     * @return bool
     */
    public static function selectCell($table, array $params = [])
    {
        $params['from'] = $table;
        $params['limit'] = 1;
        if (!$result = self::query(self::buildQuery($params))) {
            return false;
        }
        $row = $result->fetch_row();
        $result->free();
        return $row[0];
    }

    /**
     * @param $table
     * @param $params
     * @return bool
     */
    public static function selectOne($table, array $params = [])
    {
        $params['from'] = $table;
        $params['limit'] = 1;
        if (!$result = self::query(self::buildQuery($params))) {
            return false;
        }
        $row = $result->fetch_assoc();
        $result->free();
        return $row;
    }

    /**
     * @param string $table
     * @param string|array $where =''
     * @return string
     */
    public static function count($table, $where = '')
    {
        if (!$result = self::query(
            self::buildQuery(
                [
                    'select' => 'count(*)',
                    'from' => $table,
                    'where' => $where
                ]
            )
        )
        ) {
            return false;
        }
        $row = $result->fetch_row();
        $result->free();
        return $row[0];
    }

    /**
     * @param string $table
     * @param string $select
     * @param string $where =''
     * @return string cell
     */
    public static function cell($table, $select, $where = '')
    {
        if (!$result = self::query(
            self::buildQuery(
                [
                    'select' => $select,
                    'from' => $table,
                    'where' => $where,
                    'limit' => 1
                ]
            )
        )
        ) {
            return false;
        }
        $row = $result->fetch_row();
        $result->free();
        return $row[0];
    }

    /**
     * Упрощенный selectone
     * @param string $table
     * @param string $select
     * @param string|array $where
     * @return array
     */
    public static function row($table, $where = '', $select = '*')
    {
        if (!$result = self::query(
            self::buildQuery(
                [
                    'select' => $select,
                    'from' => $table,
                    'where' => $where,
                    'limit' => 1
                ]
            )
        )
        ) {
            return false;
        }
        $row = $result->fetch_assoc();
        $result->free();
        return $row;
    }

    /**
     * @param $table
     * @param string $where
     * @param string $select
     * @param string $limit
     * @param bool $plain_array
     * @return array|bool
     */
    public static function rows($table, $where = '', $select = '*', $limit = '', $plain_array = false)
    {
        if (!$result = self::query(
            self::buildQuery(
                [
                    'select' => $select,
                    'from' => $table,
                    'where' => $where,
                    'limit' => $limit
                ]
            )
        )) {
            return false;
        }
        $return = [];
        if ($plain_array) {
            while ($row = $result->fetch_assoc()) {
                $return[] = $row[$select];
            }
        } else {
            $return = $result->fetch_all(MYSQLI_ASSOC);
        }
        $result->free();
        return $return;
    }

    /**
     * DELETE FROM | TRUNCATE TABLE
     * @param string $table
     * @param array|string $where
     * @return bool
     */
    public static function delete($table, $where)
    {
        if (empty($where)) {
            $sql = 'TRUNCATE TABLE ' . $table;
        } else {
            $sql = 'DELETE FROM ' . $table . self::getWhere($where);
        }
        return self::query($sql);
    }

    /**
     *
     * @param string $table
     * @param array $params
     * @param bool $replace
     * @return int  insert_id
     */
    public static function insert($table, array $params, $replace = false)
    {
        $values = [];
        $keys = [];
        foreach ($params as $k => $v) {
            $keys[] = '`' . self::esc($k) . '`';
            $values[] = '\'' . self::esc($v) . '\'';
        }
        $sql = ($replace ? 'REPLACE' : 'INSERT') . ' INTO ' . $table . ' ('
            . implode(',', $keys) . ') VALUES(' . implode(',', $values) . ')';
        if (self::query($sql)) {
            return self::$db->insert_id;
        }
        return false;
    }

    /**
     *
     * @param string $table
     * @param string|array $params
     * @return bool
     */
    public static function replace($table, array $params)
    {
        return self::insert($table, $params, true);
    }

    /**
     *
     * @param string $table
     * @param string|array $set
     * @param string|array $where
     * @return bool
     */
    public static function update($table, $set, $where)
    {
        if (is_array($set)) {
            $var = [];
            foreach ($set as $k => $v) {
                $var[] = '`' . self::esc($k) . '`=\'' . self::esc($v) . '\'';
            }
            $var = implode(', ', $var);
        } else {
            $var = $set;
        }
        return self::query('UPDATE ' . $table . ' SET ' . $var . self::getWhere($where));
    }
}