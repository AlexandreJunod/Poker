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

CREATE TABLE IF NOT EXISTS Poker.Player (
  idPlayer INT(11) NOT NULL AUTO_INCREMENT,
  PseudoPlayer VARCHAR(45) NOT NULL,
  PasswordPlayer VARCHAR(45) NOT NULL,
  PRIMARY KEY (idPlayer),
  UNIQUE INDEX PseudoPlayer_UNIQUE (PseudoPlayer ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Seat (
  idSeat INT(11) NOT NULL AUTO_INCREMENT,
  MoneySeat INT(11) NULL DEFAULT NULL,
  BetSeat INT(11) NULL DEFAULT NULL,
  HandSeat VARCHAR(45) NULL DEFAULT NULL,
  OrderSeat INT(11) NULL DEFAULT NULL,
  fkGameSeat INT(11) NOT NULL,
  fkStatusSeat INT(11) NOT NULL,
  fkPlayerSeat INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (idSeat),
  INDEX fk_Seat_Game1_idx (fkGameSeat ASC),
  INDEX fk_Seat_Status1_idx (fkStatusSeat ASC),
  INDEX fk_Seat_Player1_idx (fkPlayerSeat ASC),
  UNIQUE INDEX OrderSeat_UNIQUE (OrderSeat ASC),
  UNIQUE INDEX fkPlayerSeat_UNIQUE (fkPlayerSeat ASC),
  CONSTRAINT fk_Seat_Game1
    FOREIGN KEY (fkGameSeat)
    REFERENCES Poker.Game (idGame)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_Seat_Status1
    FOREIGN KEY (fkStatusSeat)
    REFERENCES Poker.Status (idStatus)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_Seat_Player1
    FOREIGN KEY (fkPlayerSeat)
    REFERENCES Poker.Player (idPlayer)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Game (
  idGame INT(11) NOT NULL AUTO_INCREMENT,
  PotGame INT(11) NULL DEFAULT NULL,
  BoardGame VARCHAR(45) NULL DEFAULT NULL,
  BlindGame INT(11) NULL DEFAULT NULL,
  DealerGame INT(11) NOT NULL DEFAULT '0',
  HourStartGame TIME NULL DEFAULT NULL,
  PRIMARY KEY (idGame))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Status (
  idStatus INT(11) NOT NULL AUTO_INCREMENT,
  DescriptionStatus VARCHAR(45) NOT NULL,
  PRIMARY KEY (idStatus),
  UNIQUE INDEX DescriptionStatus_UNIQUE (DescriptionStatus ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

CREATE TABLE IF NOT EXISTS Poker.Settings (
  idSettings INT(11) NOT NULL AUTO_INCREMENT,
  NameSettings VARCHAR(45) NOT NULL,
  ValueInt INT(11) NULL DEFAULT NULL,
  ValueDate DATETIME NULL DEFAULT NULL,
  ValueChar VARCHAR(45) NULL DEFAULT NULL,
  PRIMARY KEY (idSettings))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

INSERT INTO poker.Game (PotGame, BlindGame) VALUES ('0', '3000');
INSERT INTO poker.Status (idStatus, DescriptionStatus) VALUES ('1', 'Waiting');
INSERT INTO poker.Status (idStatus, DescriptionStatus) VALUES ('2', 'In game');
INSERT INTO poker.Status (idStatus, DescriptionStatus) VALUES ('3', 'Playing');
INSERT INTO poker.Status (idStatus, DescriptionStatus) VALUES ('4', 'Bet a blind');
INSERT INTO poker.Status (idStatus, DescriptionStatus) VALUES ('99', 'Eliminated');
INSERT INTO poker.Seat (fkGameSeat, fkStatusSeat) VALUES ('1', '1');
INSERT INTO poker.Seat (fkGameSeat, fkStatusSeat) VALUES ('1', '1');
INSERT INTO poker.Seat (fkGameSeat, fkStatusSeat) VALUES ('1', '1');
INSERT INTO poker.Seat (fkGameSeat, fkStatusSeat) VALUES ('1', '1');
INSERT INTO poker.Seat (fkGameSeat, fkStatusSeat) VALUES ('1', '1');
INSERT INTO poker.Seat (fkGameSeat, fkStatusSeat) VALUES ('1', '1');
INSERT INTO poker.Settings (NameSettings, ValueInt) VALUES ('TimeToIncreaseBlind', '5');

-- ADD 9 Players, using for test
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre2', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre3', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre4', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre5', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre6', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre7', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre8', PASSWORD('Pa$$w0rd'));
INSERT INTO poker.Player (PseudoPlayer, PasswordPlayer) VALUES ('Alexandre9', PASSWORD('Pa$$w0rd'));

