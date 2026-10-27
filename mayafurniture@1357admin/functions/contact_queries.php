<?php

class ContactQueries
{
    private $db;

    public function __construct()
    {
        $this->db = new dbClass();
    }

    public function getAllInquiries()
    {
        $query = "SELECT * FROM contact_queries ORDER BY id DESC";
        return $this->db->getAllData($query);
    }

    /**
     * Delete inquiries by ID(s)
     */
    public function deleteInquiry($ids)
    {
        if (empty($ids)) {
            return array('success' => false, 'message' => 'Invalid ID(s) provided.');
        }

        // Normalize to array
        if (!is_array($ids)) {
            $ids = array($ids);
        }

        // Filter valid numeric IDs
        $clean_ids = array_filter($ids, function ($id) {
            return is_numeric($id);
        });

        if (empty($clean_ids)) {
            return array('success' => false, 'message' => 'No valid IDs provided.');
        }

        // Prepare placeholders
        $placeholders = implode(',', array_fill(0, count($clean_ids), '?'));

        // Delete query
        $deleteQuery = "DELETE FROM contact_queries WHERE id IN ($placeholders)";

        if ($this->db->executeStatement($deleteQuery, array_values($clean_ids))) {
            return array('success' => true, 'message' => 'Inquiry(s) deleted successfully!');
        }

        return array('success' => false, 'message' => 'Failed to delete inquiry(s).');
    }
}
?>
