<?php

class User extends BaseModel {

    public $user_id, $name, $password, $asema, $aihe;

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
                'asema' => $row['asema'],
                'aihe' => Aihe::findById($row['aihe_id'])
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
                'asema' => $row['asema'],
                'aihe' => Aihe::findById($row['aihe_id'])
            ));
            return $user;
        }
        return null;
    }

    public function lisaaAihe($aihe) {
        $query = DB::connection()->prepare(
                'UPDATE Käyttäjä SET aihe_id = :aihe_id'
                . ' WHERE käyttäjä_id = :id');
        $query->execute(array(
            'id' => $this->user_id,
            'aihe_id' => $aihe->aihe_id
        ));
    }

}
