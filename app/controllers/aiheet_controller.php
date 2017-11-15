<?php

class AiheController extends BaseController {

    public static function listAll() {
        $aiheet = Aihe::all();
        View::make('aihe_listaus.html', array('aiheet' => $aiheet));
    }

    public static function naytaAihemuokkaus($aihe_id) {
        $aihe = Aihe::find($aihe_id);
        View::make('aihe_muokkaus.html', array('aihe' => $aihe));
    }
    public static function aihemuokkaus($aihe_id) {
        $aihe = Aihe::find($aihe_id);
        $parametrit = $_POST;
        $aihe->nimi = $parametrit['nimi'];
        $aihe->kuvaus = $parametrit['kuvaus'];
        $aihe->paivita();
         Redirect::to('/aihe/'.$aihe->aihe_id, array('viesti' => 'Aiheen muokkaus onnistui!'));
    }

    public static function aihe($aihe_id) {
        $aihe = Aihe::find($aihe_id);
        View::make('aihesivu.html', array('aihe' => $aihe));
    }
    
    public static function naytaAihelisays() {
        View::make('aihe_lisays.html');
    }

    public static function aihelisays() {
        $parametrit = $_POST;
        $aihe = new Aihe(Array(
            'nimi' => $parametrit['nimi'],
            'kuvaus' => $parametrit['kuvaus']
        ));
        $aihe->save();
        
        Redirect::to('/aihe/'.$aihe->aihe_id, array('viesti' => 'Aiheen lisäys onnistui!'));
    }

}
