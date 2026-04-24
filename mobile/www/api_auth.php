<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database configuration
$db_file = __DIR__ . '/auth_database.sqlite';

// Database connection
try {
    $pdo = new PDO("sqlite:$db_file");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    initializeProductTable($pdo);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $e->getMessage()
    ]);
    exit;
}

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path_parts = explode('/', trim($path, '/'));

// API v1 routes
if (count($path_parts) >= 2 && $path_parts[0] == 'api' && $path_parts[1] == 'v1') {
    if (count($path_parts) >= 3) {
        switch ($path_parts[2]) {
            case 'users':
                handleUsersRequest($method, $path_parts, $pdo);
                break;
            case 'auth':
                handleAuthRequest($method, $path_parts, $pdo);
                break;
            case 'products':
                handleProductsRequest($method, $path_parts, $pdo);
                break;
            default:
                sendJsonResponse(['success' => false, 'message' => 'Endpoint not found'], 404);
        }
    } else {
        sendJsonResponse(['success' => false, 'message' => 'Invalid API path'], 404);
    }
} else {
    sendJsonResponse(['success' => false, 'message' => 'Invalid API path'], 404);
}

function handleAuthRequest($method, $path_parts, $pdo) {
    $action = isset($path_parts[3]) ? $path_parts[3] : null;
    
    switch ($method) {
        case 'POST':
            if ($action === 'login') {
                login($pdo);
            } elseif ($action === 'register') {
                register($pdo);
            } elseif ($action === 'logout') {
                logout($pdo);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'Auth endpoint not found'], 404);
            }
            break;
        default:
            sendJsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
    }
}

function handleProductsRequest($method, $path_parts, $pdo) {
    if ($method === 'GET' && isset($path_parts[3]) && $path_parts[3] === 'barcode' && isset($path_parts[4])) {
        getProductByBarcode($path_parts[4], $pdo);
        return;
    }

    if ($method === 'POST' && count($path_parts) === 3) {
        createLocalProduct($pdo);
        return;
    }

    sendJsonResponse(['success' => false, 'message' => 'Products endpoint not found'], 404);
}

function getProductByBarcode($barcode, $pdo) {
    $barcode = preg_replace('/\D/', '', (string)$barcode);
    if (strlen($barcode) < 8 || strlen($barcode) > 14) {
        sendJsonResponse(['success' => false, 'message' => 'Invalid barcode format'], 422);
    }

    $localProduct = getLocalProductByBarcode($pdo, $barcode);
    if ($localProduct) {
        sendJsonResponse([
            'success' => true,
            'message' => 'Product found in local database',
            'data' => $localProduct,
            'source' => 'local_db'
        ]);
    }

    $fallbackProducts = [
        '5449000000996' => [
            'id' => null,
            'barcode' => '5449000000996',
            'name' => 'Coca-Cola',
            'brand' => 'Coca-Cola',
            'category' => 'Bebidas',
            'nutriscore' => 'e',
            'image_url' => 'https://images.openfoodfacts.org/images/products/544/900/000/0996/front_es.3.400.jpg',
            'calories_per_100g' => 42,
            'proteins_per_100g' => 0,
            'carbs_per_100g' => 10.6,
            'fats_per_100g' => 0,
        ],
        '8410000036144' => [
            'id' => null,
            'barcode' => '8410000036144',
            'name' => 'Leche Entera',
            'brand' => 'Pascual',
            'category' => 'Lacteos',
            'nutriscore' => 'b',
            'image_url' => null,
            'calories_per_100g' => 63,
            'proteins_per_100g' => 3.1,
            'carbs_per_100g' => 4.7,
            'fats_per_100g' => 3.6,
        ]
    ];

    $urls = [
        "https://world.openfoodfacts.org/api/v2/product/" . urlencode($barcode) . ".json",
        "https://es.openfoodfacts.org/api/v2/product/" . urlencode($barcode) . ".json",
        "https://world.openfoodfacts.org/api/v0/product/" . urlencode($barcode) . ".json"
    ];

    $decoded = null;
    $networkError = false;

    foreach ($urls as $url) {
        [$statusCode, $body, $error] = fetchJsonWithRetry($url);

        if ($error !== null) {
            $networkError = true;
            continue;
        }

        if ($statusCode >= 200 && $statusCode < 300 && is_array($body)) {
            if (($body['status'] ?? 0) === 1 && isset($body['product'])) {
                $decoded = $body;
                break;
            }
        }
    }

    if ($decoded === null) {
        if (isset($fallbackProducts[$barcode])) {
            sendJsonResponse([
                'success' => true,
                'message' => 'Product found (fallback mode)',
                'data' => $fallbackProducts[$barcode],
                'source' => 'fallback'
            ]);
        }

        if ($networkError) {
            sendJsonResponse([
                'success' => false,
                'message' => 'Unable to reach Open Food Facts. Check connection or retry.',
                'error_type' => 'network'
            ], 502);
        }

        sendJsonResponse([
            'success' => false,
            'message' => 'Product not found in Open Food Facts database',
            'error_type' => 'not_found'
        ], 404);
    }

    $p = $decoded['product'];
    $nutriments = $p['nutriments'] ?? [];
    $product = [
        'id' => null,
        'barcode' => $barcode,
        'name' => $p['product_name'] ?? $p['product_name_es'] ?? 'Producto sin nombre',
        'brand' => $p['brands'] ?? null,
        'category' => $p['categories'] ?? null,
        'nutriscore' => $p['nutrition_grades'] ?? null,
        'image_url' => $p['image_front_url'] ?? null,
        'calories_per_100g' => $nutriments['energy-kcal_100g'] ?? null,
        'proteins_per_100g' => $nutriments['proteins_100g'] ?? null,
        'carbs_per_100g' => $nutriments['carbohydrates_100g'] ?? null,
        'fats_per_100g' => $nutriments['fat_100g'] ?? null,
    ];

    sendJsonResponse([
        'success' => true,
        'message' => 'Product found',
        'data' => $product,
        'source' => 'open_food_facts'
    ]);
}

