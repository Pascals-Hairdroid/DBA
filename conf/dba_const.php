<?php
// Allgemein
const DBA_FUNCTION = "f";
const DBA_JSON_PARAMS = "jsonParams";
const DBA_SESSION_ID = "sessionId";

// Funktionen
const DBA_F_KUNDEEINTRAGEN = "kundeEintragen";
const DBA_F_KUNDEPWUPDATEN = "kundePwUpdaten";
const DBA_F_KUNDEUPDATEN = "kundeUpdaten";
const DBA_F_GETKUNDEDATEN = "getKundeDaten";
const DBA_F_GETALLMITARBEITER = "getAllMitarbeiter";

$DBA_FUNCTIONS = array(
		DBA_F_KUNDEEINTRAGEN,
		DBA_F_KUNDEPWUPDATEN,
		DBA_F_KUNDEUPDATEN,
		DBA_F_GETKUNDEDATEN,
		DBA_F_GETALLMITARBEITER
);

// Kunde
const DBA_P_KUNDE_EMAIL = "email";
const DBA_P_KUNDE_VORNAME = "vorname";
const DBA_P_KUNDE_NACHNAME = "nachname";
const DBA_P_KUNDE_TELNR = "telnr";
const DBA_P_KUNDE_FREISCHALTUNG = "freischaltung";
const DBA_P_KUNDE_FOTO = "foto";
const DBA_P_FOTONAME = "tmp_name";

const DBA_P_KUNDE_INTERESSEN = "interessen";

const DBA_P_PASSWORT = "passwort";

// Interesse
const DBA_P_INTERESSE_ID = "id";

?>