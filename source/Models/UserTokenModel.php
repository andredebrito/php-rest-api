<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class UserTokenModel [MODEL]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Models
 */
class UserTokenModel extends Model {

    public function __construct() {
        parent::__construct("user_tokens", ["user_id", "token"]);
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
     * @param string $token
     * @param string $columns
     * @return \Source\Models\UserTokenModel|null
     */
    public function findByToken(string $token, string $columns = "*"): ?UserTokenModel {
        $find = $this->find("token = :token", "token={$token}", $columns);
        return $find->fetch();
    }

}
