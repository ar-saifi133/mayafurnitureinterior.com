<?php

class dbClass
{
    // Local Settings (XAMPP)
    // private $host = 'localhost';
    // private $dbname = 'maya_furniture_db';
    // private $user = 'root';
    // private $pass = '';

    // Live Settings
    private $host = 'localhost';
    private $dbname = 'mayafurniture_db';
    private $user = 'mayafurniture_db';
    private $pass = 'eUvcYXGvs67QfNgYev2V';

    public $conn;

    public function __construct()
    {
        $this->connect();
    }

    private function connect()
    {
        try {
            // Enable mysqli exceptions
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            $this->conn = new mysqli(
                $this->host,
                $this->user,
                $this->pass,
                $this->dbname
            );

            if ($this->conn->connect_error) {
                throw new Exception($this->conn->connect_error);
            }
        } catch (Throwable $e) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Connection Failed: " . $e->getMessage()]);
                exit;
            }
            die("Connection Failed: " . $e->getMessage());
        }
    }

    // 🔹 Helper for prepared query
    private function queryWithParams($query, $params = array())
    {
        if (empty($params)) {
            return $this->conn->query($query);
        }

        $newParams = array(); // ✅ FIX: define variable

        // Named to positional conversion
        if (!empty($params) && is_assoc($params)) {
            foreach ($params as $key => $value) {
                $namedKey = (strpos($key, ':') === 0) ? $key : ':' . $key;

                if (strpos($query, $namedKey) !== false) {
                    $query = str_replace($namedKey, '?', $query);
                    $newParams[] = $value;
                }
            }
            $params = $newParams;
        }

        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Prepare Error: " . $this->conn->error]);
                exit;
            }
            die("Prepare Error: " . $this->conn->error . " Query: " . $query);
        }

        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $bind_params = array(&$types);
            foreach ($params as $k => $v) {
                $bind_params[] = &$params[$k];
            }
            call_user_func_array(array($stmt, 'bind_param'), $bind_params);
        }

        $stmt->execute();

        $result = $stmt->get_result();
        return $result ? $result : $stmt;
    }

    // 🔹 Single row
    public function getData($query, $params = array())
    {
        $result = $this->queryWithParams($query, $params);
        if ($result instanceof mysqli_result) {
            return $result->fetch_assoc();
        }
        return false;
    }

    // 🔹 Count rows
    public function getRowCount($query, $params = array())
    {
        $result = $this->queryWithParams($query, $params);
        if ($result instanceof mysqli_result) {
            return $result->num_rows;
        } elseif ($result instanceof mysqli_stmt) {
            return $result->affected_rows;
        }
        return 0;
    }

    // 🔹 All rows
    public function getAllData($query, $params = array())
    {
        $result = $this->queryWithParams($query, $params);
        if ($result instanceof mysqli_result) {
            return $result->fetch_all(MYSQLI_ASSOC);
        }
        return array();
    }

    // 🔹 Insert
    public function insertData($query, $params = array())
    {
        $result = $this->queryWithParams($query, $params);
        if ($result) {
            return $this->conn->insert_id;
        } else {
            die("Insert Error: " . $this->conn->error);
        }
    }

    // 🔹 Execute
    public function executeStatement($query, $params = array())
    {
        return $this->queryWithParams($query, $params);
    }
}

// 🔹 Check associative array
if (!function_exists('is_assoc')) {
    function is_assoc($arr)
    {
        if (array() === $arr)
            return false;
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}

// Time
date_default_timezone_set("Asia/Kolkata");
$dateTime = date('Y-m-d H:i:s');