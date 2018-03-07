-- Generated: 2018-03-07 10:48
-- Model: Poker
-- Version: 1.0
-- Project: Poker online
-- Author: Alexandre Junod

DROP SCHEMA IF EXISTS poker;
CREATE SCHEMA poker ;

CREATE TABLE poker.Etat(
	idEtat INT(11) NOT NULL AUTO_INCREMENT,
	DescriptionEtat VARCHAR(45) NOT NULL,
	PRIMARY KEY (idEtat),
	UNIQUE INDEX DescriptionEtat_UNIQUE (DescriptionEtat ASC))
    ENGINE=INNODB;

CREATE TABLE IF NOT EXISTS poker.Page (
idPage INT(11) NOT NULL AUTO_INCREMENT,
DescriptionPage VARCHAR(45) NOT NULL,
PRIMARY KEY (idPage),
UNIQUE INDEX DescriptionPage_UNIQUE (DescriptionPage ASC))
ENGINE=INNODB;

CREATE TABLE poker.Joueur(
	idJoueur INT(11) NOT NULL AUTO_INCREMENT,
	PseudoJoueur VARCHAR(45) NOT NULL,
	MotDePasseJoueur VARCHAR(45) NOT NULL,
	fkEtatJoueur INT(11) NOT NULL,
	fkPageJoueur INT(11) NOT NULL,
	PRIMARY KEY (idJoueur),
	UNIQUE INDEX PseudoJoueur_UNIQUE (PseudoJoueur ASC),
	INDEX fk_Joueur_Etat_idx (fkEtatJoueur ASC),
	INDEX fk_Joueur_Page1_idx (fkPageJoueur ASC),
	CONSTRAINT fk_Joueur_Etat
		FOREIGN KEY (fkEtatJoueur)
		REFERENCES poker.Etat (idEtat)
		ON DELETE NO ACTION
		ON UPDATE NO ACTION,
	CONSTRAINT fk_Joueur_Page1
		FOREIGN KEY (fkPageJoueur)
		REFERENCES poker.Page (idPage)
		ON DELETE NO ACTION
		ON UPDATE NO ACTION)
        ENGINE=INNODB;