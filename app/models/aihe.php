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

    public function save() {
        // Lisätään RETURNING id tietokantakyselymme loppuun, niin saamme lisätyn rivin id-sarakkeen arvon
        $query = DB::connection()->prepare('INSERT INTO Aihe (nimi, kuvaus) VALUES (:nimi, :kuvaus) RETURNING aihe_id');
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $row = $query->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
        $this->id = $row['aihe_id'];
    }

}
