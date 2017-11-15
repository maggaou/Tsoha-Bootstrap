<?php

class Aihe extends BaseModel {

    public $aihe_id, $nimi, $kuvaus;

    public function __construct($attributes) {
        parent::__construct($attributes);
    }

    public static function all() {
        // Alustetaan kysely tietokantayhteydellämme
        $query = DB::connection()->prepare('SELECT * FROM Aihe');
        // Suoritetaan kysely
        $query->execute();
        // Haetaan kyselyn tuottamat rivit
        $rows = $query->fetchAll();
        $aiheet = array();

        foreach ($rows as $row) {
            $aiheet[] = new Aihe(array(
                'aihe_id' => $row['aihe_id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
        }

        return $aiheet;
    }

    public static function find($aihe_id) {
        $query = DB::connection()->prepare('SELECT * FROM Aihe WHERE aihe_id = :aihe_id LIMIT 1');
        $query->execute(array('aihe_id' => $aihe_id));
        $row = $query->fetch();

        if ($row) {
            $aihe = new Aihe(array(
                'aihe_id' => $row['aihe_id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
            ));

            return $aihe;
        }
        return null;
    }
    
    public static function save($nimi, $kuvaus) {
        
    }

}
