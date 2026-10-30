<?php
/**
 * Helper Autentikasi Pengguna
 * LOGIX PRO
 */

require_once __DIR__ . '/database.php';

function checkAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php?msg=need_login');
        exit;
    }
}

function currentUser() {
    if (isset($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'nama_lengkap' => $_SESSION['nama_lengkap'] ?? 'User',
            'username' => $_SESSION['username'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'role' => $_SESSION['role'] ?? 'operator',
        ];
    }
    return null;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
