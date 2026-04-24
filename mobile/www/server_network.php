<?php
/**
 * Servidor de Red - Acepta conexiones de todos los dispositivos
 * 
 * Este servidor sirve:
 * - API REST en /api/v1/*
 * - Frontend HTML en las rutas raíz
 * - Accesible desde cualquier dispositivo en la red local
 */

$request_uri = $_SERVER['REQUEST_URI'];
$request_uri = strtok($request_uri, '?');

// Servir archivos estáticos
if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$/', $request_uri)) {
    return false;
}

// API Routes
if (strpos($request_uri, '/api/') === 0) {
    include 'api_auth.php';
    exit;
}

// Ruta principal - Login
if ($request_uri === '/' || $request_uri === '/index.html') {
    include 'login.html';
    exit;
}

// Login
if ($request_uri === '/login' || $request_uri === '/login.html') {
    include 'login.html';
    exit;
}

// Dashboard de usuario normal
if ($request_uri === '/user_dashboard' || $request_uri === '/user_dashboard.html') {
    include 'user_dashboard.html';
    exit;
}

// Dashboard de administrador
if ($request_uri === '/admin_dashboard' || $request_uri === '/admin_dashboard.html') {
    include 'admin_dashboard.html';
    exit;
}

// Aplicación web de nutrición
if ($request_uri === '/app' || $request_uri === '/app.html') {
    include 'app.html';
    exit;
}

// Para cualquier otra ruta, servir login
include 'login.html';
?>
