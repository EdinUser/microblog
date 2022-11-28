<?php

namespace MicroBlog\Utils;

use PDO;
use PDOStatement;

class Db
{
    /**
     * @var PDO
     */
    private PDO $db;

    public function __construct()
    {
        $this->db = new PDO('mysql:dbname=' . $_ENV['DB_DB'] . ';host=' . $_ENV['DB_SERVER'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);
    }

    /**
     * Return the PDO object to work
     *
     * @return PDO
     */
    function getDb(): PDO
    {
        return $this->db;
    }

    /**
     * @param string $select Name of columns to return
     * @param string $table  Name of the table to read from
     * @param array  $where  Array with limiters
     *
     */
    function sql_query_table(string $select = '', string $table = '', array $where = array(), $returnType = 'multiple'): bool|array
    {
        //full table read, no limiters
        if (empty($where)) {
            $buildQuery = $this->db
              ->query(
                "
                    SELECT
                        {$select}
                    FROM
                        {$table}
                  "
              );

            return $this->return_result($buildQuery, $returnType);
        }

        $simpleQuery = "
                    SELECT
                        {$select}
                    FROM
                        {$table}
                  ";

        $whereValues = $whereString = array();
        foreach ($where as $key => $item) {
            $whereValues[':' . $key] = $item;
            $whereString[$key] = $key . " = :" . $key;
        }

        $simpleQuery .= ' WHERE ' . implode(" AND ", $whereString);

        $buildQuery = $this->db
          ->prepare($simpleQuery);
        $buildQuery
          ->execute(
            $whereValues
          );

        return $this->return_result($buildQuery, $returnType);
    }

    /**
     * Return the result - either multiple results or single row
     *
     * @param $fetchResult
     * @param $returnType
     *
     * @return array|bool
     */
    private function return_result($fetchResult, $returnType): bool|array
    {
        if ($returnType === 'single') {
            return $this->sql_fetch_row($fetchResult);
        }

        return $this->sql_fetch_obj($fetchResult);
    }

    /**
     * Read all results, return them as an object
     *
     * @param PDOStatement $dbQuery
     *
     * @return array|false
     */
    function sql_fetch_obj(PDOStatement $dbQuery): array|bool
    {
        return $dbQuery->fetchAll(PDO::FETCH_OBJ);
    }

    /**
     * Read single row
     *
     * @param PDOStatement $dbQuery
     *
     * @return array|false
     */
    function sql_fetch_row(PDOStatement $dbQuery): array|bool
    {
        return $dbQuery->fetch(PDO::FETCH_BOTH);
    }
}