<?php

namespace Source\Core;

use CoffeeCode\DataLayer\DataLayer;
use Source\Support\Message;

/**
 * Class Controller
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Core
 */
abstract class Model extends DataLayer {

    /** @var Message */
    protected $message;

    public function __construct(string $entity, array $required, string $primary = 'id', bool $timestamps = true) {
        parent::__construct($entity, $required, $primary, $timestamps);
        $this->message = new Message;
    }

    /**
     * @return Message|null
     */
    public function message(): ?Message {
        return $this->message;
    }  

}
