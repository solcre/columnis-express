<?php

namespace ColumnisExpress\Adapters;

use ZF\OAuth2\Adapter\PdoAdapter;

class ApiAdapter extends PdoAdapter {

    const USER_TABLE = 'usuarios';

    public function getUser($username) {
        $stmt = $this->db->prepare($sql = sprintf('SELECT * from %s where usuario=:usuario', self::USER_TABLE));
        $stmt->execute(array('usuario' => $username));


        if(!$userInfo = $stmt->fetch()) {
            return false;
        }

        // the default behavior is to use "username" as the user_id
        $result = array_merge(array(
            'user_id' => $username
                ), $userInfo);

        return $result;
    }

    /**
     * Check password using bcrypt
     *
     * @param string $user
     * @param string $password
     * @return bool
     */
    protected function checkPassword($user, $password) {
        return $this->verifyHash($password, $user['clave']);
    }

    /**
     * Set the user
     *
     * @param string $username
     * @param string $password
     * @param string $firstName
     * @param string $lastName
     * @return bool
     */
    public function setUser($username, $password, $firstName = null, $lastName = null) {
        // do not store in plaintext, use bcrypt
        $this->createBcryptHash($password);

        // if it exists, update it.
        if($this->getUser($username)) {
            $stmt = $this->db->prepare(sprintf(
                            'UPDATE %s SET clave=:clave where usuario=:usuario', self::USER_TABLE
            ));
        }
        else {
            $stmt = $this->db->prepare(sprintf(
                            'INSERT INTO %s (usuario, clave) VALUES (:usuario, :clave)', self::USER_TABLE
            ));
        }

        return $stmt->execute(compact('usuario', 'clave'));
    }
}

?>