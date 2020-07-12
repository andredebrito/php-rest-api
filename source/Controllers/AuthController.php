<?php

namespace Source\Controllers;

use Source\Models\DrinkModel;
use Source\Models\AuthModel;

/**
 * Class AuthController [CONTROLLER]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Controllers
 */
class AuthController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Login endpoint
     * 
     * @return void
     */
    public function login(): void {       

        if (!$this->auth()) {
            exit();
        }

        $tokenGenerate = new AuthModel();
        if (!$authUser = $tokenGenerate->tokenGenerate($this->user)) {
            $this->call(
                    500,
                    "internal_error",
                    $tokenGenerate->message()->getText()
            )->back();
            return;
        }

        $drinkCounter = (new DrinkModel())->findByUserToday($this->user->id, "drink_counter");
        $authUser->drink_counter = (!empty($drinkCounter->drink_counter) ? $drinkCounter->drink_counter : 0);

        $response["authUser"] = $authUser->data();
        $this->back($response);
        return;
    }

    /**
     * Validates login
     * 
     * @return bool
     */
    private function auth(): bool {
        if (empty($this->data["email"]) || empty($this->data["password"])) {
            $this->call(
                    400,
                    "auth_empty",
                    "Por efetuar o login informe seu e-mail e sua senha!"
            )->back();
            return false;
        }

        //limit auth requests to 3 attempts in 5 minutes:
        $endpoint = [$this->data["email"], "loginAuth", 3, 60 * 5];
        if (!$this->requestLimit($endpoint[0], $endpoint[1], $endpoint[2], $endpoint[3], false)) {
            return false;
        }

        $auth = new AuthModel();
        $user = $auth->attempt($this->data["email"], $this->data["password"]);

        if (!$user) {
            $this->call(
                    401,
                    "invalid_auth",
                    $auth->message()->getText()
            )->back();
            return false;
        }

        $this->user = $user;
        return true;
    }

}
