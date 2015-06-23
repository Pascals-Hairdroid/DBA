<?php 
include_once(dirname(__FILE__)."/login_const.php");

const DB_DEFAULT_CONF_FILE = "conf/db.php";
// FORMATE
const DB_FORMAT_DATETIME = "Y-m-d H:i:s";
const DB_MYSQL_FORMAT_DATETIME = "%Y-%m-%d %H:%i:%s";
const DB_FORMAT_TIME = "H:i:s";

// TABELLEN

const DB_TB_MITARBEITER = "mitarbeiter";
const DB_F_MITARBEITER_PK_SVNR = "SVNr";
const DB_F_MITARBEITER_VORNAME = "Vorname";
const DB_F_MITARBEITER_NACHNAME = "Nachname";
const DB_F_MITARBEITER_MOTTO = "Motto";
const DB_F_MITARBEITER_ADMIN = "Admin";
const DB_F_MITARBEITER_PASSWORT = "Passwort";

const DB_TB_SKILLS = "skills";
const DB_F_SKILLS_PK_ID = "ID";
const DB_F_SKILLS_BESCHREIBUNG = "Beschreibung";

const DB_TB_ARBEITSPLATZRESSOURCEN = "arbeitsplatzressourcen";
const DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER = "ArbeitsplatzNr";
const DB_F_ARBEITSPLATZRESSOURCEN_NAME = "Arbeitsplatzname";

const DB_TB_ARBEITSPLATZAUSSTATTUNGEN = "arbeitsplatzausstattungen";
const DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID = "ID";
const DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME = "Ausstattung";

const DB_TB_WERBUNG = "werbung";
const DB_F_WERBUNG_PK_NUMMER = "WerbungsNr";
const DB_F_WERBUNG_TITEL = "Titel";
const DB_F_WERBUNG_TEXT = "Text";
const DB_F_WERBUNG_DATUM = "Datum";

const DB_TB_INTERESSEN = "interessen";
const DB_F_INTERESSEN_PK_ID = "ID";
const DB_F_INTERESSEN_BEZEICHNUNG = "Bez";

const DB_TB_KUNDEN = "kunden";
const DB_F_KUNDEN_PK_EMAIL = "EMail";
const DB_F_KUNDEN_VORNAME = "Vorname";
const DB_F_KUNDEN_NACHNAME = "Nachname";
const DB_F_KUNDEN_TELNR = "TelNr";
const DB_F_KUNDEN_FOTO = "Foto";
const DB_F_KUNDEN_FREISCHALTUNG = "Freischaltung";
const DB_F_KUNDEN_PASSWORT = "Passwort";
const DB_F_KUNDEN_TOKEN = "Token";
const DB_F_KUNDEN_TIMESTAMP = "Datum";

const DB_TB_PRODUKTE = "produkte";
const DB_F_PRODUKTE_PK_ID = "ID";
const DB_F_PRODUKTE_NAME = "Produktname";
const DB_F_PRODUKTE_HERSTELLER = "Hersteller";
const DB_F_PRODUKTE_BESCHREIBUNG = "Beschreibung";
const DB_F_PRODUKTE_PREIS = "Preis";
const DB_F_PRODUKTE_BESTAND = "Bestand";
const DB_F_PRODUKTE_PRODUKTKATEGORIE = "Produktkategorien_Kuerzel";

const DB_TB_PRODUKTKATEGORIEN = "produktkategorien";
const DB_F_PRODUKTKATEGORIEN_PK_KUERZEL = "Kuerzel";
const DB_F_PRODUKTKATEGORIEN_BEZEICHNUNG = "Bezeichnung";

const DB_TB_WOCHENTAGE = "wochentage";
const DB_F_WOCHENTAGE_PK_KUERZEL = "Kuerzel";
const DB_F_WOCHENTAGE_BEZEICHNUNG = "Bezeichnung";

const DB_TB_HAARTYPEN = "haartypen";
const DB_F_HAARTYPEN_PK_KUERZEL = "Kuerzel";
const DB_F_HAARTYPEN_BEZEICHNUNG = "Bezeichnung";

