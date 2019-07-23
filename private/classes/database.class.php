<?php
require_once dirname( __DIR__ ) . '/db_data.php';

class Database
{
    public function __construct()
    {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);

        if(mysqli_connect_errno())
        {
            echo "Error: Could not connect to database.";
            exit;
        }
    }

    private function confirm_db_connect($db)
    {
        if ($db->connect_errno) {
            exit("Database connection failed: " . $db->connect_error . " (" . $db->connect_errno . ")");
        }
    }
}
?>