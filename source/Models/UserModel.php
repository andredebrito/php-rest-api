<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class UserModel [MODEL]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Models
 */
class UserModel extends Model {

    public function __construct() {
        parent::__construct("users", ["name", "email", "password"]);
    }

    /**
     * Find user by email
     * @param string $email
     * @param string $columns
     * @return \Source\Models\UserModel|null
     */
    public function findByEmail(string $email, string $columns = "*"): ?UserModel {
        $find = $this->find("email = :email", "email={$email}", $columns);
        return $find->fetch();
    }

    /**
     * 
     * @return bool
     */
    public function save(): bool {
        /**
         * Validation
         */
        if (!$this->validate()) {
            return false;
        }

        /**
         * User Update
         */
        if (!empty($this->id)) {
            $userId = $this->id;

            if ($this->find("email = :email AND id != :id", "email={$this->email}&id={$userId}", "id")->fetch()) {
                $this->message->warning("O e-mail informado já esta cadastrado!");
                return false;
            }
            $this->update($this->safe(), "id = :id", "id={$userId}");
            if ($this->fail()) {
                $this->message->error("Erro ao atualizar, verifique os dados!");
                return false;
            }

            $this->message->success("Usuário atualizado com sucesso!");
        }

        /**
         * User Create
         */
        if (empty($this->id)) {
            if ($this->findByEmail($this->email, "id")) {
                $this->message->warning("O usuário informado já esta cadastrado!");
                return false;
            }

            $userId = $this->create($this->safe());
            if ($this->fail()) {
                $this->message->error("Erro ao cadastrar, verifique os dados!");
                return false;
            }

            $this->message->success("Usuário cadastrado com sucesso!");
        }

        $this->data = ($this->findById($userId))->data();
        return true;
    }

    /**
     * 
     * @return bool
     */
    private function validate(): bool {
        if (!$this->required()) {
            $this->message->warning("Nome, E-mail e Senha são obrigatórios!");
            return false;
        }

        if (!is_email($this->email)) {
            $this->message->warning("O E-mail informado não tem um formato válido!");
            return false;
        }

        if (!is_passwd($this->password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->warning("A senha deve ter entre {$min} e {$max} caracteres!");
            return false;
        } else {
            //encrypts user password:
            $this->password = passwd($this->password);
        }

        return true;
    }

}
