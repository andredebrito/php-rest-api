<?php

namespace Source\Controllers;

use Source\Controllers\ApiController;
use Source\Models\UserModel;
use Source\Models\DrinkModel;
use Source\Support\Pager;

/**
 * Class UserController [CONTROLLER]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Controllers
 */
class UserController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * List users endpoint
     * 
     * @param array $data
     * @return void
     */
    public function index(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $findUsers = user()->find(null, null, "id, name, email, created_at, updated_at");

        if (!$findUsers->count()) {
            $this->call(
                    404,
                    "not_found",
                    "Nenhum usuário encontrado"
            )->back(["results" => 0]);
            return;
        }

        $page = (!empty($data["page"]) ? $data["page"] : 1);
        $pager = new Pager(url("/users/page/"));
        $limit = (!empty($data["limit"]) ? $data["limit"] : RESULTS_LIMIT);
        $pager->pager($findUsers->count(), $limit, $page);

        $response["results"] = $findUsers->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        /** @var UserModel $user */
        foreach ($findUsers->limit($pager->limit())->offset($pager->offset())->order("name ASC")->fetch(true) as $user) {
            $response["users"][] = $user->data();
        }

        $this->back($response);
        return;
    }

    /**
     * Create user endpoint
     * 
     * @return void
     */
    public function create(): void {
        $userCreate = new UserModel();
        $userCreate->name = (!empty($this->data["name"]) ? $this->data["name"] : null);
        $userCreate->email = (!empty($this->data["email"]) ? $this->data["email"] : null);
        $userCreate->password = (!empty($this->data["password"]) ? $this->data["password"] : null);

        if (!$userCreate->save()) {
            $this->call(
                    400,
                    "invalid_data",
                    $userCreate->message()->getText()
            )->back();
            return;
        }

        $this->call(
                201,
                "resource_created",
                $userCreate->message()->getText(),
                "success"
        )->back();
    }

    /**
     * Get user endpoint
     * 
     * @param array $data
     * @return void
     */
    public function read(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        if (empty($data["user_id"]) || !$user_id = filter_var($data["user_id"], FILTER_VALIDATE_INT)) {
            $this->call(
                    400,
                    "invalid_data",
                    "É preciso informar o ID do usuário que deseja consultar"
            )->back();
            return;
        }

        $user = user()->findById($user_id, "id, name");

        if (!$user) {
            $this->call(
                    404,
                    "not_found",
                    "Você tentou acessar um usuário que não existe"
            )->back();
            return;
        }

        $drinkCounter = (new DrinkModel())->findByUserToday($user->id);        
        $user->drink_couter =  ($drinkCounter ? $drinkCounter->drink_counter : 0);
        $response["user"] = $user->data();
        $this->back($response);
    }

    /**
     * Update user endpoint
     * 
     * @param array $data
     * @return void
     */
    public function update(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);

        if (!$this->validateAuthUser($userId)) {
            exit();
        }

        $userUpdate = (new UserModel())->findById($userId);
        $userUpdate->name = (!empty($this->data["name"]) ? $this->data["name"] : $userUpdate->name);
        $userUpdate->email = (!empty($this->data["email"]) ? $this->data["email"] : $userUpdate->email);
        $userUpdate->password = (!empty($this->data["password"]) ? $this->data["password"] : $userUpdate->password);

        if (!$userUpdate->save()) {
            $this->call(
                    400,
                    "invalid_data",
                    $userUpdate->message()->getText()
            )->back();
            return;
        }

        $this->call(
                201,
                "resource_updated",
                $userUpdate->message()->getText(),
                "success"
        )->back();
    }

    /**
     * Delete user endpoint
     * 
     * @param array $data
     * @return void
     */
    public function delete(array $data): void {
        $this->token = (isset($this->headers["token"]) ? filter_var($this->headers["token"], FILTER_SANITIZE_STRIPPED) : "");

        if (!$this->validateToken()) {
            exit();
        }

        $data = filter_array($data);
        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);

        if (!$this->validateAuthUser($userId)) {
            exit();
        }

        $userDelete = (new UserModel())->findById($userId);
        if (!$userDelete->destroy()) {
            $this->call(
                    500,
                    "internal_error",
                    $userDelete->fail()->getMessage()
            )->back();
            return;
        }

        $this->call(
                200,
                "resource_deleted",
                "Seu usuário foi apagado com sucesso",
                "success"
        )->back();
    }

}
