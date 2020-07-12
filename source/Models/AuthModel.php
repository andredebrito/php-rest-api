<?php

namespace Source\Models;

use Source\Core\Model;
use Source\Models\UserTokenModel;
use Source\Models\TokenModel;
use DateTime;

/**
 * Class Auth [MODEL]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Models
 */
class AuthModel extends Model {

    /**
     * Auth constructor.
     */
    public function __construct() {
        parent::__construct("users", ["email", "password"]);
    }

    /**
     * Login attempts
     * 
     * @param string $email
     * @param string $password
     * @return \Source\Models\User|null
     */
    public function attempt(string $email, string $password): ?UserModel {
        if (!is_email($email)) {
            $this->message->warning("O e-mail informado não é válido");
            return null;
        }

        if (!is_passwd($password)) {
            $this->message->warning("A senha informada não é válida");
            return null;
        }

        // find user by e-mail
        $user = user()->findByEmail($email);

        if (!$user) {
            $this->message->error("O e-mail informado não está cadastrado");
            return null;
        }

        if (!passwd_verify($password, $user->password)) {
            $this->message->error("A senha informada não confere");
            return null;
        }

        if (passwd_rehash($user->password)) {
            $user->password = $password;
            $user->save();
        }

        return $user;
    }

    /**
     * 
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function login(string $email, string $password): bool {
        $user = $this->attempt($email, $password);
        if (!$user) {
            return false;
        }

        return true;
    }

    /**
     * 
     * @param \Source\Models\UserModel $user
     * @return \Source\Models\UserModel|null
     */
    public function tokenGenerate(UserModel $user): ?UserModel {
        $token = (new TokenModel())->setToken($user->id, $user->email, $user->name);
        $expiresAt = (new DateTime("now"))->modify("+" . TOKEN_EXPIRATION_HOURS . " hours")->format("Y-m-d H:i:s");

        //update:
        $tokenUpdate = (new UserTokenModel())->findByUser($user->id);
        if ($tokenUpdate) {
            //update user token:
            $tokenUpdate->token = $token;
            $tokenUpdate->expires_at = $expiresAt;
            $tokenUpdate->save();

            if (!$tokenUpdate->save()) {
                $this->message->error($tokenUpdate->fail()->getMessage());
                return null;
            }

            $return = user()->findById($user->id, "id, name, email");
            $return->token = $tokenUpdate->token;

            return $return;
        }

        //create:
        $tokenCreate = new UserTokenModel();
        $tokenCreate->user_id = $user->id;
        $tokenCreate->token = $token;
        $tokenCreate->expires_at = $expiresAt;

        if (!$tokenCreate->save()) {
            $this->message->error($tokenCreate->fail()->getMessage());
            return null;
        }

        $return = user()->findById($user->id, "id, name, email");
        $return->token = $tokenCreate->token;          

        return $return;
        
    }

}
