<?php

ob_start();

/**
 * autoload
 */
require __DIR__ . "/vendor/autoload.php";

/**
 * BOOTSTRAP
 */
use CoffeeCode\Router\Router;

//Starts the router:
$route = new Router(url(), ":");

/**
 * API ROUTES
 */
$route->namespace("Source\Controllers");

//login
$route->group("/login");
$route->post("/", "AuthController:login");

//users
$route->group("/users");
$route->post("/", "UserController:create");
$route->get("/{user_id}", "UserController:read");
$route->get("/", "UserController:index");
$route->get("/page/{page}", "UserController:index");
$route->get("/page/{page}/limit/{limit}", "UserController:index");
$route->put("/{user_id}", "UserController:update");
$route->delete("/{user_id}", "UserController:delete");

//drinks
$route->get("/{user_id}/drinks", "DrinkController:index");
$route->get("/{user_id}/drinks/page/{page}", "DrinkController:index");
$route->get("/{user_id}/drinks/page/{page}/limit/{limit}", "DrinkController:index");
$route->post("/{user_id}/drink", "DrinkController:create");
$route->put("/{user_id}/drink/{drink_id}", "DrinkController:update");
$route->delete("/{user_id}/drink/{drink_id}", "DrinkController:delete");

//drinks ranking
$route->get("/drinks/ranking", "DrinkController:ranking");
$route->get("/drinks/ranking/page/{page}", "DrinkController:ranking");
$route->get("/drinks/ranking/page/{page}/limit/{limit}", "DrinkController:ranking");


/**
 * ERROR ROUTES
 */
$route->namespace("Source\Controllers\App");
$route->group("/ops");
$route->get("/{errcode}", "ErrorController:error");

/**
 * ROUTE
 */
$route->dispatch();

/**
 * ERROR REDIRECT
 */
if ($route->error()) {
    header('Content-Type: application/json; charset=UTF-8');
    http_response_code(404);

    echo json_encode([
        "errors" => [
            "type " => "endpoint_not_found",
            "message" => "Não foi possível processar a requisição"
        ]
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

ob_end_flush();
