<?php

class KategoriaController extends BaseController {

    public static function listaaKaikkiKategoriat() {
        $user = self::get_user_logged_in();
        $kategoriat = Kategoria::all();
        $valittu = array();
        foreach ($kategoriat as $kategoria) {
            $aiheet = Kategoria::aiheet($kategoria->kategoria_id);
            $kategoriaaValittu = 0;
            foreach ($aiheet as $aihe) {
                $kategoriaaValittu = $kategoriaaValittu + Aihe::aihettaValittu($aihe->aihe_id);
            }
            $valittu[$kategoria->kategoria_id] = $kategoriaaValittu;
        }
        View::make('kategoria_listaus.html', array(
            'kategoriat' => $kategoriat,
            'user_logged_in' => $user,
            'valittu' => $valittu
        ));
    }

    public static function naytaKategoria($kategoria_id) {
        $params = $_GET;
        $user = self::get_user_logged_in();
        $kategoria = Kategoria::findById($kategoria_id);
        $aiheet = Kategoria::aiheet($kategoria_id);
        $kategoriaaValittu = 0;
        $aihettaValittu = array();
        foreach ($aiheet as $aihe) {
            $kategoriaaValittu = $kategoriaaValittu + ($aihettaValittu[$aihe->aihe_id] = Aihe::aihettaValittu($aihe->aihe_id));
        }
        $mista = null;
        if (isset($params['mista'])) {
            $mista = $params['mista'];
        }
        View::make('kategoriasivu.html', array(
            'kategoria' => $kategoria,
            'aiheet' => $aiheet,
            'user_logged_in' => $user,
            'kategoriaaValittu' => $kategoriaaValittu,
            'aihettaValittu' => $aihettaValittu,
            'mista' => $mista
        ));
    }

    public static function suoritaKategorianLisays() {
        self::check_logged_in('Kategorian lisäys');
        $user = self::get_user_logged_in();
        if ($user->asema == 'vastuuhenkilö') {
            $parametrit = $_POST;
            $kategoria = new Kategoria(Array(
                'nimi' => trim($parametrit['nimi']),
            ));
            $errors = array();
            $errors = array_merge($errors, $kategoria->validoiNimi());
            $errors = array_merge($errors, $kategoria->validoiUniikkius());
            if (count($errors) == 0) {
                $kategoria->save();
                Redirect::to('/kategoria/' . $kategoria->kategoria_id, array(
                    'viesti' => 'Kategorian lisäys onnistui!',
                    'mista' => 'kategoriat'
                ));
            } else {
                Redirect::to('/kategorialisays', array(
                    'virheet' => $errors, 'nimi' => $parametrit['nimi']));
            }
        }
        Redirect::to('/kategoriat', array('virheet' => array('Kategorian lisäys ei sallittu')));
    }

    public static function naytaKategorianLisays() {
        View::make('kategoria_lisays.html');
    }

    public static function suoritaKategorianMuokkaus($kategoria_id) {
        self::check_logged_in('Kategorian muokkaus');
        $user = self::get_user_logged_in();
        if ($user->asema == 'vastuuhenkilö') {
            $parametrit = $_POST;
            $muokattavaKategoria = Kategoria::findById($kategoria_id);
            $loydettyKategoria = Kategoria::findByName($parametrit['nimi']);
            self::asetaKategorianParametrit($muokattavaKategoria, $parametrit);
            $virheet = array();
            if (count($virhe = $muokattavaKategoria->validoiNimi()) != 0) {
                $virheet = array_merge($virheet, $virhe);
            }
            if (count($virhe = $muokattavaKategoria->validoiAiheet()) != 0) {
                $virheet = array_merge($virheet, $virhe);
            }
            if (!(is_null($loydettyKategoria) || $muokattavaKategoria->kategoria_id == $loydettyKategoria->kategoria_id)) {
                $virheet = array_merge($virheet, array("Virhe: nimellä löytyy toinen kategoria"));
            }

            if (count($virheet) == 0) {
                $muokattavaKategoria->paivita();
                Redirect::to('/kategoria/' . $muokattavaKategoria->kategoria_id, array(
                    'viesti' => 'Kategorian muokkaus onnistui!'
                ));
            } else {
                Redirect::to('/kategoriamuokkaus/' . $kategoria_id, array('virheet' => $virheet, 'kategoria' => $muokattavaKategoria));
            }
        }
        Redirect::to('/kategoriat', array('virheet' => array('Kategorian muokkaus ei sallittu')));
    }

    public static function naytaKategorianMuokkaus($kategoria_id) {
        $kategoria = Kategoria::findById($kategoria_id);
        $aiheet = Kategoria::aiheet($kategoria_id);
        $aiheet_id = array();
        foreach ($aiheet as $aihe) {
            $aiheet_id[] = $aihe->aihe_id;
        }
        $kategoria->aiheet = $aiheet_id;
        View::make('kategoria_muokkaus.html', array('kategoria' => $kategoria, 'aiheet' => Aihe::all()));
    }

    public static function poista($kategoria_id) {
        self::check_logged_in('Kategorian poistaminen');
        $user = self::get_user_logged_in();
        if ($user->asema == 'vastuuhenkilö') {
            Kategoria::delete($kategoria_id);
            Redirect::to('/kategoriat', array('viesti' => 'Kategorian poistaminen onnistui!'));
        }
        Redirect::to('/kategoria/' . $kategoria_id, array('virheet' => array('Virhe: kategorian poistaminen ei sallittu!')));
    }

    public static function asetaKategorianParametrit($kategoria, $parametrit) {
        if (isset($parametrit['nimi'])) {
            $kategoria->nimi = $parametrit['nimi'];
        }
        if (isset($parametrit['aiheet'])) {
            $aiheet = $parametrit['aiheet'];
            $kategoria->aiheet = $parametrit['aiheet'];
        }
    }

}
