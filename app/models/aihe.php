<?php

class Aihe extends BaseModel {

    public $aihe_id, $nimi, $kuvaus, $kayttaja, $kategoriat;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi', 'validoiKuvaus', 'validoiUniikkius', 'validoiKategoriat');
    }

    public function validoiNimi() {
        // aiheen nimen pituuden tulee olla vähintään 3
        return $this->validoiMerkkijononPituus($this->nimi, 3, 100, 'nimi');
    }

    public function validoiKuvaus() {
        // kuvaus saa olla tyhjä, mutta epätyhjänä pituuden tulee olla väh 3
        $errors = array();
        if (strlen($this->kuvaus) == 0) {
            return $errors;
        }
        return $this->validoiMerkkijononPituus($this->kuvaus, 3, 2000, 'kuvaus');
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
            $id = $row['aihe_id'];
            $aiheet[] = new Aihe(array(
                'aihe_id' => $id,
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
        }

        return $aiheet;
    }

    public function validoiKategoriat() {
        $errors = array();
        if (!is_null($this->kategoriat)  && in_array('tyhja', $this->kategoriat) 
                && count($this->kategoriat) > 1) {
            $errors[] = 'Virhe: valitsit ei mitään ja kategorian samaan aikaan';
        }
        return $errors;
    }

    public static function findById($aihe_id) {
        $query = DB::connection()->prepare('SELECT * FROM Aihe WHERE aihe_id = :aihe_id LIMIT 1');
        $query->execute(array('aihe_id' => $aihe_id));
        $row = $query->fetch();

        if ($row) {
            return new Aihe(array(
                'aihe_id' => $row['aihe_id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus']
            ));
        }
        return null;
    }

    public static function findByName($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Aihe WHERE LOWER(nimi) = :nimi');
        $query->execute(array('nimi' => strtolower($nimi)));
        $row = $query->fetch();
        if ($row) {
            return new Aihe(array(
                'aihe_id' => $row['aihe_id'],
                'nimi' => $row['nimi'],
                'kuvaus' => $row['kuvaus'],
            ));
        }
        return null;
    }

    public function save() {
        $query = DB::connection()->prepare('INSERT INTO Aihe (nimi, kuvaus)'
                . ' VALUES (:nimi, :kuvaus)'
                . ' RETURNING aihe_id');
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
        $row = $query->fetch();
        $this->aihe_id = $row['aihe_id'];

        // jos aiheelle on lisätty kategoriat
        if ($this->kategoriat) {
            $this->tallennaKategoriat();
        }
        // jos aiheelle on lisätty käyttäjä 
        if ($this->kayttaja) {
            $this->tallennaKayttaja();
        }
    }

    public function tallennaKategoriat() {
        $query = DB::connection()->prepare('INSERT INTO KategoriaAihe'
                . ' VALUES(:kategoria_id, :aihe_id)');
        foreach ($this->kategoriat as $kategoria_id) {
            $query->execute(array(
                'kategoria_id' => $kategoria_id,
                'aihe_id' => $this->aihe_id
            ));
        }
    }

    public function tallennaKayttaja() {
        $kayttaja_id = $this->user_id;
        $query = DB::connection()->prepare(
                'UPDATE Käyttäjä SET aihe_id = :aihe_id'
                . ' WHERE käyttäjä_id = :id');
        $query->execute(array(
            'aihe_id' => $this->aihe_id,
            'id' => $kayttaja_id
        ));
    }

    public function paivita() {
        // päivitetään aiheen nimi ja kuvaus
        $query = DB::connection()->prepare('UPDATE Aihe SET nimi = :nimi, kuvaus = :kuvaus WHERE aihe_id = ' . $this->aihe_id);
        $query->execute(array('nimi' => $this->nimi, 'kuvaus' => $this->kuvaus));
        // päivitetään aiheen kategoriat!
        $query = DB::connection()->prepare('DELETE FROM KategoriaAihe'
                . ' WHERE aihe_id = :id');
        $query->execute(array('id' => $this->aihe_id));

        $query = DB::connection()->prepare('INSERT INTO KategoriaAihe'
                . ' VALUES(:kategoria_id, :aihe_id)');
        if ($this->kategoriat) {
            foreach ($this->kategoriat as $kategoria_id) {
                $query->execute(array(
                    'kategoria_id' => $kategoria_id,
                    'aihe_id' => $this->aihe_id
                ));
            }
        }
    }

    public function getValidators() {
        return $this->validators;
    }

    public static function delete($aihe_id) {
        // poistetaan ensin aiheen esiintymät KategoriaAihe-taulusta
        $query = DB::connection()->prepare('DELETE FROM KategoriaAihe'
                . ' WHERE aihe_id = :id');
        $query->execute(array('id' => $aihe_id));
        // sitten pitää poistaa aiheen valinnat käyttäjiltä
        $query = DB::connection()->prepare('UPDATE Käyttäjä SET aihe_id = NULL '
                . 'WHERE aihe_id = :id');
        $query->execute(array('id' => $aihe_id));
        // tehdään sitten poisto Aihe-taulusta
        $query = DB::connection()->prepare('DELETE FROM Aihe WHERE aihe_id = :id');
        $query->execute(array('id' => $aihe_id));
    }

    public static function aiheitaValittuYhteensa() {
        $query = DB::connection()->query('SELECT COUNT(*) AS valittu_yht FROM Käyttäjä'
                . ' WHERE aihe_id IS NOT NULL');
        $query->execute();
        $row = $query->fetch();
        return $row['valittu_yht'];
    }

    public static function aihettaValittu($aihe_id) {
        $query = DB::connection()->prepare('SELECT COUNT(*) AS valittu FROM Käyttäjä'
                . ' WHERE aihe_id = :aihe_id');
        $query->execute(array('aihe_id' => $aihe_id));
        $row = $query->fetch();
        return $row['valittu'];
    }

    public static function kategoriat($aihe_id) {
        $query = DB::connection()->prepare(
                'SELECT Kategoria.kategoria_id AS id FROM Aihe, Kategoria, KategoriaAihe'
                . ' WHERE Kategoria.kategoria_id = KategoriaAihe.kategoria_id'
                . ' AND Aihe.aihe_id = KategoriaAihe.aihe_id'
                . ' AND Aihe.aihe_id = :id');
        $query->execute(array('id' => $aihe_id));
        $row = $query->fetch();
        $kategoriat = array();
        while ($row) {
            $kategoriat[] = Kategoria::findById($row['id']);
            $row = $query->fetch();
        }
        return $kategoriat;
    }

    public static function tyhjennaValinnat() {
        $query = DB::connection()->query(
                'UPDATE Käyttäjä SET aihe_id = NULL WHERE 1=1');
        $query->execute();
    }

}
