<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Models\TokenModel;
use Source\Models\UserTokenModel;
use Source\Models\RequestLimitModel;
use Source\Support\RequestLimit;

/**
 * Class ApiController [CONTROLLER]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Controllers
 */
class ApiController extends Controller {

    /** @var \Source\Models\UserModel|null */
    protected $user;

    /** @var array|false */
    protected $headers;

    /** @var array|null */
    protected $response;

    /** @var string */
    protected $token;

    /** @var TokenModel */
    protected $tokenValidator;

    /** @var RequestLimitModel; */
    protected $apiRequestLimit;

    /** @var object */
    protected $data;

    public function __construct() {
        parent::__construct();

        header('Content-Type: application/json; charset=UTF-8');
        $this->headers = getallheaders();

        $this->tokenValidator = new TokenModel();
        $this->apiRequestLimit = new RequestLimitModel();

        //checks if exists a json request data:        
        $this->setRequestData();
    }

    /**
     * 
     * @param int $code
     * @param string $type
     * @param string $message
     * @param string $rule
     * @return \Source\Controllers\ApiController
     */
    protected function call(int $code, string $type = null, string $message = null, string $rule = "errors"): ApiController {
        http_response_code($code);

        if (!empty($type)) {
            $this->response = [
                $rule => [
                    "type" => $type,
                    "message" => (!empty($message) ? $message : null)
                ]
            ];
        }

        return $this;
    }

    /**
     * 
     * @param array $response
     * @return \Source\Controllers\ApiController
     */
    protected function back(array $response = null): ApiController {
        if (!empty($response)) {
            $this->response = (!empty($this->response) ? array_merge($this->response, $response) : $response);
        }

        echo json_encode($this->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $this;
    }

    /**
     * Validates user token
     * @return bool
     */
    protected function validateToken(): bool {
        $this->token = (isset($this->headers["Token"]) ?
                filter_var($this->headers["Token"], FILTER_SANITIZE_STRIPPED) :
                (isset($this->headers["token"]) ? filter_var($this->headers["token"], FILTER_SANITIZE_STRIPPED) : ""));

        if (!$this->token) {
            $this->call(
                    400,
                    "invalid_data",
                    "É preciso informar o token de acesso"
            )->back();
            return false;
        }

        if (!$this->tokenValidator->validateToken($this->token)) {
            $this->call(
                    400,
                    "invalid_data",
                    "O token informado é inválido"
            )->back();
            return false;
        }

        return true;
    }

    /**
     * Validates auth user
     * @param int $requestUserId
     * @return bool
     */
    protected function validateAuthUser(int $requestUserId): bool {
        $authUser = (new UserTokenModel())->findByToken($this->token, "user_id")->user_id;

        if ($authUser != $requestUserId) {
            $this->call(
                    400,
                    "invalid_data",
                    "Sua requisição é inválida"
            )->back();
            return false;
        }

        $this->user = user()->findById($authUser, "id, name, email");

        return true;
    }

    /**
     * Limits requisitions
     * @param string $identifier
     * @param string $endpoint
     * @param int $limit
     * @param int $seconds
     * @param bool $testMode
     * @return bool
     */
    protected function requestLimit(string $identifier, string $endpoint, int $limit, int $seconds, bool $testMode = false): bool {
        $requestLimit = (new RequestLimit())->requestLimit($identifier, $endpoint, $limit, $seconds);

        if (!$requestLimit) {
            $this->call(
                    400,
                    "request_limit",
                    "Você ultrapassou o limite de requisições para essa ação"
            )->back();

            return false;
        }

        return true;
    }

    /**
     *
     * @return void
     */
    private function setRequestData(): void {
        //get json request data:
        $json = file_get_contents('php://input');
        $this->data = ($json ? (array) json_decode($json) : null);

        if ($this->data) {
            $this->data = filter_array($this->data);
        }
    }

}
