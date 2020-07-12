<?php

namespace Source\Core;

use Source\Support\Message;

/**
 * Class Controller
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Core
 */
abstract class Controller {

    /** @var Message */
    protected $message;

    /**
     * Controller constructor
     * @param string|null $pathToViews
     */
    public function __construct() {
        $this->message = new Message;
    }

}
