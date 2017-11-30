<?php

// puuttuvat toiminnot: kategorian aiheiden muokkaus ja kategorian poistaminen

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
            $errors = $kategoria->errors();
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

}