// MULTI PK
const DB_TB_DIENSTLEISTUNGEN = "dienstleistungen";
const DB_F_DIENSTLEISTUNGEN_PK_KUERZEL = "Kuerzel";
const DB_F_DIENSTLEISTUNGEN_PK_HAARTYP = "Haartypen_Kuerzel";
const DB_F_DIENSTLEISTUNGEN_NAME = "Dienstleistung";
const DB_F_DIENSTLEISTUNGEN_BENOETIGTEEINHEITEN = "BenoetigteEinheiten";
const DB_F_DIENSTLEISTUNGEN_PAUSENEINHEITEN = "PausenEinheiten";
const DB_F_DIENSTLEISTUNGEN_GRUPPIERUNG = "Gruppierung";

const DB_TB_URLAUBE = "urlaub";
const DB_F_URLAUBE_PK_MITARBEITER = "Mitarbeiter_SVNr";
const DB_F_URLAUBE_PK_BEGINN = "Beginn";
const DB_F_URLAUBE_ENDE = "Ende";

// Zwischentabellen (MULTI PK)
// Informationenbeinhaltende Zwischentabellen
const DB_TB_DIENSTZEITEN = "dienstzeiten";
const DB_F_DIENSTZEITEN_PK_MITARBEITER = "Mitarbeiter_SVNr";
const DB_F_DIENSTZEITEN_PK_WOCHENTAGE = "Wochentage_Kuerzel";
const DB_F_DIENSTZEITEN_BEGINN = "Beginn";
const DB_F_DIENSTZEITEN_ENDE = "Ende";

const DB_TB_ZEITTABELLE = "zeittabelle";
const DB_F_ZEITTABELLE_PK_ZEITSTEMPEL = "Zeitstempel";
const DB_F_ZEITTABELLE_PK_MITARBEITER = "Mitarbeiter";
const DB_F_ZEITTABELLE_ARBEITSPLATZ = "ArbeitsplatzNr";
const DB_F_ZEITTABELLE_KUNDE = "Kunden_EMail";
const DB_F_ZEITTABELLE_FRISURWUNSCH = "FrisurwunschFoto";
const DB_F_ZEITTABELLE_DIENSTLEISTUNG = "Dienstleistung";
const DB_F_ZEITTABELLE_DIENSTLEISTUNG_HAARTYP = "Dienstleistungen_Haartypen_Kuerzel";

// M:N Zwischentabellen
const DB_TB_MITARBEITER_SKILLS = "mitarbeiter_has_skills";
const DB_F_MITARBEITER_SKILLS_PK_MITARBEITER = "Mitarbeiter_SVNr";
const DB_F_MITARBEITER_SKILLS_PK_SKILLS = "Skills_Bez";

const DB_TB_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN = "arbeitsplatzressourcen_has_arbeitsplatzausstattungen";
const DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZRESSOURCEN = "Arbeitsplatzressourcen_ArbeitsplatzNr";
const DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN = "Arbeitsplatzausstattungen_ID";

const DB_TB_DIENSTLEISTUNGEN_SKILLS = "dienstleistungen_has_skills";
const DB_F_DIENSTLEISTUNGEN_SKILLS_PK_DIENSTLEISTUNGEN = "Dienstleistungen_Kuerzel";
const DB_F_DIENSTLEISTUNGEN_SKILLS_PK_SKILLS = "Skills_ID";

const DB_TB_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN = "dienstleistungen_has_arbeitsplatzausstattungen";
const DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_DIENSTLEISTUNGEN = "Dienstleistungen_Kuerzel";
const DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN = "Arbeitsplatzausstattungen_ID";

const DB_TB_WERBUNG_INTERESSEN = "werbung_has_interessen";
const DB_F_WERBUNG_INTERESSEN_PK_WERBUNG = "Werbung_WerbungsNr";
const DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN= "Interessen_ID";

const DB_TB_KUNDEN_INTERESSEN ="kunden_has_interessen";
const DB_F_KUNDEN_INTERESSEN_PK_KUNDEN = "Kunden_EMail";
const DB_F_KUNDEN_INTERESSEN_PK_INTERESSEN = "Interessen_ID";

const DB_TB_SESSION = "session";
const DB_F_SESSION_PK_ID = "Session_ID";
const DB_F_SESSION_KUNDEN = "Kunden_EMail";


// Views

