<?php

class User extends BaseModel {

    public $user_id, $name, $password, $asema;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function authenticate($username, $password) {
        $query = DB::connection()->prepare('SELECT * FROM Käyttäjä WHERE nimi = :nimi AND salasana = :salasana');
        $query->execute(array('nimi' => $username, 'salasana' => $password));
        $row = $query->fetch();

        if ($row) {
            $user = new User(array(
                'user_id' => $row['käyttäjä_id'],
                'name' => $row['nimi'],
                'password' => $row['salasana'],
                'asema' => $row['asema']
            ));
            return $user;
        }
        return null;
    }

    public static function find($user_id) {
        $query = DB::connection()->prepare('SELECT * FROM Käyttäjä WHERE käyttäjä_id = :id');
        $query->execute(array('id' => $user_id));
        $row = $query->fetch();

        if ($row) {
            $user = new User(array(
                'user_id' => $row['käyttäjä_id'],
                'name' => $row['nimi'],
                'password' => $row['salasana'],
                'asema' => $row['asema']
            ));
            return $user;
        }
        return null;
    }

}
