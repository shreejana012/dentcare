<?php
// notification_ui.php - UI components for the notification system

/**
 * Generate notification dropdown for navbar
 * @param array $notifications - Array of notification objects
 * @param int $unread_count - Number of unread notifications
 * @return string - HTML for notification dropdown
 */
function generateNotificationDropdown($notifications, $unread_count) {
    $html = '
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" 
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-bell"></i>
            ' . ($unread_count > 0 ? '<span class="badge badge-danger">' . $unread_count . '</span>' : '') . '
        </a>
        <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationDropdown">
            <div class="dropdown-header d-flex justify-content-between align-items-center">
                <span>Notifications</span>
                ' . ($unread_count > 0 ? '<a href="mark_all_read.php" class="text-primary">Mark all as read</a>' : '') . '
            </div>
            <div class="notification-list">';
    
    if (count($notifications) > 0) {
        foreach ($notifications as $notification) {
            $html .= '
            <a class="dropdown-item notification-item ' . ($notification['is_read'] == 0 ? 'unread' : '') . '" 
               href="view_notification.php?id=' . $notification['id'] . '">
                <div class="notification-icon">
                    ' . getNotificationIcon($notification['type']) . '
                </div>
                <div class="notification-content">
                    <p class="mb-1">' . htmlspecialchars($notification['message']) . '</p>
                    <small class="text-muted">' . timeElapsed($notification['created_at']) . '</small>
                </div>
            </a>';
        }
    } else {
        $html .= '<div class="dropdown-item text-center">No notifications</div>';
    }
    
    $html .= '
            </div>
            <div class="dropdown-footer text-center">
                <a href="all_notifications.php">View All</a>
            </div>
        </div>
    </li>';
    
    return $html;
}

/**
 * Get appropriate icon for notification type
 * @param string $type - Type of notification
 * @return string - HTML for icon
 */
function getNotificationIcon($type) {
    switch ($type) {
        case 'appointment':
            return '<i class="fas fa-calendar-check text-primary"></i>';
        case 'reminder':
            return '<i class="fas fa-clock text-warning"></i>';
        case 'message':
            return '<i class="fas fa-envelope text-info"></i>';
        case 'system':
            return '<i class="fas fa-cog text-secondary"></i>';
        default:
            return '<i class="fas fa-bell text-primary"></i>';
    }
}

/**
 * Calculate time elapsed from created_at date
 * @param string $datetime - MySQL datetime string
 * @return string - Formatted time elapsed (e.g., "2 hours ago")
 */
function timeElapsed($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    
    if ($diff->y > 0) {
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    } elseif ($diff->m > 0) {
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    } elseif ($diff->d > 0) {
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    } elseif ($diff->i > 0) {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    } else {
        return 'Just now';
    }
}