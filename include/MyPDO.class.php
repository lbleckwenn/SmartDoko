<?php

// https://stackoverflow.com/questions/1472250/pdo-working-with-table-prefixes
class MyPDO extends PDO
{

    protected $_table_prefix;

    protected $_table_suffix;

    public function __construct($dsn, $user = null, $password = null, $driver_options = array(), $prefix = null, $suffix = null)
    {
        $this->_table_prefix = $prefix;
        $this->_table_suffix = $suffix;
        parent::__construct($dsn, $user, $password, $driver_options);
    }

    public function exec($statement)
    {
        $statement = $this->_tablePrefixSuffix($statement);
        return parent::exec($statement);
    }

    public function prepare($statement, $driver_options = array())
    {
        $statement = $this->_tablePrefixSuffix($statement);
        return parent::prepare($statement, $driver_options);
    }

    public function query($statement)
    {
        $statement = $this->_tablePrefixSuffix($statement);
        $args = func_get_args();

        if (count($args) > 1) {
            return call_user_func_array(array(
                $this,
                'parent::query'
            ), $args);
        } else {
            return parent::query($statement);
        }
    }

    protected function _tablePrefixSuffix($statement)
    {
        return sprintf($statement, $this->_table_prefix, $this->_table_suffix);
    }
}