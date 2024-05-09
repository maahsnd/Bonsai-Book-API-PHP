<?php
class Database
{
    private $db;

    public function __construct()
    {
        // Set the absolute path to the database file 
        $path = __DIR__ . '/../database/bonsai_book.db';
        $this->db = new PDO('sqlite:' . $path);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection()
    {
        return $this->db;
    }
}
