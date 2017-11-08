CREATE TABLE Kategoria (
	kategoria_id SERIAL PRIMARY KEY,
	nimi varchar(100)
);

CREATE TABLE Aihe (
	aihe_id SERIAL PRIMARY KEY,
	nimi varchar(100),
	kuvaus varchar(2000)
);

CREATE TABLE KategoriaAihe (
	kategoria_id int REFERENCES Kategoria(kategoria_id),
	aihe_id int REFERENCES Aihe(aihe_id)
);

CREATE TABLE Käyttäjä (
	käyttäjä_id SERIAL PRIMARY KEY,
	nimi varchar(30),
	salasana varchar(30),
	asema varchar(30),
	aihe_id int REFERENCES Aihe(aihe_id)
);
	
