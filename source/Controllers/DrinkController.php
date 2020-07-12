<?php

namespace Source\Controllers;

use Source\Controllers\ApiController;
use Source\Models\DrinkModel;
use Source\Support\Pager;

/**
 * Class DrinkController [CONTROLLER]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Controllers
 */
class DrinkController extends ApiController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * List user drinks endpoint
     * 
     * @param array $data
     * @return void
     */
    public function index(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $data = filter_array($data);
        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);

        if (!$this->validateAuthUser($userId)) {
            exit();
        }

        $findDrinks = (new DrinkModel())->find("user_id = :user", "user={$this->user->id}", "id, user_id, date, drink_ml");

        if (!$findDrinks->count()) {
            $this->call(
                    404,
                    "not_found",
                    "Nenhum registro encontrado"
            )->back(["results" => 0]);
            return;
        }

        $page = (!empty($data["page"]) ? $data["page"] : 1);
        $pager = new Pager(url("/users/{$this->user->id}/drinks/page/"));
        $limit = (!empty($data["limit"]) ? $data["limit"] : RESULTS_LIMIT);
        $pager->pager($findDrinks->count(), $limit, $page);

        $response["results"] = $findDrinks->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        /** @var DrinkModel $drink */
        foreach ($findDrinks->limit($pager->limit())->offset($pager->offset())->order("date DESC")->fetch(true) as $drink) {
            unset($drink->data()->user_id);
            $response["drinks_history"][] = $drink->data();
        }

        $this->back($response);
        return;
    }

    /**
     * Create user drink endpoint
     * 
     * @param array $data
     * @return void
     */
    public function create(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $userId = filter_var($data["user_id"], FILTER_VALIDATE_INT);

        if (!$this->validateAuthUser($userId)) {
            exit();
        }

        //UPDATE
        $findDrinksToday = (new DrinkModel())->findByUserToday($userId);
        if ($findDrinksToday) {
            $findDrinksToday->drink_ml += (!empty($this->data["drink_ml"]) ? $this->data["drink_ml"] : null);

            if (!$findDrinksToday->save()) {
                $this->call(
                        400,
                        "invalid_data",
                        $findDrinksToday->message()->getText()
                )->back();
                return;
            }

            $this->user->drink_counter = $findDrinksToday->drink_counter;
            $response["user"] = $this->user->data();
            $this->back($response);
            return;
        }

        //CREATE
        $createDrink = new DrinkModel();
        $createDrink->user_id = $this->user->id;
        $createDrink->drink_ml = (!empty($this->data["drink_ml"]) ? $this->data["drink_ml"] : null);

        if (!$createDrink->save()) {
            $this->call(
                    400,
                    "invalid_data",
                    $createDrink->message()->getText()
            )->back();
            return;
        }

        $this->user->drink_counter = $createDrink->drink_counter;
        $response["user"] = $this->user->data();
        $this->back($response);
    }

    /**
     * Updates user drink endpoint
     * 
     * @param array $data
     * @return void
     */
    public function update(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $userId = (isset($data["user_id"]) ? filter_var($data["user_id"], FILTER_VALIDATE_INT) : 0);
        if (!$this->validateAuthUser($userId)) {
            exit();
        }

        if (empty($data["drink_id"]) || empty($this->data["drink_ml"]) || !filter_var($this->data["drink_ml"], FILTER_VALIDATE_INT)) {
            $this->call(400, "invalid_data", "Para atualizar, é preciso informar o id e a quantidade mL do registro!")
                    ->back();
            return;
        }

        $drinkId = filter_var($data["drink_id"], FILTER_VALIDATE_INT);
        if (!$this->validateDrink($drinkId)) {
            exit();
        }

        $drinkUpdate = (new DrinkModel())->findById($drinkId);
        $drinkUpdate->drink_ml = filter_var($this->data["drink_ml"], FILTER_VALIDATE_INT);
        if (!$drinkUpdate->save()) {
            $this->call(
                    400,
                    "invalid_data",
                    $drinkUpdate->message()->getText()
            )->back();
            return;
        }

        $this->call(
                201,
                "resource_updated",
                $drinkUpdate->message()->getText(),
                "success"
        )->back();
    }

    /**
     * Deletes user drink endpoint
     * 
     * @param array $data
     * @return void
     */
    public function delete(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $userId = (isset($data["user_id"]) ? filter_var($data["user_id"], FILTER_VALIDATE_INT) : 0);
        if (!$this->validateAuthUser($userId)) {
            exit();
        }

        if (empty($data["drink_id"])) {
            $this->call(400, "invalid_data", "Para deletar, é preciso informar o id registro!")
                    ->back();
            return;
        }

        $drinkId = filter_var($data["drink_id"], FILTER_VALIDATE_INT);
        if (!$this->validateDrink($drinkId)) {
            exit();
        }

        $drinkDelete = (new DrinkModel())->findById($drinkId);
        if (!$drinkDelete->destroy()) {
            $this->call(
                    400,
                    "invalid_data",
                    $drinkDelete->message()->getText()
            )->back();
            return;
        }

        $this->call(
                201,
                "resource_deleted",
                "Registro removido com sucesso!",
                "success"
        )->back();
    }

    /**
     * Get users drink ranking endpoint
     * 
     * @param array $data
     * @return void
     */
    public function ranking(array $data): void {
        if (!$this->validateToken()) {
            exit();
        }

        $data = filter_array($data);

        $drinkRanking = (new DrinkModel())->findByToday("user_id, date, drink_ml");

        if (!$drinkRanking->count()) {
            $this->call(
                    404,
                    "not_found",
                    "Nenhum registro encontrado"
            )->back(["results" => 0]);
            return;
        }

        $page = (!empty($data["page"]) ? $data["page"] : 1);
        $pager = new Pager(url("/users/drinks/ranking/page/"));
        $limit = (!empty($data["limit"]) ? $data["limit"] : RESULTS_LIMIT);
        $pager->pager($drinkRanking->count(), $limit, $page);

        $response["results"] = $drinkRanking->count();
        $response["page"] = $pager->page();
        $response["pages"] = $pager->pages();

        /** @var DrinkModel $drink */
        foreach ($drinkRanking->limit($pager->limit())->offset($pager->offset())->order("drink_ml DESC")->fetch(true) as $drink) {
            $drink->data()->name = user()->findById($drink->user_id, "name")->name;
            unset($drink->data()->user_id, $drink->data()->date);

            $response["ranking_drinks_today"][] = $drink->data();
        }

        $this->back($response);
        return;
    }

    /**
     * Validate if the drink exists and belongs to requester user
     * 
     * @param int $drinkId
     * @return bool
     */
    private function validateDrink(int $drinkId): bool {
        $findDrink = (new DrinkModel())
                ->find("id = :drink AND user_id = :user", "drink={$drinkId}&user={$this->user->id}")
                ->fetch();

        if (!$findDrink) {
            $this->call(400, "invalid_data", "Você tentou editar um registro que não existe!")
                    ->back();
            return false;
        }

        return true;
    }

}
