<?php

namespace Source\Support;

use Source\Models\RequestLimitModel;

/**
 * Class RequestLimit
 *
 * @author André de Brito <https://github.com/andredebrito>
 * @package Source\Support
 */
class RequestLimit {

    private $request;
    private $identifier;
    private $identifierType;
    private $endpoint;
    private $limit;
    private $seconds;
    private $testMode;

    /**
     * 
     * @param string $identifier
     * @param string $endpoint
     * @param int $limit
     * @param int $seconds
     * @param bool $testMode
     * @return bool
     */
    public function requestLimit(string $identifier, string $endpoint, int $limit, int $seconds, bool $testMode = false): bool {
        $this->identifier = $identifier;
        $this->identifierType = (is_email($this->identifier) ? "email" : "token");
        $this->endpoint = $endpoint;
        $this->limit = $limit;
        $this->seconds = $seconds;
        $this->testMode = $testMode;

        //valida se já existe uma requisição salva:
        $findRequest = (New RequestLimitModel())->find("request_identifier = :identifier AND endpoint = :endpoint",
                        "identifier={$identifier}&endpoint={$endpoint}")->fetch();

        if (!$findRequest && !$testMode) {
            return $this->createRequest();
        }

        if ($findRequest && $findRequest->time >= time() && $findRequest->requests < $limit && !$testMode) {
            $this->request = $findRequest;
            return $this->updateRequest();
        }

        if ($findRequest && $findRequest->time >= time() && $findRequest->requests >= $limit) {
            return false;
        }

        return $this->refreshRequest($findRequest);
    }

    /**
     * Creates new request
     * @return bool
     */
    private function createRequest(): bool {
        $requestCreate = new RequestLimitModel();
        $requestCreate->request_identifier = $this->identifier;
        $requestCreate->identifier_type = $this->identifierType;
        $requestCreate->endpoint = $this->endpoint;
        $requestCreate->requests = 1;
        $requestCreate->time = time() + $this->seconds;
        return $requestCreate->save();
    }

    /**
     * Updates request
     * @return bool
     */
    private function updateRequest(): bool {
        $this->request->time = time() + $this->seconds;
        $this->request->requests += 1;
        return $this->request->save();
    }

    /**
     * Refreshs requests counter to 1 
     * @return bool
     */
    private function refreshRequest(?object $request): bool {
        if ($request) {
            $request->requests = 1;
            $request->time = time() + $this->seconds;
            return $request->save();
        }
        
        return false;
    }

}