const DB_VIEW_ZEITTABELLE = "View_Zeittabelle";
const DB_F_VIEW_ZEITTABELE_MITARBEITER_VORNAME = "Mitarbeiitervorname";
const DB_F_VIEW_ZEITTABELE_MITARBEITER_NACHNAME = "Mitarbeiiternachname";
const DB_F_VIEW_ZEITTABELE_DIENSTLEISTUNG = "Dienstleistung";
const DB_F_VIEW_ZEITTABELE_KUNDEN_VORNAME = "Kundenvorname";
const DB_F_VIEW_ZEITTABELE_KUNDEN_NACHNAME = "Kundennachname";
const DB_F_VIEW_ZEITTABELE_KUNDEN_TELNR = "Kundennummer";
const DB_F_VIEW_ZEITTABELE_ARBEITSPLATZRESSOURCEN_NAME = "Arbeitsplatz";

const DB_VIEW_DIENSTLEISTUNGEN_SKILLS = "View_Dienstleistungen_Skills";

const DB_VIEW_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN= "View_Dienstleistungen_Arbeitplatzausstattung";

const DB_VIEW_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN = "View_Arbeitsplatzressourcen_Arbeitplatzausstattung";

const DB_VIEW_ARBEITSPLATZAUSSTATTUNGEN_ARBEITSPLATZRESSOURCEN = "View_Arbeitplatzausstattung_Arbeitsplatzressourcen";

const DB_VIEW_MITARBEITER_SKILLS = "View_Mitarbeiter_Skills";

const DB_VIEW_KUNDEN_INTERESSEN = "View_Kunden_Interessen";

const DB_VIEW_INTERESSEN_KUNDEN = "View_Interessen_Kunden";

const DB_VIEW_ARBEITSPLATZAUSSTATTUNGEN_DIENSTLEISTUNGEN = "View_Arbeitplatzausstattung_Dienstleistungen";

const DB_VIEW_INTERESSEN_WERBUNG = "View_Interessen_Werbung";

const DB_VIEW_MITARBEITERSKILLS = "View_MitarbeiterSkills";

const DB_VIEW_SKILLS_DIENSTLEISTUNGEN = "View_Skills_Dienstleistungen";

const DB_VIEW_SKILLS_MITARBEITER = "View_Skills_Mitarbeiter";

const DB_VIEW_WERBUNG_INTERESSEN = "View_Werbung_Interessen";



// Procedures

const DB_PC_FREIE_TERMINE = "FreieTermine";


const DB_PC_TERMIN_EINTRAGEN = "TerminEintragen";
const DB_PC_TERMIN_STORNIEREN = "TerminStornieren";








// Namenskonvention: ( Pfadaufbau: $beginn.$id.$mitte.$counter.$ende   ; wobei: $counter = $counter_zero bis n;
// $kunde->$id = md5($kunde->getEmail())
const NK_COUNTER_ZERO = 0;
define("NK_Bild_Formate", serialize(array("jpg", "jpeg", "png", "gif")));
// Werbung:
const NK_Werbung_Bild_Width = 1024;
const NK_Werbung_Bild_Height = 768;
const NK_Pfad_Werbung_Bild_beginn = "http://www.pascals.at/v2/Bilder/Werbung/";
const NK_Pfad_Werbung_Bild_mitte = "_";
const NK_Pfad_Werbung_Bild_ende = ".jpg";
define("NK_Pfad_Werbung_Bildupload_beginn",$homepage_dir."/Bilder/Werbung/");

const NK_Kunde_Bild_Width = 1024;
const NK_Kunde_Bild_Height = 768;
const NK_Pfad_Kunde_Bild_beginn = "http://www.pascals.at/v2/Bilder/Profilbilder/";
const NK_Pfad_Kunde_Bild_ende = ".jpg";
define("NK_Pfad_Kunde_Bildupload_beginn",$homepage_dir."/Bilder/Profilbilder/");


//pfad: NK_Pfad_Frisur_Bildupload_beginn.$mitarbeiter->getSvnr().NK_Pfad_Frisur_Bild_mitte.$timestamp.NK_Pfad_Frisur_Bild_ende
define("NK_Pfad_Frisur_Bildupload_beginn",$homepage_dir."/Bilder/Frisurwunsch/");
const NK_Pfad_Frisur_Bild_beginn = "http://www.pascals.at/v2/Bilder/Frisurwunsch/";
const NK_Pfad_Frisur_Bild_mitte = "_";
const NK_Pfad_Frisur_Bild_ende = ".jpg";


//Pascal als Konstante
const PRIORITY_MITARBEITER = "1713270187";
?>