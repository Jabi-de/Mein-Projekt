CREATE DATABASE checkliste;

USE checkliste;


CREATE TABLE checkliste (
  id INT AUTO_INCREMENT PRIMARY KEY,
  termin_id INT,
  aufgaben Text,
  erstellungszeitpunkt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  soll_erledigt_werden DATE,
  erledigt_am DATE,
  Unteraufgabe_von INT,
  notiz Text
);

CREATE TABLE cc_mitglieder (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50),
  vorname VARCHAR(50),
  email VARCHAR(100)
);

CREATE TABLE aufgaben_personen (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aufgaben_id INT,
  person_id INT
);
