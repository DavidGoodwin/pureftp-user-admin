<?php

namespace PureFTPAdmin;


class Database
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run an update/insert/delete on the db; returns row count.
     * @param string $sql
     * @param array $args for prepared statement placeholders
     * @return int
     */
    public function update($sql, $args = [])
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->rowCount();
    }

    /**
     * Run a select query on the DB.
     * @param string $sql
     * @param array $args
     * @return array - assoc array of results
     */
    public function select($sql, $args)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Run a select query on the DB.
     * @param string $sql
     * @param array $args
     * @return array|false - assoc array - single row from the database.
     */
    public function selectOne($sql, $args)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }


    /**
     * Get the last autoincrement value.
     * @return string
     */
    public function getLastInsertId()
    {
        return $this->pdo->lastInsertId();
    }

}
