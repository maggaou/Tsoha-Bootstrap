<?php

// puuttuvat toiminnot: aiheen kunnollinen muokkaus ja poisto
class AiheController extends BaseController {

    public static function listaaKaikkiAiheet() {
        $user = self::get_user_logged_in();
        $aiheet = Aihe::all();
        $valittu = array();
        foreach ($aiheet as $aihe) {
            $valittu[$aihe->aihe_id] = Aihe::aihettaValittu($aihe->aihe_id);
        }
        View::make('aihe_listaus.html', array(
            'aiheet' => $aiheet,
            'user_logged_in' => $user,
            'valittu' => $valittu
        ));
    }

    // tämä metodi on testausta varten!
    public static function tyhjennaKaikkiAiheValinnat() {
        Aihe::tyhjennaValinnat();
        Redirect::to('/', array('viesti' => 'Aihevalintojen tyhjennys onnistui!'));
    }

    public static function naytaAiheenMuokkaus($aihe_id) {
        $aihe = Aihe::findById($aihe_id);
        $kategoriat = Aihe::kategoriat($aihe_id);
        $kategoriat_id = array();
        foreach ($kategoriat as $kategoria) {
            $kategoriat_id[] = $kategoria->kategoria_id;
        }
        $aihe->kategoriat = $kategoriat_id;
        View::make('aihe_muokkaus.html', array('aihe' => $aihe, 'categories' => Kategoria::all()));
    }

    public static function suoritaAiheenMuokkaus($aihe_id) {
        self::check_logged_in('Aiheen muokkaus');
        $user = self::get_user_logged_in();
        if ($user->asema == 'vastuuhenkilö') {
            $parametrit = $_POST;
            $muokattavaAihe = Aihe::findById($aihe_id);
            self::asetaAiheenParametrit($muokattavaAihe, $parametrit);
            $loydettyAihe = Aihe::findByName($parametrit['nimi']);
            $virheet = array();
            $virheet = array_merge($virheet, $muokattavaAihe->validoiNimi());
            $virheet = array_merge($virheet, $muokattavaAihe->validoiKuvaus());
            $virheet = array_merge($virheet, $muokattavaAihe->validoiKategoriat());
            if (!(is_null($loydettyAihe) || $loydettyAihe->aihe_id == $muokattavaAihe->aihe_id)) {
                $virheet = array_merge($virheet, array("Virhe: nimellä löytyy toinen aihe"));
            }
            if (count($virheet) == 0) {
                $muokattavaAihe->paivita();
                Redirect::to('/aihe/' . $muokattavaAihe->aihe_id, array('viesti' => 'Aiheen muokkaus onnistui!', 'mista' => 'aiheet'));
            } else {
                View::make('aihe_muokkaus.html', array(
                    'virheet' => $virheet,
                    'aihe' => $muokattavaAihe,
                    'categories' => Kategoria::all()
                ));
            }
            Redirect::to('/aihe/' . $aihe_id, array('virheet' => array('Virhe: aiheen muokkaus ei sallittu!')));
        }
    }

    public static function asetaAiheenParametrit($aihe, $parametrit) {
        if (isset($parametrit['nimi'])) {
            $aihe->nimi = trim($parametrit['nimi']);
        }
        if (isset($parametrit['kuvaus'])) {
            $aihe->kuvaus = trim($parametrit['kuvaus']);
        }
        if (isset($parametrit['categories'])) {
            $aihe->kategoriat = $parametrit['categories'];
        } 
    }

    public static function poista($aihe_id) {
        self::check_logged_in('Aiheen poistaminen');
        $user = self::get_user_logged_in();
        if ($user->asema == 'vastuuhenkilö') {
            Aihe::delete($aihe_id);
            Redirect::to('/aiheet', array('viesti' => 'Aiheen poistaminen onnistui!'));
        }
        Redirect::to('/aihe/' . $aihe_id, array('virheet' => array('Virhe: aiheen poistaminen ei sallittu!')));
    }

    public static function naytaAihe($aihe_id) {
        $params = $_GET;
        $user = self::get_user_logged_in();
        $aihe = Aihe::findById($aihe_id);
        $kategoriat = Aihe::kategoriat($aihe_id);

        // selvitetään valinnat kategorioittain
        $valittuKategoriassa = array();
        foreach ($kategoriat as $kategoria) {
            $valittu = 0;
            foreach (Kategoria::aiheet($kategoria->kategoria_id) as $kategorianAihe) {
                $valittu = $valittu + Aihe::aihettaValittu($kategorianAihe->aihe_id);
            }
            $valittuKategoriassa[$kategoria->kategoria_id] = $valittu;
        }
        $mista = null;
        if (isset($params['mista'])) {
            $mista = $params['mista'];
        }
        View::make('aihesivu.html', array(
            'aihe' => $aihe,
            'user_logged_in' => $user,
            'kategoriat' => $kategoriat,
            'valittu' => Aihe::aihettaValittu($aihe_id),
            'mista' => $mista,
            'valittuKategoriassa' => $valittuKategoriassa
        ));
    }

    public static function naytaAiheenLisays() {
        View::make('aihe_lisays.html', array(
            'categories' => Kategoria::all()
        ));
    }

    public static function suoritaAiheenLisays() {
        self::check_logged_in('Aiheen lisäys');
        $user = self::get_user_logged_in();
        if ($user->asema == 'vastuuhenkilö') {
            $parametrit = $_POST;
            $kategoriat = null;
            if (isset($parametrit['categories'])) {
                $kategoriat = $parametrit['categories'];
            }
            $aihe = new Aihe(Array(
                'nimi' => trim($parametrit['nimi']),
                'kuvaus' => trim($parametrit['kuvaus']),
                'kategoriat' => $kategoriat
            ));
            $errors = $aihe->errors();
            if (count($errors) == 0) {
                $aihe->save();
                Redirect::to('/aihe/' . $aihe->aihe_id, array(
                    'viesti' => 'Aiheen lisäys onnistui!',
                    'mista' => 'aiheet'
                ));
            } else {
                Redirect::to('/aihelisays', array(
                    'virheet' => $errors,
                    'parametrit' => $parametrit,
                ));
            }
        }
        Redirect::to('/aiheet', array('virheet' => array('Aiheen lisäys ei sallittu')));
    }

    public static function suoritaAihevalinta($aihe_id) {
        self::check_logged_in('Aiheen valinta');
        $kukaValitsee = self::get_user_logged_in();
        // käyttäjä voi valita aiheen korkeintaan kerran
        if ($kukaValitsee->aihe) {
            Redirect::to('/aihe/' . $aihe_id, array('virheet' => array('Olet jo valinnut aiheen!')));
        } else {
            $aihe = Aihe::findById($aihe_id);
            $kukaValitsee->lisaaAihe($aihe);
            Redirect::to('/aihe/' . $aihe_id, array('viesti' => 'Aiheen valinta onnistui!'));
        }
    }

}
