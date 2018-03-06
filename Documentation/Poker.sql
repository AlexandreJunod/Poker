DROP SCHEMA IF EXISTS poker;
CREATE SCHEMA poker ;

CREATE TABLE poker.joueurs(
idJoueur INT NOT NULL AUTO_INCREMENT,
PseudoJoueur VARCHAR(45) NOT NULL,
MotDePasseJoueur VARCHAR(45) NOT NULL,
PRIMARY KEY (idJoueur),
UNIQUE INDEX PseudoJoueur_UNIQUE (PseudoJoueur ASC));

INSERT INTO poker.joueurs (PseudoJoueur, MotDePasseJoueur)
VALUES ('alex', PASSWORD('password'));