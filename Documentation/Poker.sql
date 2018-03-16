-- MySQL Workbench Synchronization
-- Generated: 2018-03-08 16:18
-- Model: New Model
-- Version: 1.0
-- Project: Name of the project
-- Author: Alexandre.JUNOD

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


DROP SCHEMA IF EXISTS poker;
CREATE SCHEMA poker ;

CREATE TABLE IF NOT EXISTS Poker.Joueur (
  idJoueur INT(11) NOT NULL AUTO_INCREMENT,
  PseudoJoueur VARCHAR(45) NOT NULL,
  MotDePasseJoueur VARCHAR(45) NOT NULL,
  PRIMARY KEY (idJoueur),
  UNIQUE INDEX PseudoJoueur_UNIQUE (PseudoJoueur ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Siege (
  idSiege INT(11) NOT NULL AUTO_INCREMENT,
  ArgentSiege INT(11) NULL DEFAULT NULL,
  MainSiege VARCHAR(45) NULL DEFAULT NULL,
  fkPartieSiege INT(11) NOT NULL,
  fkEtatSiege INT(11) NOT NULL,
  fkJoueurSiege INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (idSiege),
  INDEX fk_Siege_Partie1_idx (fkPartieSiege ASC),
  INDEX fk_Siege_Etat1_idx (fkEtatSiege ASC),
  INDEX fk_Siege_Joueur1_idx (fkJoueurSiege ASC),
  UNIQUE INDEX fkJoueurSiege_UNIQUE (fkJoueurSiege ASC),
  CONSTRAINT fk_Siege_Partie1
    FOREIGN KEY (fkPartieSiege)
    REFERENCES Poker.Partie (idPartie)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_Siege_Etat1
    FOREIGN KEY (fkEtatSiege)
    REFERENCES Poker.Etat (idEtat)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_Siege_Joueur1
    FOREIGN KEY (fkJoueurSiege)
    REFERENCES Poker.Joueur (idJoueur)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Partie (
  idPartie INT(11) NOT NULL AUTO_INCREMENT,
  PotPartie INT(11) NULL DEFAULT NULL,
  BoardPartie VARCHAR(45) NULL DEFAULT NULL,
  BlindPartie INT(11) NULL DEFAULT NULL,
  MainPartie INT(11) NULL DEFAULT NULL,
  HeureDebutPartie TIME NULL DEFAULT NULL,
  PRIMARY KEY (idPartie))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Etat (
  idEtat INT(11) NOT NULL AUTO_INCREMENT,
  DescriptionEtat VARCHAR(45) NOT NULL,
  PRIMARY KEY (idEtat),
  UNIQUE INDEX DescriptionEtat_UNIQUE (DescriptionEtat ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Parametres (
  idParametres INT(11) NOT NULL AUTO_INCREMENT,
  NomParametres VARCHAR(45) NOT NULL,
  ValeurInt INT(11) NULL DEFAULT NULL,
  ValeurDate DATETIME NULL DEFAULT NULL,
  ValeurChar VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (idParametres))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO poker.partie (PotPartie, BlindPartie) VALUES ('0', '3000');
INSERT INTO poker.etat (DescriptionEtat) VALUES ('En attente');
INSERT INTO poker.etat (DescriptionEtat) VALUES ('En jeu');
INSERT INTO poker.etat (DescriptionEtat) VALUES ('Doit jouer');
INSERT INTO poker.siege (fkPartieSiege, fkEtatSiege) VALUES ('1', '1');
INSERT INTO poker.siege (fkPartieSiege, fkEtatSiege) VALUES ('1', '1');
INSERT INTO poker.siege (fkPartieSiege, fkEtatSiege) VALUES ('1', '1');
INSERT INTO poker.siege (fkPartieSiege, fkEtatSiege) VALUES ('1', '1');
INSERT INTO poker.siege (fkPartieSiege, fkEtatSiege) VALUES ('1', '1');
INSERT INTO poker.siege (fkPartieSiege, fkEtatSiege) VALUES ('1', '1');
INSERT INTO poker.parametres (NomParametres, ValeurInt) VALUES ('TempsAugmentationBlind', '5');

-- Ajout de 9 joueurs, afin de pouvoir tester sans avoir a recréer à chaque fois les joueurs
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre2', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre3', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre4', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre5', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre6', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre7', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre8', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.joueur (PseudoJoueur, MotDePasseJoueur) VALUES ('Alexandre9', PASSWORD('Pa$$w0rd'));

