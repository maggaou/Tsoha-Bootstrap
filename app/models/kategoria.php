<?php

class Kategoria extends BaseModel {

    public $kategoria_id, $nimi, $aiheet;

    public function __construct($attributes) {
        parent::__construct($attributes);
        $this->validators = array('validoiNimi', 'validoiUniikkius');
    }

    public function validoiNimi() {
        // kategorian nimen pituuden tulee olla vähintään 3
        return $this->validoiMerkkijononPituus($this->nimi, 3, 'nimi');
    }

    public function validoiUniikkius() {
        // jos aihe on tietokannassa valmiiksi
        $errors = array();
        if ($this->kategoria_id) {
            if (count(self::findByName($this->nimi)) > 1) {
                $errors[] = 'Virhe: samalla nimellä löytyy jo kategoria';
            }
            return $errors;
        } else {
            if (count(self::findByName($this->nimi)) > 0) {
                $errors[] = 'Virhe: samalla nimellä löytyy jo kategoria';
            }
            return $errors;
        }
    }

    public static function all() {
        // Alustetaan kysely tietokantayhteydellämme
        $query = DB::connection()->prepare('SELECT * FROM Kategoria');
        // Suoritetaan kysely
        $query->execute();
        // Haetaan kyselyn tuottamat rivit
        $rows = $query->fetchAll();
        $kategoriat = array();

        foreach ($rows as $row) {
            $id = $row['kategoria_id'];
            $nimi = $row['nimi'];
            $kategoria = new Kategoria(array(
                'kategoria_id' => $id,
                'nimi' => $nimi
            ));
            $aiheet = Kategoria::aiheet($id);
            $kategoria->aiheet = $aiheet;
            $kategoriat[] = $kategoria;
        }
        return $kategoriat;
    }

    public static function findById($kategoria_id) {
        $query = DB::connection()->prepare(
                'SELECT * FROM Kategoria WHERE kategoria_id = :kategoria_id LIMIT 1');
        $query->execute(array('kategoria_id' => $kategoria_id));
        $row = $query->fetch();

        if ($row) {
            $kategoria = new Kategoria(array(
                'kategoria_id' => $row['kategoria_id'],
                'nimi' => $row['nimi'],
            ));
            $aiheet = Kategoria::aiheet($kategoria_id);
            $kategoria->aiheet = $aiheet;
            return $kategoria;
        }
        return null;
    }

    public static function findByName($nimi) {
        $query = DB::connection()->prepare('SELECT * FROM Kategoria WHERE LOWER(nimi) = :nimi');
        $query->execute(array('nimi' => strtolower($nimi)));
        $row = $query->fetch();

        $kategoriat = array();
        while ($row) {
            $kategoria = new Aihe(array(
                'kategoria_id' => $row['kategoria_id'],
                'nimi' => $row['nimi'],
            ));
            $kategoriat[] = $kategoria;
            $row = $query->fetch();
        }
        return $kategoriat;
    }

    public function save() {
        // Lisätään RETURNING id tietokantakyselymme loppuun, 
        // niin saamme lisätyn rivin id-sarakkeen arvon
        $query = DB::connection()->prepare(
                'INSERT INTO Kategoria (nimi) '
                . 'VALUES (:nimi) RETURNING kategoria_id');
        $query->execute(array('nimi' => $this->nimi));
        // Haetaan kyselyn tuottama rivi, joka sisältää lisätyn rivin id-sarakkeen arvon
        $row = $query->fetch();
        // Asetetaan lisätyn rivin id-sarakkeen arvo oliomme id-attribuutin arvoksi
        $this->kategoria_id = $row['kategoria_id'];
    }

    public function paivita() {
        $query = DB::connection()->prepare(
                'UPDATE Kategoria SET nimi = :nimi WHERE kategoria_id = :kategoria_id');
        $query->execute(array('nimi' => $this->nimi, 'kategoria_id' => $this->kategoria_id));
    }

    public function getValidators() {
        return $this->validators;
    }

    public static function delete($kategoria_id) {
        // poistetaan esiintymät KategoriaAihe-taulusta
        $query = DB::connection()->prepare('DELETE FROM KategoriaAihe'
                . ' WHERE kategoria_id = :id');
        $query->execute(array('id' => $kategoria_id));
        // poistetaan sitten Kategoria-taulusta
        $query = DB::connection()->prepare('DELETE FROM Kategoria WHERE kategoria_id = :id');
        $query->execute(array('id' => $kategoria_id));
    }

    public static function aiheet($kategoria_id) {
        $query = DB::connection()->prepare(
                'SELECT Aihe.aihe_id AS id FROM Aihe, Kategoria, KategoriaAihe'
                . ' WHERE Kategoria.kategoria_id = KategoriaAihe.kategoria_id'
                . ' AND Aihe.aihe_id = KategoriaAihe.aihe_id'
                . ' AND Kategoria.kategoria_id = :id');
        $query->execute(array('id' => $kategoria_id));
        $row = $query->fetch();
        $aiheet = array();
        while ($row) {
            $aiheet[] = Aihe::findById($row['id']);
            $row = $query->fetch();
        }
        return $aiheet;
    }

}
