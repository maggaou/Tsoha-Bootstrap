<?php

class Aihe extends BaseModel {

    public $aihe_id, $nimi, $kuvaus;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi', 'validoiKuvaus', 'validoiUniikkius');
    }

    public function validoiNimi() {
        // aiheen nimen pituuden tulee olla vähintään 3
        return $this->validoiMerkkijononPituus($this->nimi, 3, 'nimi');
    }

    public function validoiKuvaus() {
        // kuvaus saa olla tyhjä, mutta epätyhjänä pituuden tulee olla väh 3
        $errors = array();
        if (strlen($this->kuvaus) == 0) {
            return $errors;
        }
        return $this->validoiMerkkijononPituus($this->kuvaus, 3, 'kuvaus');
    }

    public function validoiUniikkius() {
        // jos aihe on tietokannassa valmiiksi
        $errors = array();
        if ($this->aihe_id) {
            if (count(self::findByName($this->nimi)) > 1) {
                $errors[] = 'Virhe: samalla nimellä löytyy jo aihe';
            }
            return $errors;
        } else {
            if (count(self::findByName($this->nimi)) > 0) {
                $errors[] = 'Virhe: samalla nimellä löytyy jo aihe';
            }
            return $errors;
        }
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

    public static function findById($aihe_id) {
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

    public static function findByName($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Aihe WHERE LOWER(nimi) = :nimi');
        $query->execute(array('nimi' => strtolower($nimi)));
        $row = $query->fetch();

        $aiheet = array();
        while ($row) {
            $aihe = new Aihe(array(
                'aihe_id' => $row['aihe_id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
            ));
            $aiheet[] = $aihe;
            $row = $query->fetch();
        }
        return $aiheet;
    }

    public function save() {
        // Lisätään RETURNING id tietokantakyselymme loppuun, niin saamme lisätyn rivin id-sarakkeen arvon
        $query = DB::connection()->prepare('INSERT INTO Aihe (nimi, kuvaus) VALUES (:nimi, :kuvaus) RETURNING aihe_id');
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $row = $query->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
        $this->aihe_id = $row['aihe_id'];
    }

    public function paivita() {
        $query = DB::connection()->prepare('UPDATE Aihe SET nimi = :nimi, kuvaus = :kuvaus WHERE aihe_id = ' . $this->aihe_id);
        // Muistathan, että olion attribuuttiin pääse syntaksilla $this->attribuutin_nimi
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
    }

    public function getValidators() {
        return $this->validators;
    }

    public static function delete($aihe_id) {
        Kint::dump('aiheen id on '.$aihe_id);
        $query = DB::connection()->prepare('DELETE FROM Aihe WHERE aihe_id = :id');
        $query->execute(array('id' => $aihe_id));
    }

}
