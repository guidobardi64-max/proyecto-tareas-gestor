<?php
// Funciones auxiliares para el portafolio

function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

function truncateText($text, $limit = 100) {
    if (strlen($text) > $limit) {
        return substr($text, 0, $limit) . '...';
    }
    return $text;
}

function getActiveClass($page, $current) {
    return $page === $current ? 'active' : '';
}

function uploadImage($file, $targetDir = '../uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed)) {
        return false;
    }
    
    $newFilename = uniqid() . '.' . $ext;
    $targetPath = $targetDir . $newFilename;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $newFilename;
    }
    
    return false;
}

function sendEmail($to, $subject, $message) {
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: no-reply@guidobardi.com" . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function getSkillLevelColor($level) {
    if ($level >= 80) return 'success';
    if ($level >= 60) return 'info';
    if ($level >= 40) return 'warning';
    return 'danger';
}
?>