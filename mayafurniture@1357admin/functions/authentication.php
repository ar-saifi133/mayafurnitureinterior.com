<?php
class AdminManager
{
    private $conn;
private $uploadDir = __DIR__ . '/../images/';


    public function __construct()
    {
        $this->conn = new dbClass();
    }

    public function adminLogin($email, $pass)
    {
        $result = $this->conn->getData("SELECT * FROM `admin` WHERE `email` = :email", array(':email' => $email));

        if ($result && password_verify($pass, $result['password'])) {

            $query = "UPDATE `admin` SET `login_ip` = :login_ip, `login_date` = NOW() WHERE `id` = :id";
            $this->conn->executeStatement($query, array(':login_ip' => $_SERVER['REMOTE_ADDR'], ':id' => $result['id']));

            $_SESSION['ADMIN_USER_ID'] = $result['id'];
            
            return true;
        }
        return false;
    }

    public function checkSession()
    {
        if (!isset($_SESSION['ADMIN_USER_ID']) || empty($_SESSION['ADMIN_USER_ID'])) {
            header('Location: index.php');
            exit();
        }
    }

    public function signOut()
    {
        unset($_SESSION['ADMIN_USER_ID']);
        session_destroy();
        header('Location: index.php');
        exit();
    }

    public function getAdminHeaderInfo($adminId)
    {
        if (!$adminId) {
            return null;
        }
        return $this->conn->getData("SELECT username, image FROM admin WHERE id = :id", array(':id' => $adminId));
    }

    public function getAdminDetails($adminId)
    {
        if (!$adminId) {
            return false;
        }
        return $this->conn->getData("SELECT * FROM `admin` WHERE `id` = :id", array(':id' => $adminId));
    }

    public function updateProfileDetails($adminId, $data, $file)
    {
        $username = trim($data['username']);
        $phone = trim($data['phone']);
        $email = filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL);
        $oldImageFilename = isset($data['oldimage']) ? $data['oldimage'] : null;
        $newImageFilename = $oldImageFilename;

        if (empty($username) || empty($phone) || empty($email)) {
            return array('success' => false, 'message' => 'All fields are required.');
        }

        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            if (!empty($oldImageFilename)) {
               $oldImageFullPath = $this->uploadDir . $oldImageFilename;

                if (file_exists($oldImageFullPath)) {
                    @unlink($oldImageFullPath);
                }
            }

            $uploadResult = uploadFileHandler($file, null, $this->uploadDir);

            if (is_string($uploadResult) && strpos($uploadResult, 'ERROR:') === 0) {
                return array('success' => false, 'message' => $uploadResult);
            }
            $newImageFilename = $uploadResult;
        }

        $sql = "UPDATE admin SET username = :username, phone = :phone, email = :email, image = :image WHERE id = :id";
        $params = array(
            ':username' => $username,
            ':phone' => $phone,
            ':email' => $email,
            ':image' => $newImageFilename,
            ':id' => $adminId
        );

        if ($this->conn->executeStatement($sql, $params)) {
            return array(
                'success' => true,
                'message' => 'Profile updated successfully!',
                'newData' => array(
                    'username' => $username,
                    'email' => $email,
                    'image' => $newImageFilename
                )
            );
        }

        return array('success' => false, 'message' => 'Failed to update profile.');
    }




    public function changePassword($adminId, $currentPassword, $newPassword)
    {
        if (empty($currentPassword) || empty($newPassword)) {
            return array('success' => false, 'message' => 'All password fields are required.');
        }
        $admin = $this->getAdminDetails($adminId);
        if (!$admin) {
            return array('success' => false, 'message' => 'Could not find user.');
        }

        if (!password_verify($currentPassword, $admin['password'])) {
            return array('success' => false, 'message' => 'Your current password is incorrect.');
        }

        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE admin SET password = :password WHERE id = :id";
        if ($this->conn->executeStatement($sql, array(':password' => $newHashedPassword, ':id' => $adminId))) {
            return array('success' => true, 'message' => 'Password changed successfully!');
        }
        return array('success' => false, 'message' => 'An error occurred while changing the password.');
    }
}

?>