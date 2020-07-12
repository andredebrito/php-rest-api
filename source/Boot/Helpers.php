<?php

/**
 * ####################
 * ###   VALIDATE   ###
 * ####################
 */

/** Checks if is a valid email
 * @param string $email
 * @return bool
 */
function is_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/** Checks if is a valid password
 * @param string $password
 * @return bool
 */
function is_passwd(string $password): bool {
    if (password_get_info($password)['algo']) {
        return true;
    }

    return (mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN ? true : false);
}

/**
 * Applies FILTER_SANITIZE_STRIPPED and trim
 * 
 * @param array $data
 * @return array
 */
function filter_array(array $data): Array {
    $data = filter_var_array($data, FILTER_SANITIZE_STRIPPED);
    $data = array_map('trim', $data);

    return $data;
}


/**
 * ###############
 * ###   URL   ###
 * ###############
 */

/**
 * @param string|null $uri
 * @return string
 */
function url(string $uri = null): string {
    if ($uri) {
        return ROOT . "/{$uri}";
    }

    return ROOT;
}


/**
 * ####################
 * ###   ASSETS   #####
 * ####################
 */

/**
 * 
 * @return \Source\Models\UserModel
 */
function user(): \Source\Models\UserModel {
    return new Source\Models\UserModel;
}

/**
 * 
 * @return \Source\Models\UserTokenModel
 */
function token(): \Source\Models\UserTokenModel{
    return new Source\Models\UserTokenModel;
}

/**
 * 
 * @return \Source\Models\DrinkModel
 */
function drink(): \Source\Models\DrinkModel{
    return new \Source\Models\DrinkModel();
}


/**
 * ####################
 * ###   PASSWORD   ###
 * ####################
 */

/**
 * @param string $password
 * @return string
 */
function passwd(string $password): string {
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }

    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool {
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool {
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

