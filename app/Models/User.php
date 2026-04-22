<?php

namespace App\Models;

class User
{
    public $id;
    public $username;
    public $email;
    public $password;
    public $is_admin;
    public $created_at;

    // Simulated database
    private static $db;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function all()
    {
        return self::query("SELECT * FROM users");
    }

    public static function find($id)
    {
        $result = self::query("SELECT * FROM users WHERE id = ?", [$id]);
        return $result[0] ?? null;
    }

    public static function where($column, $operator, $value = null)
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }
        return self::query("SELECT * FROM users WHERE $column $operator ?", [$value]);
    }

    public static function create($data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO users ($columns) VALUES ($placeholders)";
        self::execute($query, array_values($data));
        return new self($data);
    }

    public function save()
    {
        if ($this->id) {
            $updates = [];
            foreach (get_object_vars($this) as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = ?";
                }
            }
            $query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
            $values = array_values((array)$this);
            array_pop($values);
            $values[] = $this->id;
            self::execute($query, $values);
        } else {
            return self::create((array)$this);
        }
    }

    public static function query($query, $params = [])
    {
        $db = self::getDB();
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        $results = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = new self($row);
        }
        return $results;
    }

    public static function execute($query, $params = [])
    {
        $db = self::getDB();
        $stmt = $db->prepare($query);
        return $stmt->execute($params);
    }

    private static function getDB()
    {
        if (!self::$db) {
            $dbPath = __DIR__ . '/../../storage/database/coffee_haven.db';
            self::$db = new \PDO('sqlite:' . $dbPath);
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return self::$db;
    }
}
