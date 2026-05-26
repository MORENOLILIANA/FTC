<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NutriCasa API</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .card { transition: transform 0.15s ease, box-shadow 0.15s ease; }
        .card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1); }
        .pulse-dot { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }
    </style>
</head>
<body class="bg-gray-950 text-white min-h-screen">

    <!-- Header -->
    <header class="border-b border-gray-800 bg-gray-900/80 backdrop-blur sticky top-0 z-10">
        <div class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-lg">🥗</div>
                <div>
                    <span class="font-bold text-lg">NutriCasa</span>
                    <span class="ml-2 text-xs bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-500/30">v1.0.0</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div class="pulse-dot w-2 h-2 rounded-full bg-emerald-400"></div>
                <span class="text-sm text-emerald-400 font-medium">API Online</span>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-6 py-10 space-y-10">

        <!-- Stats -->
        <section>
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Base de datos</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php
                    $stats = [
                        ['label' => 'Usuarios',   'value' => \App\Models\User::count(),    'icon' => '👤', 'color' => 'blue'],
                        ['label' => 'Productos',  'value' => \App\Models\Product::count(), 'icon' => '🛒', 'color' => 'emerald'],
                        ['label' => 'Despensas',  'value' => \App\Models\Pantry::count(),  'icon' => '🏠', 'color' => 'violet'],
                        ['label' => 'Items',      'value' => \App\Models\PantryItem::count(), 'icon' => '📦', 'color' => 'amber'],
                    ];
                ?>
                <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card bg-gray-900 border border-gray-800 rounded-xl p-5">
                    <div class="text-2xl mb-2"><?php echo e($stat['icon']); ?></div>
                    <div class="text-3xl font-bold text-white"><?php echo e(number_format($stat['value'])); ?></div>
                    <div class="text-sm text-gray-400 mt-1"><?php echo e($stat['label']); ?></div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <!-- Estado del sistema -->
        <section>
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Sistema</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php
                    $checks = [
                        ['label' => 'Base de datos', 'ok' => true, 'detail' => 'MySQL conectado'],
                        ['label' => 'Open Food Facts', 'ok' => true, 'detail' => 'API externa activa'],
                        ['label' => 'UPC Item DB', 'ok' => true, 'detail' => 'API de respaldo activa'],
                    ];
                    try { \DB::connection()->getPdo(); } catch(\Exception $e) { $checks[0]['ok'] = false; $checks[0]['detail'] = 'Sin conexión'; }
                ?>
                <?php $__currentLoopData = $checks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $check): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="card bg-gray-900 border border-gray-800 rounded-xl px-5 py-4 flex items-center gap-4">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center <?php echo e($check['ok'] ? 'bg-emerald-500/10' : 'bg-red-500/10'); ?>">
                        <span class="text-lg"><?php echo e($check['ok'] ? '✅' : '❌'); ?></span>
                    </div>
                    <div>
                        <div class="font-medium text-sm"><?php echo e($check['label']); ?></div>
                        <div class="text-xs text-gray-400"><?php echo e($check['detail']); ?></div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <!-- Endpoints -->
        <section>
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Endpoints de la API</h2>
            <div class="space-y-3">
                <?php
                    $groups = [
                        ['title' => '🔐 Autenticación', 'color' => 'gray', 'routes' => [
                            ['POST', '/api/v1/auth/register', 'Registrar usuario'],
                            ['POST', '/api/v1/auth/login', 'Iniciar sesión'],
                            ['POST', '/api/v1/auth/logout', 'Cerrar sesión'],
                            ['GET',  '/api/v1/auth/me', 'Usuario actual'],
                        ]],
                        ['title' => '🛒 Productos', 'color' => 'emerald', 'routes' => [
                            ['GET',    '/api/v1/products', 'Listar productos'],
                            ['POST',   '/api/v1/products', 'Crear producto manual'],
                            ['GET',    '/api/v1/products/search?query=...', 'Buscar por nombre'],
                            ['GET',    '/api/v1/products/barcode/{ean}', 'Buscar por código de barras'],
                            ['GET',    '/api/v1/products/{id}', 'Ver producto'],
                        ]],
                        ['title' => '🏠 Despensas', 'color' => 'violet', 'routes' => [
                            ['GET',    '/api/v1/pantries', 'Mis despensas'],
                            ['POST',   '/api/v1/pantries', 'Crear despensa'],
                            ['GET',    '/api/v1/pantries/{id}', 'Ver despensa con items'],
                            ['POST',   '/api/v1/pantries/{id}/items', 'Añadir item (soporta barcode)'],
                            ['PUT',    '/api/v1/pantries/{id}/items/{itemId}', 'Actualizar item'],
                            ['DELETE', '/api/v1/pantries/{id}/items/{itemId}', 'Eliminar item'],
                            ['POST',   '/api/v1/pantries/{id}/share', 'Compartir despensa'],
                        ]],
                        ['title' => '📋 Listas de compra', 'color' => 'amber', 'routes' => [
                            ['GET',  '/api/v1/shopping-lists', 'Mis listas'],
                            ['POST', '/api/v1/shopping-lists', 'Crear lista'],
                            ['POST', '/api/v1/shopping-lists/{id}/items', 'Añadir item'],
                            ['POST', '/api/v1/shopping-lists/{id}/move-to-pantry', 'Mover a despensa'],
                        ]],
                    ];
                    $methodColors = [
                        'GET'    => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                        'POST'   => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                        'PUT'    => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                        'DELETE' => 'bg-red-500/10 text-red-400 border-red-500/20',
                    ];
                ?>

                <?php $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-gray-900 border border-gray-800 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-800 bg-gray-900/50">
                        <span class="font-semibold text-sm"><?php echo e($group['title']); ?></span>
                    </div>
                    <div class="divide-y divide-gray-800/50">
                        <?php $__currentLoopData = $group['routes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $route): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="px-5 py-3 flex items-center gap-4 hover:bg-gray-800/30 transition-colors">
                            <span class="text-xs font-mono font-bold px-2 py-0.5 rounded border <?php echo e($methodColors[$route[0]] ?? ''); ?> min-w-[52px] text-center">
                                <?php echo e($route[0]); ?>

                            </span>
                            <code class="text-sm text-gray-300 flex-1"><?php echo e($route[1]); ?></code>
                            <span class="text-xs text-gray-500 hidden md:block"><?php echo e($route[2]); ?></span>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>

        <!-- Tools -->
        <section>
            <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-4">Herramientas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="/telescope" class="card block bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-gray-700">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl">🔭</span>
                        <span class="font-semibold">Laravel Telescope</span>
                    </div>
                    <p class="text-sm text-gray-400">Monitoriza peticiones HTTP, queries SQL, logs y excepciones en tiempo real.</p>
                </a>
                <a href="/adminer/" class="card block bg-gray-900 border border-gray-800 rounded-xl p-5 hover:border-gray-700">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl">🗄️</span>
                        <span class="font-semibold">Adminer</span>
                    </div>
                    <p class="text-sm text-gray-400">Gestiona la base de datos MySQL visualmente — tablas, registros, consultas.</p>
                </a>
            </div>
        </section>

    </main>

    <footer class="border-t border-gray-800 mt-16 py-6 text-center text-xs text-gray-600">
        NutriCasa API · Laravel <?php echo e(app()->version()); ?> · PHP <?php echo e(PHP_VERSION); ?>

        · Servidor <?php echo e(now()->format('d/m/Y H:i')); ?>

    </footer>

</body>
</html>
<?php /**PATH /var/www/resources/views/dashboard.blade.php ENDPATH**/ ?>