<?php
class Registration
{
    private $db; // this will be PDO connection

    public function __construct($dbClass)
    {
        // assign the PDO connection to $this->db
        $this->db = $dbClass->conn;
    }

    public function saveRegistration($data, $files)
    {
        try {
            $query = "INSERT INTO sulemankhateeb
                (name, email, subject, message)
            VALUES
                (:name, :email, :subject, :message)";

            $params = [
                ':name' => trim($data['name'] ?? ''),
                ':email' => trim($data['email'] ?? ''),
                ':subject' => trim($data['subject'] ?? ''),
                ':message' => trim($data['message'] ?? '')
            ];

            // ensure db is not null
            if (!$this->db) {
                return ['success' => false, 'message' => 'Database connection not initialized'];
            }

            $stmt = $this->db->prepare($query);
            if ($stmt->execute($params)) {
                return ['success' => true, 'message' => 'Registration successful!'];
            }

            // if execution fails
            $err = $stmt->errorInfo();
            return ['success' => false, 'message' => 'SQL error: ' . implode(' | ', $err)];

        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}

?>
