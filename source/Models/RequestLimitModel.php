<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Class RequestLimitModel [MODEL]
 *
 * @author André de Brito <andrebrito1990@gmail.com>
 * @package Source\Models
 */
class RequestLimitModel extends Model {

    public function __construct() {
        parent::__construct("request_limit", ["request_identifier", "endpoint", "requests", "time"]);
    }
    

}
