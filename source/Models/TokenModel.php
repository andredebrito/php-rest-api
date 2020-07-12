<?php

namespace Source\Models;

use Source\Models\UserTokenModel;
use DateTime;

/**
 * Class Token [MODEL]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Models
 */
class TokenModel {

   /**
    * Check if the token is valid
    * @param string $token
    * @return bool
    */
    public function validateToken(string $token): bool {
        $findToken = (new UserTokenModel())->findByToken($token);
        if (!$findToken) {
            return false;
        }

        $expirationDate = $findToken->expires_at;
        $dateNow = (new DateTime("Now"))->format("Y-m-d H:i:s");

        //checks if token is expired:
        if ($dateNow > $expirationDate) {
            //remove expired token from table:
            $findToken->destroy();
            return false;
        }

        return true;
    }

    /**
     * 
     * @param int $userId
     * @param string $email
     * @param string $name
     * @param int $drinkCounter
     * @return string
     */
    public function setToken(int $userId, string $email, string $name): string {
        $token = md5($userId . $email . $name . uniqid());
        return $token;
    }

    /**
     * 
     * @param int $userId
     * @return string|null
     */
    public function getToken(int $userId): ?string {
        $findToken = (new UserTokenModel())->findByUser($userId);

        if (!$findToken) {
            return null;
        }

        return $findToken->token;
    }

}
