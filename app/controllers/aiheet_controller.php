<?php

class AiheController extends BaseController {

    public static function listAll() {
        $aiheet = Aihe::all();
        View::make('aihe_listaus.html', array('aiheet' => $aiheet));
    }

    public static function aihemuokkaus($aihe_id) {
        $aihe = Aihe::find($aihe_id);
        View::make('aihe_muokkaus.html', array('aihe' => $aihe));
    }

    public static function aihe($aihe_id) {
        $aihe = Aihe::find($aihe_id);
        View::make('aihesivu.html', array('aihe' => $aihe));
    }

}
