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

        $whereDetails = $this->buildQueryFromArray($where);

        $simpleQuery .= ' WHERE ' . implode(" AND ", $whereDetails['string']);

        $buildQuery = $this->db
          ->prepare($simpleQuery);
        $buildQuery
          ->execute(
            $whereDetails['array']
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
        return $dbQuery->fetch(PDO::FETCH_GROUP|PDO::FETCH_ASSOC);
    }


    /**
     * Insert or Update into a table
     *
     * @param string $table
     * @param array  $where
     * @param array  $dataArray
     * @param string $type
     *
     * @return int
     */
    function sql_upsert(string $table = '', array $where = array(), array $dataArray = array(), string $type = 'INSERT'): int
    {
        $sql = '';
        $dataDetails = $this->buildQueryFromArray($dataArray);
        if (!empty($where)) {
            $whereDetails = $this->buildQueryFromArray($where);
        }

        switch ($type) {
            case 'INSERT':
                $sql = "
                INSERT INTO
                `{$table}`
                    (`" . implode("`, `", array_keys($dataDetails['string'])) . "`)
                VALUES
                    (" . implode(", ", array_keys($dataDetails['array'])) . ")
                ";
                break;

            case 'UPDATE':
                $sql = "
                UPDATE
                `{$table}`
                SET
                    " . implode(",\n", $dataDetails['string']) . "
                WHERE
                    " . implode(",", $whereDetails['string']) . "
                ";
                break;
        }

        $upsertQuery = $this->db->prepare($sql);
        $upsertQuery->execute($dataDetails['array']);

        $stmt = $this->db->query("SELECT LAST_INSERT_ID()");
        $recordId = $stmt->fetchColumn();

        return $recordId ?? 0;

    }

    /**
     * Process array to :key=>value for easy op PDO usage
     *
     * @param array $arrayToProcess
     *
     * @return array[]
     */
    private function buildQueryFromArray(array $arrayToProcess): array
    {
        $returnValues = $returnString = array();
        foreach ($arrayToProcess as $key => $item) {
            $returnValues[':' . $key] = $item;
            $returnString[$key] = '`' . $key . "` = :" . $key;
        }

        return array(
          'string' => $returnString,
          'array'  => $returnValues,
        );

    }
}