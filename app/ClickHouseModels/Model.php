<?php

namespace App\ClickHouseModels;

use App\ClickHouseModels\ClickHouseClient as Client;
use ClickHouseDB\Statement;
use Illuminate\Support\Facades\Log;

class Model
{
    protected string $where = '';
    protected string $select = '';
    protected string $from = '';
    protected string $orderBy = '';
    protected Statement $result;
    public string $table;

    public function __construct()
    {
        $this->from = "FROM " . $this->getTableName();
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function __call($name, $arguments) {
        $method = "{$name}NotStatic";
        return $this->$method(...$arguments);
    }

    public static function __callStatic($name, $arguments) {
        $vkBanner = new self;
        $method = "{$name}Static";
        return $vkBanner->$method(...$arguments);
    }

    public function insertNotStatic(array $data): bool
    {
        $client = new Client();
        $data = array_map(function ($item) {
            if (is_int($item)) {
                return $item;
            } else {
                return "'{$item}'";
            }
        }, $data);
        $table = $this->getTableName();
        $query = "INSERT INTO {$table} VALUES (".implode(',', array_values($data)).")";
        return !$client->write($query)->isError();
    }

    public function updateNotStatic( $update): bool
    {
        $client = new Client();

        $update = self::addQuotes($update);

        foreach ($update as $key => $value) {
            $update[$key] = "{$key} = {$value}";
        }

        $table = $this->getTableName();

        $query = "ALTER TABLE {$table} UPDATE ".implode(',', $update)." ". $this->where;

        $result = $client->write($query);
        return !$result->isError();
    }

    public function selectStatic($row): self
    {
        $vkBanner = new self;
        $vkBanner->select = 'SELECT '.$row;
        return $vkBanner;
    }

    public function selectNotStatic($row): self
    {
        if(!empty($this->select)) {
            $this->select .= ', '.$row;
        }else{
            $this->select = 'SELECT '.$row;
        }
        return $this;
    }

    public function whereStatic($key, $value, $operator = '='): self
    {
        $where = "{$key} {$operator} {$value}";
        $vkBanner = new self;
        $vkBanner->where = $where;
        return $vkBanner;
    }

    public function whereNotStatic($key, $value, $operator = '='): self
    {
        $where = "{$key} {$operator} {$value}";
        if(!empty($this->where)) {
            $this->where .= ' AND '.$where;
        }else{
            $this->where = 'WHERE '. $where;
        }
        return $this;
    }

    public function orderByStatic($row): self
    {
        $vkBanner = new self;
        $vkBanner->orderBy = 'ORDER BY '.$row;
        return $vkBanner;
    }

    public function orderByNotStatic($row): self
    {
        if(!empty($this->orderBy)) {
            $this->orderBy .= ', '.$row;
        }
        $this->orderBy = 'ORDER BY '.$row;
        return $this;
    }

    public function getNotStatic(): self
    {
        $client = new Client();

        $query = "{$this->select} {$this->from} {$this->where} {$this->orderBy}";

        $this->result = $client->select($query);
        return $this;
    }

    public function toArrayNotStatic(): array
    {
        return $this->result->rows();
    }

    public static function addQuotes($array)
    {
        return array_map(function ($item) {
            if (is_int($item)) {
                return $item;
            } else {
                return "'{$item}'";
            }
        }, $array);
    }
}
