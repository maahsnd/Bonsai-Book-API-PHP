<?php
class Database
{
    private $db;

    public function __construct()
    {
        $this->db = new PDO('sqlite:bonsai_book.db');
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getConnection()
    {
        return $this->db;
    }
}
