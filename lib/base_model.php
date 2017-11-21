<?php

class BaseModel {

    // "protected"-attribuutti on käytössä vain luokan ja sen perivien luokkien sisällä
    protected $validators;

    public function __construct($attributes = null) {
        // Käydään assosiaatiolistan avaimet läpi
        foreach ($attributes as $attribute => $value) {
            // Jos avaimen niminen attribuutti on olemassa...
            if (property_exists($this, $attribute)) {
                // ... lisätään avaimen nimiseen attribuuttin siihen liittyvä arvo
                $this->{$attribute} = $value;
            }
        }
    }

    public function errors() {
        // Lisätään $errors muuttujaan kaikki virheilmoitukset taulukkona
        $errors = array();

        foreach ($this->validators as $validator) {
            // Kutsu validointimetodia tässä ja lisää sen palauttamat virheet errors-taulukkoon
            $errorValidator = $this->{$validator}();
            $errors = array_merge($errors, $errorValidator);
        }
        return $errors;
    }

    public static function validoiMerkkijononPituus($merkkijono, $pituus, $nimi) {
        $errors = array();
        if (strlen($merkkijono) < $pituus) {
            $errors[] = $nimi.' on tyhjä tai liian lyhyt';
        }
        return $errors;
    }

}
