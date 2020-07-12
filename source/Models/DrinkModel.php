<?php

namespace Source\Models;

use Source\Core\Model;
use DateTime;

/**
 * Class Drink [MODEL]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Models
 */
class DrinkModel extends Model {

    public function __construct() {
        parent::__construct("drinks", ["user_id", "drink_ml"]);
    }

    /**
     * 
     * @param int $userId
     * @param string $columns
     * @return \Source\Models\UserTokenModel|null
     */
    public function findByUser(int $userId, string $columns = "*"): ?UserTokenModel {
        $find = $this->find("user_id = :user", "user={$userId}", $columns);
        return $find->fetch();
    }

    /**
     * 
     * @param int $userId
     * @param string $columns
     * @return \Source\Models\DrinkModel|null
     */
    public function findByUserToday(int $userId, string $columns = "*"): ?DrinkModel {
        $today = (new DateTime("now"))->format("Y-m-d");
        $find = $this->find("user_id = :user AND date = :date", "user={$userId}&date={$today}", $columns);
        return $find->fetch();
    }

    /**
     * 
     * @param type $columns
     * @return \Source\Models\DrinkModel|null
     */
    public function findByToday($columns = "*"): ?DrinkModel {
        $today = (new DateTime("now"))->format("Y-m-d");
        $find = $this->find("date = :date AND drink_ml > :ml", "date={$today}&ml=0", $columns);
        return $find;
    }

    public function save(): bool {
        /**
         * Validation
         */
        if (!$this->validate()) {
            return false;
        }

        /**
         * Drink Update
         */
        if (!empty($this->id)) {
            $drinkId = $this->id;
            $this->drink_counter += 1;

            $this->update($this->safe(), "id = :id", "id={$drinkId}");

            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados!");
                return false;
            }
            $this->message->success("Registo atualizado com sucesso!");
        }

        /**
         * Drink Create
         */
        if (empty($this->id)) {
            $this->drink_counter = 1;
            $this->date = (new DateTime("now"))->format("Y-m-d");

            $drinkId = $this->create($this->safe());

            if ($this->fail()) {
                $this->message->error("Erro ao cadastrar, verifique os dados!");
                return false;
            }
            $this->message->success("Registro cadastrado com sucesso!");
        }

        $this->data = ($this->findById($drinkId))->data();
        return true;
    }

    public function validate(): bool {
        if (!$this->required()) {
            $this->message->warning("A quantidade em mL de água é obrigatória!");
            return false;
        }

        return true;
    }

}
