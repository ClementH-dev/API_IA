<?php
namespace Core;

class Router {
    private static array $routes = [];
    private static ?string $currentPrefix = null;

    public static function get(string $path, callable|array $callback): void {
        $fullPath = self::buildFullPath($path);
        self::$routes['GET'][$fullPath] = $callback;
    }

    public static function post(string $path, callable|array $callback): void {
        $fullPath = self::buildFullPath($path);
        self::$routes['POST'][$fullPath] = $callback;
    }

    public static function delete(string $path, callable|array $callback): void {
        $fullPath = self::buildFullPath($path);
        self::$routes['DELETE'][$fullPath] = $callback;
    }

    public static function put(string $path, callable|array $callback): void {
        $fullPath = self::buildFullPath($path);
        self::$routes['PUT'][$fullPath] = $callback;
    }

    /**
     * Méthode pour gérer les groupes de routes
     */
    public static function group(string $prefix, callable $routesGroup): void {
        // Sauvegarde l'ancien préfixe pour permettre des groupes imbriqués
        $currentPrefix = self::$currentPrefix ?? '';
        self::$currentPrefix = rtrim($currentPrefix, '/') . '/' . ltrim($prefix, '/');
        
        // Exécute le groupe de routes
        $routesGroup();
        self::$currentPrefix = $currentPrefix;
    }
    
    private static function buildFullPath(string $path): string {
        $prefix = self::$currentPrefix ?? '';
        return rtrim($prefix, '/') . '/' . ltrim($path, '/');
    }

    public static function handle(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        $uri = str_replace($basePath, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

        $routes = self::$routes[$method] ?? [];
        foreach ($routes as $route => $callback) {
            $pattern = preg_replace('#\{([^}]+)\}#', '([^/]+)', $route);
            $pattern = "#^" . $pattern . "$#";

            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);

                if (is_callable($callback)) {
                    call_user_func_array($callback, $matches);
                } elseif (is_array($callback)) {
                    [$controllerClass, $method] = $callback;
                    $controllerInstance = self::resolveController($controllerClass);
                    call_user_func_array([$controllerInstance, $method], $matches);
                }
                return;
            }
        }

        http_response_code(404);
        echo json_encode(['error' => 'Route non trouvée']);
    }

    private static function resolveController(string $controllerClass) {
        // Déduire le nom du service correspondant
        $serviceClass = str_replace('Controllers', 'Services', $controllerClass);
        $serviceClass = str_replace('Controller', 'Service', $serviceClass);

        if (class_exists($serviceClass)) {
            // Instancier dynamiquement les dépendances du service
            $serviceInstance = self::resolveDependencies($serviceClass);
            return new $controllerClass($serviceInstance);
        }

        return new $controllerClass(); // Pas de service, on instancie le contrôleur seul
    }

    private static function resolveDependencies(string $class) {
        $reflectionClass = new \ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        if (!$constructor) return new $class(); // Pas de constructeur, pas de dépendances

        $dependencies = [];
        foreach ($constructor->getParameters() as $param) {
            $paramClass = $param->getType()?->getName();
            if ($paramClass && class_exists($paramClass)) {
                $dependencies[] = self::resolveDependencies($paramClass); // Récursion pour injecter les dépendances du service
            }
        }

        return $reflectionClass->newInstanceArgs($dependencies);
    }
}