function createLocalProduct($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        sendJsonResponse(['success' => false, 'message' => 'Invalid JSON payload'], 400);
    }

    $barcode = preg_replace('/\D/', '', (string)($input['barcode'] ?? ''));
    $name = trim((string)($input['name'] ?? ''));

    if (strlen($barcode) < 8 || strlen($barcode) > 14) {
        sendJsonResponse(['success' => false, 'message' => 'Invalid barcode format'], 422);
    }
    if ($name === '') {
        sendJsonResponse(['success' => false, 'message' => 'Product name is required'], 422);
    }

    $brand = trim((string)($input['brand'] ?? '')) ?: null;
    $category = trim((string)($input['category'] ?? '')) ?: null;
    $nutriscore = strtolower(trim((string)($input['nutriscore'] ?? ''))) ?: null;
    $imageUrl = trim((string)($input['image_url'] ?? '')) ?: null;
    $calories = normalizeNumeric($input['calories_per_100g'] ?? null);
    $proteins = normalizeNumeric($input['proteins_per_100g'] ?? null);
    $carbs = normalizeNumeric($input['carbs_per_100g'] ?? null);
    $fats = normalizeNumeric($input['fats_per_100g'] ?? null);
    $now = date('Y-m-d H:i:s');

    try {
        $stmt = $pdo->prepare("
            INSERT INTO local_products 
                (barcode, name, brand, category, nutriscore, image_url, calories_per_100g, proteins_per_100g, carbs_per_100g, fats_per_100g, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON CONFLICT(barcode) DO UPDATE SET
                name = excluded.name,
                brand = excluded.brand,
                category = excluded.category,
                nutriscore = excluded.nutriscore,
                image_url = excluded.image_url,
                calories_per_100g = excluded.calories_per_100g,
                proteins_per_100g = excluded.proteins_per_100g,
                carbs_per_100g = excluded.carbs_per_100g,
                fats_per_100g = excluded.fats_per_100g,
                updated_at = excluded.updated_at
        ");
        $stmt->execute([
            $barcode, $name, $brand, $category, $nutriscore, $imageUrl,
            $calories, $proteins, $carbs, $fats, $now, $now
        ]);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Error saving product: ' . $e->getMessage()], 500);
    }

    $saved = getLocalProductByBarcode($pdo, $barcode);
    sendJsonResponse([
        'success' => true,
        'message' => 'Product saved locally',
        'data' => $saved,
        'source' => 'local_db'
    ], 201);
}

function getLocalProductByBarcode($pdo, $barcode) {
    $stmt = $pdo->prepare("
        SELECT id, barcode, name, brand, category, nutriscore, image_url, calories_per_100g, proteins_per_100g, carbs_per_100g, fats_per_100g
        FROM local_products
        WHERE barcode = ?
        LIMIT 1
    ");
    $stmt->execute([$barcode]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

function initializeProductTable($pdo) {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS local_products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            barcode TEXT NOT NULL UNIQUE,
            name TEXT NOT NULL,
            brand TEXT NULL,
            category TEXT NULL,
            nutriscore TEXT NULL,
            image_url TEXT NULL,
            calories_per_100g REAL NULL,
            proteins_per_100g REAL NULL,
            carbs_per_100g REAL NULL,
            fats_per_100g REAL NULL,
            created_at TEXT NOT NULL,
            updated_at TEXT NOT NULL
        )
    ");
}

function normalizeNumeric($value) {
    if ($value === null || $value === '') {
        return null;
    }
    if (!is_numeric($value)) {
        return null;
    }
    return (float)$value;
}

function fetchJsonWithRetry($url, $timeout = 10) {
    // Prefer cURL because some Windows/PHP setups fail TLS with file_get_contents.
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: PantryManagerTFG/1.0 (+mobile scanner)'
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // Dev fallback for Windows TLS/cert issues.
        if ($response === false && stripos($error, 'SSL') !== false) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            $response = curl_exec($ch);
            $error = curl_error($ch);
            $statusCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        }
        curl_close($ch);

        if ($response === false) {
            return [0, null, $error ?: 'curl_error'];
        }

        $decoded = json_decode($response, true);
        return [$statusCode, $decoded, null];
    }

    $context = stream_context_create([
        'http' => [
            'timeout' => $timeout,
            'ignore_errors' => true,
            'header' => "User-Agent: PantryManagerTFG/1.0 (+mobile scanner)\r\n"
        ]
    ]);

    $raw = @file_get_contents($url, false, $context);
    if ($raw === false) {
        return [0, null, 'stream_error'];
    }

    $statusCode = 200;
    if (isset($http_response_header) && is_array($http_response_header)) {
        foreach ($http_response_header as $line) {
            if (preg_match('/HTTP\/\d+\.\d+\s+(\d+)/', $line, $matches)) {
                $statusCode = (int)$matches[1];
                break;
            }
        }
    }

    $decoded = json_decode($raw, true);
    return [$statusCode, $decoded, null];
}

function handleUsersRequest($method, $path_parts, $pdo) {
    $user_id = isset($path_parts[3]) ? (int)$path_parts[3] : null;
    
    switch ($method) {
        case 'GET':
            if ($user_id) {
                getUser($user_id, $pdo);
            } else {
                getAllUsers($pdo);
            }
            break;
        case 'POST':
            createUser($pdo);
            break;
        case 'PUT':
        case 'PATCH':
            if ($user_id) {
                updateUser($user_id, $pdo);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'User ID required'], 400);
            }
            break;
        case 'DELETE':
            if ($user_id) {
                deleteUser($user_id, $pdo);
            } else {
                sendJsonResponse(['success' => false, 'message' => 'User ID required'], 400);
            }
            break;
        default:
            sendJsonResponse(['success' => false, 'message' => 'Method not allowed'], 405);
    }
}

