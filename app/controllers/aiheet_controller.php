<?php

class AiheController extends BaseController {

    public static function listAll() {
        $aiheet = Aihe::all();
        View::make('aihe_listaus.html', array('aiheet' => $aiheet));
    }

    public static function naytaAihemuokkaus($aihe_id) {
        $aihe = Aihe::findById($aihe_id);
        View::make('aihe_muokkaus.html', array('aihe' => $aihe));
    }

    public static function aihemuokkaus($aihe_id) {
        $aihe = Aihe::findById($aihe_id);
        $parametrit = $_POST;
        $aihe->nimi = trim($parametrit['nimi']);
        $aihe->kuvaus = trim($parametrit['kuvaus']);
        $errors = $aihe->errors();
        if (count($errors) == 0) {
            $aihe->paivita();
            Redirect::to('/aihe/' . $aihe->aihe_id, array('viesti' => 'Aiheen muokkaus onnistui!'));
        } else {
            View::make('aihe_muokkaus.html', array('virheet' => $errors, 'aihe' => $aihe));
        }
    }

    public static function poista($aihe_id) {
        Kint::dump('poista kutsuttiin!');
        Aihe::delete($aihe_id);
        Redirect::to('/aiheet', array('viesti' => 'Aiheen poistaminen onnistui!'));
    }

    public static function aihe($aihe_id) {
        $aihe = Aihe::findById($aihe_id);
        View::make('aihesivu.html', array('aihe' => $aihe));
    }

    public static function naytaAihelisays() {
        View::make('aihe_lisays.html');
    }

    public static function aihelisays() {
        $parametrit = $_POST;
        $aihe = new Aihe(Array(
            'nimi' => trim($parametrit['nimi']),
            'kuvaus' => trim($parametrit['kuvaus'])
        ));
        $errors = $aihe->errors();
        if (count($errors) == 0) {
            $aihe->save();
            Redirect::to('/aihe/' . $aihe->aihe_id, array('viesti' => 'Aiheen lisäys onnistui!'));
        } else {
            View::make('aihe_lisays.html', array('virheet' => $errors, 'parametrit' => $parametrit));
        }
    }

}
