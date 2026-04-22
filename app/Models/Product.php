<?php

namespace App\Models;

class Product
{
    public $id;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $image_path;
    public $created_at;

    private static $db;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function all()
    {
        return self::query("SELECT * FROM products");
    }

    public static function find($id)
    {
        $result = self::query("SELECT * FROM products WHERE id = ?", [$id]);
        return $result[0] ?? null;
    }

    public static function create($data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO products ($columns) VALUES ($placeholders)";
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
            $query = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = ?";
            $values = array_values((array)$this);
            array_pop($values);
            $values[] = $this->id;
            self::execute($query, $values);
        }
    }

    public function delete()
    {
        self::execute("DELETE FROM products WHERE id = ?", [$this->id]);
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
