<?php

namespace App\Models;

class CartItem
{
    public $id;
    public $user_id;
    public $product_id;
    public $quantity;
    public $created_at;

    private static $db;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function find($id)
    {
        $result = self::query("SELECT * FROM cart_items WHERE id = ?", [$id]);
        return $result[0] ?? null;
    }

    public static function where($column, $value = null)
    {
        if ($value === null && is_array($column)) {
            $wheres = [];
            $values = [];
            foreach ($column as $key => $val) {
                $wheres[] = "$key = ?";
                $values[] = $val;
            }
            $query = "SELECT * FROM cart_items WHERE " . implode(' AND ', $wheres);
            return self::query($query, $values);
        }
        
        // Handle parameterized usage: where('column', '=', 'value') or where('column', 'value')
        if (is_null($value) && !is_array($column)) {
            return [];
        }
        
        $query = "SELECT * FROM cart_items WHERE $column = ?";
        return self::query($query, [$value]);
    }

    public static function create($data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO cart_items ($columns) VALUES ($placeholders)";
        self::execute($query, array_values($data));
        return new self($data);
    }

    public function delete()
    {
        self::execute("DELETE FROM cart_items WHERE id = ?", [$this->id]);
    }

    public function update($data)
    {
        $updates = [];
        $values = [];
        foreach ($data as $key => $value) {
            $updates[] = "$key = ?";
            $values[] = $value;
        }
        $values[] = $this->id;
        $query = "UPDATE cart_items SET " . implode(', ', $updates) . " WHERE id = ?";
        self::execute($query, $values);
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
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
