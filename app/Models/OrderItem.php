<?php

namespace App\Models;

class OrderItem
{
    public $id;
    public $order_id;
    public $product_id;
    public $quantity;
    public $unit_price;

    private static $db;

    public function __construct($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function where($column, $value = null)
    {
        $query = "SELECT * FROM order_items WHERE $column = ?";
        return self::query($query, [$value]);
    }

    public static function create($data)
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO order_items ($columns) VALUES ($placeholders)";
        self::execute($query, array_values($data));
        return new self($data);
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