function login($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (!isset($input['email']) || !isset($input['password'])) {
        sendJsonResponse(['success' => false, 'message' => 'Email and password are required'], 400);
    }
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($input['password'], $user['password'])) {
            // Remove password from response
            unset($user['password']);
            
            sendJsonResponse([
                'success' => true,
                'data' => $user,
                'message' => 'Login successful'
            ]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Invalid email or password'], 401);
        }
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Login error: ' . $e->getMessage()], 500);
    }
}

function register($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    $errors = validateUserInput($input, false);
    if (!empty($errors)) {
        sendJsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 422);
        return;
    }
    
    if (!isset($input['password']) || strlen($input['password']) < 6) {
        sendJsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 422);
        return;
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        if ($stmt->fetch()) {
            sendJsonResponse(['success' => false, 'message' => 'Email already exists'], 422);
            return;
        }
        
        // Insert user
        $hashed_password = password_hash($input['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_admin, created_at, updated_at) VALUES (?, ?, ?, 'user', 0, ?, ?)");
        $current_time = date('Y-m-d H:i:s');
        $stmt->execute([$input['name'], $input['email'], $hashed_password, $current_time, $current_time]);
        
        $user_id = $pdo->lastInsertId();
        
        // Get the created user without password
        $stmt = $pdo->prepare("SELECT id, name, email, role, is_admin, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendJsonResponse([
            'success' => true,
            'data' => $user,
            'message' => 'User registered successfully'
        ], 201);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Registration error: ' . $e->getMessage()], 500);
    }
}

