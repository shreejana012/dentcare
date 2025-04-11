<?php
// notifications.php - Handles all notification functionality

class NotificationSystem {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Create a new notification
     * @param int $user_id - User ID to receive notification
     * @param string $type - Type of notification (appointment, reminder, etc)
     * @param string $message - Notification message
     * @param int $reference_id - ID of related entity (appointment ID, etc)
     * @return bool - Success or failure
     */
    public function createNotification($user_id, $user_type, $type, $message, $reference_id = null) {
        $query = "INSERT INTO notifications (user_id, user_type, type, message, reference_id, created_at, is_read) 
                 VALUES (?, ?, ?, ?, ?, NOW(), 0)";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("isssi", $user_id, $user_type, $type, $message, $reference_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Get unread notifications for a user
     * @param int $user_id - User ID
     * @param string $user_type - Type of user (patient, doctor, admin)
     * @return array - Array of notification objects
     */
    public function getUnreadNotifications($user_id, $user_type) {
        // Check if notifications table exists
        if (!$this->tableExists('notifications')) {
            return [];
        }
        
        $query = "SELECT * FROM notifications 
                 WHERE user_id = ? AND user_type = ? AND is_read = 0 
                 ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->conn->error);
            return [];
        }
        
        $stmt->bind_param("is", $user_id, $user_type);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        $stmt->close();
        return $notifications;
    }
    
    /**
     * Get all notifications for a user
     * @param int $user_id - User ID
     * @param string $user_type - Type of user (patient, doctor, admin)
     * @param int $limit - Maximum number of notifications to return
     * @return array - Array of notification objects
     */
    public function getAllNotifications($user_id, $user_type, $limit = 10) {
        // Check if notifications table exists
        if (!$this->tableExists('notifications')) {
            return [];
        }
        
        $query = "SELECT * FROM notifications 
                 WHERE user_id = ? AND user_type = ? 
                 ORDER BY created_at DESC 
                 LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->conn->error);
            return [];
        }
        
        $stmt->bind_param("isi", $user_id, $user_type, $limit);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $notifications = [];
        
        while ($row = $result->fetch_assoc()) {
            $notifications[] = $row;
        }
        
        $stmt->close();
        return $notifications;
    }
    
    /**
     * Mark a notification as read
     * @param int $notification_id - ID of the notification
     * @return bool - Success or failure
     */
    public function markAsRead($notification_id) {
        // Check if notifications table exists
        if (!$this->tableExists('notifications')) {
            return false;
        }
        
        $query = "UPDATE notifications SET is_read = 1 WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("i", $notification_id);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Mark all notifications as read for a user
     * @param int $user_id - User ID
     * @param string $user_type - Type of user (patient, doctor, admin)
     * @return bool - Success or failure
     */
    public function markAllAsRead($user_id, $user_type) {
        // Check if notifications table exists
        if (!$this->tableExists('notifications')) {
            return false;
        }
        
        $query = "UPDATE notifications SET is_read = 1 
                 WHERE user_id = ? AND user_type = ? AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->conn->error);
            return false;
        }
        
        $stmt->bind_param("is", $user_id, $user_type);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Count unread notifications for a user
     * @param int $user_id - User ID
     * @param string $user_type - Type of user (patient, doctor, admin)
     * @return int - Number of unread notifications
     */
    public function countUnread($user_id, $user_type) {
        // Check if notifications table exists
        if (!$this->tableExists('notifications')) {
            return 0;
        }
        
        $query = "SELECT COUNT(*) as count FROM notifications 
                 WHERE user_id = ? AND user_type = ? AND is_read = 0";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Error preparing statement: " . $this->conn->error);
            return 0;
        }
        
        $stmt->bind_param("is", $user_id, $user_type);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row['count'];
    }
    
    /**
     * Check if a table exists in the database
     * @param string $table_name - Name of the table to check
     * @return bool - Whether the table exists
     */
    private function tableExists($table_name) {
        $query = "SHOW TABLES LIKE '{$table_name}'";
        $result = $this->conn->query($query);
        
        if (!$result) {
            error_log("Error checking if table exists: " . $this->conn->error);
            return false;
        }
        
        return $result->num_rows > 0;
    }
}