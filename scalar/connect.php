<?php

class Db {

    protected static $connection;

    public function connect() {
        if (!isset(self::$connection)) {
            $config = parse_ini_file('./config/config.ini', 'db_connection');
            self::$connection = new mysqli('localhost'
                    , $config['db_connection']['username']
                    , $config['db_connection']['password']
                    , $config['db_connection']['dbname']);
        }
        if (self::$connection === false) {
            echo 'false';
            return false;
        }

        return self::$connection;
    }

    public function select($query) {
        $rows = array();
        $result = $this->query($query);
        if ($result === false) {
            return false;
        }
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function query($query) {
        $connection = $this->connect();
        $result = $connection->query($query);
        return $result;
    }

}
