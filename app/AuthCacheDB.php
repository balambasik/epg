<?php

namespace App;

use App\Interfaces\CacheDriverInterface;
use App\Exceptions\CacheDriverException;

class AuthCacheDB implements CacheDriverInterface
{
    private $db;
    private $table;

    public function __construct()
    {
        $config      = config()[__CLASS__];
        $this->table = $config["tb_name"];

        try {
            $this->db = new \PDO("mysql:host={$config["db_host"]};dbname={$config["db_name"]}", $config["db_user"], $config["db_pass"]);
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $this->createTableIfNotExists();
            $this->clearExpiredTokens();

        } catch (\PDOException $e) {
            throw new CacheDriverException($e->getMessage());
        }
    }

    /**
     * @param $token
     * @param $ttl
     */
    public function setToken($token, $ttl)
    {
        $query = $this->db->prepare("INSERT INTO {$this->table} (token, expire) VALUES (:token, :expire)");
        $query->bindValue(":token", self::prepKey($token), \PDO::PARAM_STR);
        $query->bindValue(":expire", date("Y-m-d H:i:s", $ttl), \PDO::PARAM_STR);
        $query->execute();
    }

    /**
     * @param $token
     * @return bool
     */
    public function checkToken($token)
    {
        $query = $this->db->prepare("SELECT * from {$this->table} WHERE token = :token LIMIT 1");
        $query->bindValue(":token", self::prepKey($token), \PDO::PARAM_STR);
        $query->execute();

        if ($rows = $query->fetchAll(\PDO::FETCH_ASSOC)) {
            return strtotime($rows[0]["expire"]) > time();
        }

        return false;
    }

    /**
     * @param $token
     */
    public function destroyToken($token)
    {
        $query = $this->db->prepare("DELETE FROM {$this->table} WHERE token = :token");
        $query->bindValue(":token", self::prepKey($token), \PDO::PARAM_STR);
        $query->execute();
    }

    /**
     * Delete all expired tokens
     */
    public function clearExpiredTokens()
    {
        if (config()["clear_expired_tokens_chance"] < rand(1, 100)) {
            return;
        }

        $this->db->exec("DELETE FROM {$this->table} WHERE expire < NOW()");
    }


    private function createTableIfNotExists()
    {
        $this->db->exec("CREATE TABLE IF NOT EXISTS `{$this->table}` (`token` varchar(255), `expire` timestamp)");
    }

    /**
     * @param $key
     * @return string
     */
    private static function prepKey($key)
    {
        return md5($key);
    }
}