function logout($pdo) {
    // In a real application, you would invalidate the session/token
    sendJsonResponse(['success' => true, 'message' => 'Logout successful']);
}

function getAllUsers($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role, is_admin, created_at, updated_at FROM users ORDER BY created_at DESC");
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        sendJsonResponse([
            'success' => true,
            'data' => $users,
            'message' => 'Users retrieved successfully'
        ]);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Error retrieving users: ' . $e->getMessage()], 500);
    }
}

function getUser($id, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT id, name, email, role, is_admin, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            sendJsonResponse([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);
        } else {
            sendJsonResponse(['success' => false, 'message' => 'User not found'], 404);
        }
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Error retrieving user: ' . $e->getMessage()], 500);
    }
}

function createUser($pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    $errors = validateUserInput($input, false);
    if (!empty($errors)) {
        sendJsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 422);
        return;
    }
    
    if (!isset($input['password']) || strlen($input['password']) < 6) {
        sendJsonResponse(['success' => false, 'message' => 'Password must be at least 6 characters'], 422);
        return;
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$input['email']]);
        if ($stmt->fetch()) {
            sendJsonResponse(['success' => false, 'message' => 'Email already exists'], 422);
            return;
        }
        
        // Insert user
        $hashed_password = password_hash($input['password'], PASSWORD_DEFAULT);
        $role = isset($input['role']) ? $input['role'] : 'user';
        $is_admin = isset($input['is_admin']) ? (int)$input['is_admin'] : 0;
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_admin, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $current_time = date('Y-m-d H:i:s');
        $stmt->execute([$input['name'], $input['email'], $hashed_password, $role, $is_admin, $current_time, $current_time]);
        
        $user_id = $pdo->lastInsertId();
        
        // Get the created user without password
        $stmt = $pdo->prepare("SELECT id, name, email, role, is_admin, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendJsonResponse([
            'success' => true,
            'data' => $user,
            'message' => 'User created successfully'
        ], 201);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()], 500);
    }
}

function updateUser($id, $pdo) {
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    $errors = validateUserInput($input, true, $id);
    if (!empty($errors)) {
        sendJsonResponse(['success' => false, 'message' => 'Validation failed', 'errors' => $errors], 422);
        return;
    }
    
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$existing_user) {
            sendJsonResponse(['success' => false, 'message' => 'User not found'], 404);
            return;
        }
        
        // Update user
        $role = isset($input['role']) ? $input['role'] : $existing_user['role'];
        $is_admin = isset($input['is_admin']) ? (int)$input['is_admin'] : $existing_user['is_admin'];
        
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, role = ?, is_admin = ?, updated_at = ? WHERE id = ?");
        $current_time = date('Y-m-d H:i:s');
        $stmt->execute([$input['name'], $input['email'], $role, $is_admin, $current_time, $id]);
        
        // Get the updated user without password
        $stmt = $pdo->prepare("SELECT id, name, email, role, is_admin, created_at, updated_at FROM users WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        sendJsonResponse([
            'success' => true,
            'data' => $user,
            'message' => 'User updated successfully'
        ]);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Error updating user: ' . $e->getMessage()], 500);
    }
}

function deleteUser($id, $pdo) {
    try {
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            sendJsonResponse(['success' => false, 'message' => 'User not found'], 404);
            return;
        }
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        sendJsonResponse([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    } catch (PDOException $e) {
        sendJsonResponse(['success' => false, 'message' => 'Error deleting user: ' . $e->getMessage()], 500);
    }
}

function validateUserInput($input, $is_update = false, $user_id = null) {
    $errors = [];
    
    // Name validation
    if (!isset($input['name']) || empty(trim($input['name']))) {
        $errors['name'] = 'The name field is required.';
    } elseif (strlen($input['name']) < 2) {
        $errors['name'] = 'The name must be at least 2 characters.';
    } elseif (strlen($input['name']) > 255) {
        $errors['name'] = 'The name may not be greater than 255 characters.';
    }
    
    // Email validation
    if (!isset($input['email']) || empty(trim($input['email']))) {
        $errors['email'] = 'The email field is required.';
    } elseif (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'The email must be a valid email address.';
    } elseif (strlen($input['email']) > 255) {
        $errors['email'] = 'The email may not be greater than 255 characters.';
    }
    
    return $errors;
}

function sendJsonResponse($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit;
}
?>
