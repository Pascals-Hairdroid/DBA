<?php
include_once(dirname(__FILE__)."/conf/db_exception_const.php");
include_once(dirname(__FILE__)."/conf/db_const.php");
include_once(dirname(__FILE__)."/classes/Arbeitsplatz.php");
include_once(dirname(__FILE__)."/classes/Arbeitsplatzausstattung.php");
include_once(dirname(__FILE__)."/classes/Dienstleistung.php");
include_once(dirname(__FILE__)."/classes/Dienstzeit.php");
include_once(dirname(__FILE__)."/classes/Haartyp.php");
include_once(dirname(__FILE__)."/classes/Interesse.php");
include_once(dirname(__FILE__)."/classes/Kunde.php");
include_once(dirname(__FILE__)."/classes/Mitarbeiter.php");
include_once(dirname(__FILE__)."/classes/Produktkategorie.php");
include_once(dirname(__FILE__)."/classes/Produkt.php");
include_once(dirname(__FILE__)."/classes/Skill.php");
include_once(dirname(__FILE__)."/classes/Termin.php");
include_once(dirname(__FILE__)."/classes/Urlaub.php");
include_once(dirname(__FILE__)."/classes/Werbung.php");
include_once(dirname(__FILE__)."/classes/Wochentag.php");
include_once(dirname(__FILE__)."/classes/DB_Exception.php");
include_once(dirname(__FILE__)."/classes/Token.php");
include_once(dirname(__FILE__)."/Utils.php");

try{
	date_default_timezone_set("Europe/Vienna");
}catch(Exception $e){}
class DB_Con {
	private $db_ADDRESS;
	private $db_SCHEMA_NAME;
	// User
	private $db_USER_NAME;
	private $db_USER_PASSWORD;
	// Admin
	private $db_ADMIN_NAME;
	private $db_ADMIN_PASSWORD;
	
	private $authKunde_Id;
	
	private $con;
	
	
	function __construct($conf_file, $admin, $charset="latin1"){
		// include config
		$this->changeConfig($conf_file);
		$this->connect($admin);
		$this->setCharset($charset);
	}
	
	function setCharset($charset){
		return $this->con->set_charset($charset)===TRUE;
	}
	
	function changeConfig($conf_file){ // throws Exception
		// include config
		include($conf_file);
		// check validity
		if(isset($cDB_ADDRESS) && 
		isset($cDB_SCHEMA_NAME) && 
		isset($cDB_USER_NAME) && 
		isset($cDB_USER_PASSWORD) && 
		isset($cDB_ADMIN_NAME) && 
		isset($cDB_ADMIN_PASSWORD)){
			// set config
			$this->db_ADDRESS = $cDB_ADDRESS;
			$this->db_SCHEMA_NAME = $cDB_SCHEMA_NAME;
			// User
			$this->db_USER_NAME = $cDB_USER_NAME;
			$this->db_USER_PASSWORD = $cDB_USER_PASSWORD;
			// Admin
			$this->db_ADMIN_NAME = $cDB_ADMIN_NAME;
			$this->db_ADMIN_PASSWORD = $cDB_ADMIN_PASSWORD;
		}
		else throw new DB_Exception(503, "Ungültige Konfigurationsdatei!", DB_ERR_VIEW_NO_CONNECTION);
	}
	
	function connect($admin){
		try{
			$this->con = mysqli_connect($this->db_ADDRESS,
				$admin?$this->db_ADMIN_NAME:$this->db_USER_NAME,
				$admin?$this->db_ADMIN_PASSWORD:$this->db_USER_PASSWORD,
				$this->db_SCHEMA_NAME);
			if (mysqli_connect_errno()) {
				throw new Exception(mysqli_connect_error());
				exit();
			}
			$this->authKunde_Id = null;
		}
		catch (Exception $e){
			throw new DB_Exception(503, "Keine Datenbankverbindung! Fehlermessage: {".$e->getMessage()."}", DB_ERR_VIEW_NO_CONNECTION);
		}
	}
	
	
	function terminEintragen(DateTime $beginn, $mitarbeiterId, $arbeitsplatzId, $kundeId, $foto, $dienstleistungId, $haartypId){
		
		return $this->call(DB_PC_TERMIN_EINTRAGEN, "\"".mysqli_escape_string($this->con,$beginn->format(DB_FORMAT_DATETIME))."\",".$mitarbeiterId.",".$arbeitsplatzId.",\"".mysqli_escape_string($this->con,$kundeId)."\",\"".mysqli_escape_string($this->con,$foto)."\",\"".mysqli_escape_string($this->con,$dienstleistungId)."\",\"".mysqli_escape_string($this->con,$haartypId)."\"");
	}
	
	
	function getAllTermin(DateTime $von, DateTime $bis){
		$abf = $this->selectQuery(DB_TB_ZEITTABELLE, "*",DB_F_ZEITTABELLE_PK_ZEITSTEMPEL." BETWEEN '".$von->format(DB_FORMAT_DATETIME)."' AND '".$bis->format(DB_FORMAT_DATETIME)."'");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		//var_dump($abf);
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,new DateTime ($row[DB_F_ZEITTABELLE_PK_ZEITSTEMPEL]));
		return $res;
	}
	
	function getTermineZeitstempelVonMitarbeiter(Mitarbeiter $mitarbeiter, DateTime $von, DateTime $bis){
		if(!($von->format("U") <= $bis->format("U")))
			throw new DB_Exception(400, "Von-Wert größer als Bis-Wert. Werte: Von: ".$von->format("U")." Bis: ".$bis->format("U"), DB_ERR_VIEW_VON_BIS);
		if($mitarbeiter != null){
			$abf = $this->selectQuery(DB_TB_ZEITTABELLE, DB_F_ZEITTABELLE_PK_ZEITSTEMPEL, DB_F_ZEITTABELLE_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\"");
			$return = array();
			while($row = mysqli_fetch_assoc($abf)){
				array_push($return, new DateTime($row[DB_F_ZEITTABELLE_PK_ZEITSTEMPEL]));
			}
		}
		else 
			throw new DB_Exception(400, "Mitarbeiter darf nicht null sein!", DB_ERR_VIEW_NO_MA);
		return $return;
	}
	
	
	function kundePwUpdaten(Kunde $kunde, $passwort){
		return $this->query("UPDATE ".DB_TB_KUNDEN." SET ".DB_F_KUNDEN_PASSWORT." = \"" .mysqli_escape_string($this->con, $passwort)."\" WHERE ".DB_F_KUNDEN_PK_EMAIL." = \"".mysqli_escape_string($this->con, $kunde->getEmail())."\"")===TRUE;
	}
	
	function mitarbeiterPwUpdaten(Mitarbeiter $mitarbeiter, $passwort){
		return $this->query("UPDATE ".DB_TB_MITARBEITER." SET ".DB_F_MITARBEITER_PASSWORT." = \"" .mysqli_escape_string($this->con, $passwort)."\" WHERE ".DB_F_MITARBEITER_PK_SVNR." = \"".$mitarbeiter->getSvnr()."\"")===TRUE;
	}
	
	
	function skillEntfernen(Skill $skill){
		return $this->query("DELETE FROM ".DB_TB_SKILLS." WHERE ".DB_F_SKILLS_PK_ID."=\"".$skill->getId()."\"")===TRUE;
	}
	
	function skillMitarbeiterZuweisungEntfernen(Skill $skill, Mitarbeiter $mitarbeiter){
		return $this->query("DELETE FROM ".DB_TB_MITARBEITER_SKILLS." WHERE ".DB_F_MITARBEITER_SKILLS_PK_SKILLS."=\"".$skill->getId()."\" AND ".DB_F_MITARBEITER_SKILLS_PK_MITARBEITER."=\"".$mitarbeiter->getSvnr()."\"")===TRUE;
	}
	
	function skillDienstleistungZuweisungEntfernen(Skill $skill, Dienstleistung $dienstleistung){
		return $this->query("DELETE FROM ".DB_TB_DIENSTLEISTUNGEN_SKILLS." WHERE ".DB_F_DIENSTLEISTUNGEN_SKILLS_PK_SKILLS."=\"".$skill->getId()."\" AND ".DB_F_DIENSTLEISTUNGEN_SKILLS_PK_DIENSTLEISTUNGEN."=\"".mysqli_escape_string($this->con,$dienstleistung->getKuerzel())."\"")===TRUE;
	}
	
	function haartypEntfernen(Haartyp $haartyp){
		return $this->query("DELETE FROM ".DB_TB_HAARTYPEN." WHERE ".DB_F_HAARTYPEN_PK_KUERZEL."=\"".mysqli_escape_string($this->con,$haartyp->getKuerzel())."\"")===TRUE;
	}
	
	function interesseEntfernen(Interesse $interesse){
		return $this->query("DELETE FROM ".DB_TB_INTERESSEN." WHERE ".DB_F_INTERESSEN_PK_ID."=\"".$interesse->getId()."\"")===TRUE;
	}
	
	function interesseKundeZuweisungEntfernen(Interesse $interesse, Kunde $kunde){
		return $this->query("DELETE FROM ".DB_TB_KUNDEN_INTERESSEN." WHERE ".DB_F_KUNDEN_INTERESSEN_PK_INTERESSEN."=\"".$interesse->getId()."\" AND ".DB_F_KUNDEN_INTERESSEN_PK_KUNDEN."=\"".mysqli_escape_string($this->con,$kunde->getEmail())."\"")===TRUE;
	}
	
	function interesseWerbungZuweisungEntfernen(Interesse $interesse, Werbung $werbung){
		return $this->query("DELETE FROM ".DB_TB_WERBUNG_INTERESSEN." WHERE ".DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN."=\"".$interesse->getId()."\" AND ".DB_F_WERBUNG_INTERESSEN_PK_WERBUNG."=\"".$werbung->getNummer()."\"")===TRUE;
	}
	
	function arbeitsplatzausstattungEntfernen(Arbeitsplatzausstattung $ausstattung){
		return $this->query("DELETE FROM ".DB_TB_ARBEITSPLATZAUSSTATTUNGEN." WHERE ".DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID."=\"".$ausstattung->getId()."\"")===TRUE;
	}
	
	function arbeitsplatzausstattungArbeitsplatzZuweisungEntfernen(Arbeitsplatzausstattung $ausstattung, Arbeitsplatz $arbeitsplatz){
		return $this->query("DELETE FROM ".DB_TB_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN." WHERE ".DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN."=\"".$ausstattung->getId()."\" AND ".DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZRESSOURCEN."=\"".$arbeitsplatz->getNummer()."\"")===TRUE;
	}
	
	function arbeitsplatzausstattungDienstleistungZuweisungEntfernen(Arbeitsplatzausstattung $ausstattung, Dienstleistung $dienstleistung){
		return $this->query("DELETE FROM ".DB_TB_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN." WHERE ".DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN."=\"".$ausstattung->getId()."\" AND ".DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_DIENSTLEISTUNGEN."=\"".mysqli_escape_string($this->con,$dienstleistung->getKuerzel())."\"")===TRUE;
	}
	
	function produktEntfernen(Produkt $produkt){
		return $this->query("DELETE FROM ".DB_TB_PRODUKTE." WHERE ".DB_F_PRODUKTE_PK_ID."=\"".$produkt->getId()."\"")===TRUE;
	}
	
	function wochentagEntfernen(Wochentag $wochentag){
		return $this->query("DELETE FROM ".DB_TB_WOCHENTAGE." WHERE ".DB_F_WOCHENTAGE_PK_KUERZEL."=\"".mysqli_escape_string($this->con,$wochentag->getKuerzel())."\"")===TRUE;
	}
	
	function urlaubEntfernen(Urlaub $urlaub, Mitarbeiter $mitarbeiter){
		return $this->query("DELETE FROM ".DB_TB_URLAUBE." WHERE ".DB_F_URLAUBE_PK_BEGINN."=\"".$urlaub->getBeginn()->format(DB_FORMAT_DATETIME)."\" AND ".DB_F_URLAUBE_PK_MITARBEITER."=\"".$mitarbeiter->getSvnr()."\"")===TRUE;
	}
	
	function dienstzeitEntfernen(Dienstzeit $dienstzeit, Mitarbeiter $mitarbeiter){
		return $this->query("DELETE FROM ".DB_TB_DIENSTZEITEN." WHERE ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE."=\"".mysqli_escape_string($this->con,$dienstzeit->getWochentag()->getKuerzel())."\" AND ".DB_F_DIENSTZEITEN_PK_MITARBEITER."=\"".$mitarbeiter->getSvnr()."\"")===TRUE;
	}	
	
	function werbungEntfernen(Werbung $werbung){
		$success = true;
		foreach ($werbung->getInteressen() as $interesse){
			if($interesse instanceof Interesse)
				$success=$success?$this->interesseWerbungZuweisungEntfernen($interesse, $werbung):$success;
		}
		$success=$success?$this->query("DELETE FROM ".DB_TB_WERBUNG." WHERE ".DB_F_WERBUNG_PK_NUMMER."=\"".$werbung->getNummer()."\"")===TRUE:$success;
		return $success;
	}
	
	function arbeitsplatzEntfernen(Arbeitsplatz $arbeitsplatz){
		$success = true;
		foreach ($arbeitsplatz->getAusstattung() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung)
				$success=$success?$this->arbeitsplatzausstattungArbeitsplatzZuweisungEntfernen($ausstattung, $arbeitsplatz):$success;
		}
		$success=$success?$this->query("DELETE FROM ".DB_TB_ARBEITSPLATZRESSOURCEN." WHERE ".DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER."=\"".$arbeitsplatz->getNummer()."\"")===TRUE:$success;
		return $success;
	}
	
	function mitarbeiterEntfernen(Mitarbeiter $mitarbeiter){
		$success = true;
		foreach ($mitarbeiter->getSkills() as $skill){
			if($skill instanceof Skill)
				$success=$success?$this->skillMitarbeiterZuweisungEntfernen($skill, $mitarbeiter):$success;
		}
		foreach ($mitarbeiter->getUrlaube() as $urlaub){
			if($urlaub instanceof Urlaub)
				$success=$success?$this->urlaubEntfernen($urlaub, $mitarbeiter):$success;
		}
		foreach ($mitarbeiter->getDienstzeiten() as $dienstzeit){
			if($dienstzeit instanceof Dienstzeit)
				$success=$success?$this->dienstzeitEntfernen($dienstzeit, $mitarbeiter):$success;
		}
		$success=$success?$this->query("DELETE FROM ".DB_TB_MITARBEITER." WHERE ".DB_F_MITARBEITER_PK_SVNR."=\"".$mitarbeiter->getSvnr()."\"")===TRUE:$success;
		return $success;
	}
	
	function kundeEntfernen(Kunde $kunde){
		$success = true;
		foreach ($kunde->getInteressen() as $interesse){
			if($interesse instanceof Interesse)
				$success=$success?$this->interesseKundeZuweisungEntfernen($interesse, $kunde):$success;
		}
		$success=$success?$this->query("DELETE FROM ".DB_TB_KUNDEN." WHERE ".DB_F_KUNDEN_PK_EMAIL."=\"".mysqli_escape_string($this->con,$kunde->getEmail())."\"")===TRUE:$success;
		return $success;
	}
	
	function dienstleistungEntfernen(Dienstleistung $dienstleistung){
		$success = true;
		foreach ($dienstleistung->getSkills() as $skill){
			if($skill instanceof Skill)
				$success=$success?$this->skillDienstleistungZuweisungEntfernen($skill, $dienstleistung):$success;
		}
		foreach ($dienstleistung->getArbeitsplatzausstattungen() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung)
				$success=$success?$this->arbeitsplatzausstattungDienstleistungZuweisungEntfernen($ausstattung, $dienstleistung):$success;
		}
		$success=$success?$this->query("DELETE FROM ".DB_TB_DIENSTLEISTUNGEN." WHERE ".DB_F_DIENSTLEISTUNGEN_PK_KUERZEL."=\"".mysqli_escape_string($this->con,$dienstleistung->getKuerzel())."\" AND ".DB_F_DIENSTLEISTUNGEN_PK_HAARTYP."=\"".mysqli_escape_string($this->con,$dienstleistung->getHaartyp()->getKuerzel())."\"")===TRUE:$success;
		return $success;
	}
	
	
	function sessionEintragen($session_id, $email){
		return $this->query("INSERT INTO ".DB_TB_SESSION." (".DB_F_SESSION_PK_ID.", ".DB_F_SESSION_KUNDEN.") VALUES (\"".mysqli_escape_string($this->con,$session_id)."\", \"".mysqli_escape_string($this->con,$email)."\")")===TRUE;
	}
	
	function skillEintragen(Skill $skill){
		return $this->query("INSERT INTO ".DB_TB_SKILLS." (".DB_F_SKILLS_PK_ID.", ".DB_F_SKILLS_BESCHREIBUNG.") VALUES (\"".$skill->getId()."\", \"".mysqli_escape_string($this->con,$skill->getBeschreibung())."\")")===TRUE;
	}
	
	function skillMitarbeiterZuweisen(Skill $skill, Mitarbeiter $mitarbeiter){
		return $this->query("INSERT INTO ".DB_TB_MITARBEITER_SKILLS." (".DB_F_MITARBEITER_SKILLS_PK_SKILLS.", ".DB_F_MITARBEITER_SKILLS_PK_MITARBEITER.") VALUES (\"".$skill->getId()."\", \"".$mitarbeiter->getSvnr()."\")")===TRUE;
	}
	
	function skillDienstleistungZuweisen(Skill $skill, Dienstleistung $dienstleistung){
		return $this->query("INSERT INTO ".DB_TB_DIENSTLEISTUNGEN_SKILLS." (".DB_F_DIENSTLEISTUNGEN_SKILLS_PK_SKILLS.", ".DB_F_DIENSTLEISTUNGEN_SKILLS_PK_DIENSTLEISTUNGEN.") VALUES (\"".$skill->getId()."\", \"".mysqli_escape_string($this->con,$dienstleistung->getKuerzel())."\")")===TRUE;
	}
	
	function haartypEintragen(Haartyp $haartyp){
		return $this->query("INSERT INTO ".DB_TB_HAARTYPEN." (".DB_F_HAARTYPEN_PK_KUERZEL.", ".DB_F_HAARTYPEN_BEZEICHNUNG.") VALUES (\"".mysqli_escape_string($this->con,$haartyp->getKuerzel())."\", \"".mysqli_escape_string($this->con,$haartyp->getBezeichnung())."\")")===TRUE;
	}
	
	function interesseEintragen(Interesse $interesse){
		return $this->query("INSERT INTO ".DB_TB_INTERESSEN." (".DB_F_INTERESSEN_PK_ID.", ".DB_F_INTERESSEN_BEZEICHNUNG.") VALUES (\"".$interesse->getId()."\", \"".mysqli_escape_string($this->con,$interesse->getBezeichnung())."\")")===TRUE;
	}

	function interesseKundeZuweisen(Interesse $interesse, Kunde $kunde){
		return $this->query("INSERT INTO ".DB_TB_KUNDEN_INTERESSEN." (".DB_F_KUNDEN_INTERESSEN_PK_INTERESSEN.", ".DB_F_KUNDEN_INTERESSEN_PK_KUNDEN.") VALUES (\"".$interesse->getId()."\", \"".mysqli_escape_string($this->con,$kunde->getEmail())."\")")===TRUE;
	}

	function interesseWerbungZuweisen(Interesse $interesse, Werbung $werbung){
		return $this->query("INSERT INTO ".DB_TB_WERBUNG_INTERESSEN." (".DB_F_WERBUNG_INTERESSEN_PK_WERBUNG.", ".DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN.") VALUES (\"".$werbung->getNummer()."\", \"".$interesse->getId()."\")")===TRUE;
	}
	
	function arbeitsplatzausstattungEintragen(Arbeitsplatzausstattung $ausstattung){
		return $this->query("INSERT INTO ".DB_TB_ARBEITSPLATZAUSSTATTUNGEN." (".DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID.", ".DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME.") VALUES (\"".$ausstattung->getId()."\", \"".mysqli_escape_string($this->con,$ausstattung->getName())."\")")===TRUE;
	}
	
	function arbeitsplatzausstattungArbeitsplatzZuweisen(Arbeitsplatzausstattung $ausstattung, Arbeitsplatz $arbeitsplatz){
		return $this->query("INSERT INTO ".DB_TB_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN." (".DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN.", ".DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZRESSOURCEN.") VALUES (\"".$ausstattung->getId()."\", \"".$arbeitsplatz->getNummer()."\")")===TRUE;
	}
	
	function arbeitsplatzausstattungDienstleistungZuweisen(Arbeitsplatzausstattung $ausstattung, Dienstleistung $dienstleistung){
		return $this->query("INSERT INTO ".DB_TB_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN." (".DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN.", ".DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_DIENSTLEISTUNGEN.") VALUES (\"".$ausstattung->getId()."\", \"".$dienstleistung->getKuerzel()."\")")===TRUE;
	}
	
	function produktkategorienEintragen(Produktkategorie $kategorie){
		return $this->query("INSERT INTO ".DB_TB_PRODUKTKATEGORIEN." (".DB_F_PRODUKTKATEGORIEN_PK_KUERZEL.", ".DB_F_PRODUKTKATEGORIEN_BEZEICHNUNG.") VALUES (\"".mysqli_escape_string($this->con,$kategorie->getKuerzel())."\", \"".mysqli_escape_string($this->con,$kategorie->getBezeichnung())."\")")===TRUE;
	}
	
	function produktEintragen(Produkt $produkt){
		return $this->query("INSERT INTO ".DB_TB_PRODUKTE." (".DB_F_PRODUKTE_PK_ID.", ".DB_F_PRODUKTE_NAME.", ".DB_F_PRODUKTE_HERSTELLER.", ".DB_F_PRODUKTE_BESCHREIBUNG.", ".DB_F_PRODUKTE_PREIS.", ".DB_F_PRODUKTE_BESTAND.", ".DB_F_PRODUKTE_PRODUKTKATEGORIE.") VALUES (\"".$produkt->getId()."\", \"".mysqli_escape_string($this->con,$produkt->getName())."\", \"".mysqli_escape_string($this->con,$produkt->getHersteller())."\", \"".mysqli_escape_string($this->con,$produkt->getBeschreibung())."\", \"".$produkt->getPreis()."\", \"".$produkt->getBestand()."\", \"".mysqli_escape_string($this->con,$produkt->getKategorie()->getKuerzel())."\")")===TRUE;
	}
	
	function wochentagEintragen(Wochentag $wochentag){
		return $this->query("INSERT INTO ".DB_TB_WOCHENTAGE." (".DB_F_WOCHENTAGE_PK_KUERZEL.", ".DB_F_WOCHENTAGE_BEZEICHNUNG.") VALUES (\"".mysqli_escape_string($this->con,$wochentag->getKuerzel())."\", \"".mysqli_escape_string($this->con,$wochentag->getBezeichnung())."\")")===TRUE;
	}
	
	function urlaubEintragen(Urlaub $urlaub, Mitarbeiter $mitarbeiter){
		return $this->query("INSERT INTO ".DB_TB_URLAUBE." (".DB_F_URLAUBE_PK_MITARBEITER.", ".DB_F_URLAUBE_PK_BEGINN.", ".DB_F_URLAUBE_ENDE.") VALUES (\"".$mitarbeiter->getSvnr()."\", \"".$urlaub->getBeginn()->format(DB_FORMAT_DATETIME)."\", \"".$urlaub->getEnde()->format(DB_FORMAT_DATETIME)."\")")===TRUE;
	}
	
	function dienstzeitEintragen(Dienstzeit $dienstzeit, Mitarbeiter $mitarbeiter){
		return $this->query("INSERT INTO ".DB_TB_DIENSTZEITEN." (".DB_F_DIENSTZEITEN_PK_MITARBEITER.", ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE.", ".DB_F_DIENSTZEITEN_BEGINN.", ".DB_F_DIENSTZEITEN_ENDE.") VALUES (\"".$mitarbeiter->getSvnr()."\", \"".mysqli_escape_string($this->con,$dienstzeit->getWochentag()->getKuerzel())."\", \"".$dienstzeit->getBeginn()->format(DB_FORMAT_TIME)."\", \"".$dienstzeit->getEnde()->format(DB_FORMAT_TIME)."\")")===TRUE;
	}
	

	function werbungEintragen(Werbung $werbung){
		$success = $this->query("INSERT INTO ".DB_TB_WERBUNG." (".DB_F_WERBUNG_PK_NUMMER.", ".DB_F_WERBUNG_DATUM.", ".DB_F_WERBUNG_TITEL.", ".DB_F_WERBUNG_TEXT.") VALUES (\"".$werbung->getNummer()."\", \"".$werbung->getDatum()->format(DB_FORMAT_DATETIME)."\", \"".mysqli_escape_string($this->con, $werbung->getTitel())."\", \"".mysqli_escape_string($this->con, $werbung->getText())."\")")===TRUE;
		foreach ($werbung->getInteressen() as $interesse){
			if($interesse instanceof Interesse)
				$success=$success?$this->interesseWerbungZuweisen($interesse, $werbung):$success;
		}
		return $success;
	}
	
	function arbeitsplatzEintragen(Arbeitsplatz $arbeitsplatz){
		$success = $this->query("INSERT INTO ".DB_TB_ARBEITSPLATZRESSOURCEN." (".DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER.", ".DB_F_ARBEITSPLATZRESSOURCEN_NAME.") VALUES (\"".$arbeitsplatz->getNummer()."\", \"".mysqli_escape_string($this->con,$arbeitsplatz->getName())."\")")===TRUE;
		foreach ($arbeitsplatz->getAusstattung() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung)
				$success=$success?$this->arbeitsplatzausstattungArbeitsplatzZuweisen($ausstattung, $arbeitsplatz):$success;
		}
		return $success;
	}
	
	function mitarbeiterEintragen(Mitarbeiter $mitarbeiter){
		$success = $this->query("INSERT INTO ".DB_TB_MITARBEITER." (".DB_F_MITARBEITER_PK_SVNR.", ".DB_F_MITARBEITER_VORNAME.", ".DB_F_MITARBEITER_NACHNAME.", ".DB_F_MITARBEITER_MOTTO.", ".DB_F_MITARBEITER_ADMIN.") VALUES (\"".$mitarbeiter->getSvnr()."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getVorname())."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getNachname())."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getMotto())."\", \"".$mitarbeiter->getAdmin()."\")")===TRUE;
		foreach ($mitarbeiter->getSkills() as $skill){
			if($skill instanceof Skill)
				$success=$success?$this->skillMitarbeiterZuweisen($skill, $mitarbeiter):$success;
		}
		foreach ($mitarbeiter->getUrlaube() as $urlaub){
			if($urlaub instanceof Urlaub)
				$success=$success?$this->urlaubEintragen($urlaub, $mitarbeiter):$success;
		}
		foreach ($mitarbeiter->getDienstzeiten() as $dienstzeit){
			if($dienstzeit instanceof Dienstzeit)
				$success=$success?$this->dienstzeitEintragen($dienstzeit, $mitarbeiter):$success;
		}
		return $success;
	}
	
	function kundeEintragen(Kunde $kunde){
		$success = $this->query("INSERT INTO ".DB_TB_KUNDEN." (".DB_F_KUNDEN_PK_EMAIL.", ".DB_F_KUNDEN_VORNAME.", ".DB_F_KUNDEN_NACHNAME.", ".DB_F_KUNDEN_TELNR.", ".DB_F_KUNDEN_FREISCHALTUNG.", ".DB_F_KUNDEN_FOTO.") VALUES (\"".mysqli_escape_string($this->con,$kunde->getEmail())."\", \"".mysqli_escape_string($this->con,$kunde->getVorname())."\", \"".mysqli_escape_string($this->con,$kunde->getNachname())."\", \"".mysqli_escape_string($this->con,$kunde->getTelNr())."\", \"".$kunde->getFreischaltung()."\", \"".mysqli_escape_string($this->con,$kunde->getFoto())."\")")===TRUE;
		foreach ($kunde->getInteressen() as $interesse){
			if($interesse instanceof Interesse)
				$success=$success?$this->interesseKundeZuweisen($interesse, $kunde):$success;
		}
		return $success;
	}
	
	function dienstleistungEintragen(Dienstleistung $dienstleistung){
		$success = $this->query("INSERT INTO ".DB_TB_DIENSTLEISTUNGEN." (".DB_F_DIENSTLEISTUNGEN_PK_KUERZEL.", ".DB_F_DIENSTLEISTUNGEN_PK_HAARTYP.", ".DB_F_DIENSTLEISTUNGEN_NAME.", ".DB_F_DIENSTLEISTUNGEN_BENOETIGTEEINHEITEN.", ".DB_F_DIENSTLEISTUNGEN_PAUSENEINHEITEN.", ".DB_F_DIENSTLEISTUNGEN_GRUPPIERUNG.") VALUES (\"".mysqli_escape_string($this->con,$dienstleistung->getKuerzel())."\", \"".mysqli_escape_string($this->con,$dienstleistung->getHaartyp()->getKuerzel())."\", \"".mysqli_escape_string($this->con,$dienstleistung->getName())."\", \"".$dienstleistung->getBenoetigteEinheiten()."\", \"".$dienstleistung->getPausenEinheiten()."\", \"".$dienstleistung->getGruppierung()."\")")===TRUE;
		foreach ($dienstleistung->getSkills() as $skill){
			if($skill instanceof Skill)
				$success=$success?$this->skillDienstleistungZuweisen($skill, $dienstleistung):$success;
		}
		foreach ($dienstleistung->getArbeitsplatzausstattungen() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung)
				$success=$success?$this->arbeitsplatzausstattungDienstleistungZuweisen($ausstattung, $dienstleistung):$success;
		}
		return $success;
	}
	
	function kundeTokenUpdaten(Kunde $kunde, Token $token){
		return $this->query("UPDATE ".DB_TB_KUNDEN." SET ".DB_F_KUNDEN_TOKEN." = \"" .mysqli_escape_string($this->con, $token->getToken())."\", ".DB_F_KUNDEN_TIMESTAMP." = \"".mysqli_escape_string($this->con, $token->getTimestamp()->format(DB_FORMAT_DATETIME))."\" WHERE ".DB_F_KUNDEN_PK_EMAIL." = \"".mysqli_escape_string($this->con, $kunde->getEmail())."\"")===TRUE;
	}
	
	function skillUpdaten(Skill $skill){
		return $this->query("UPDATE ".DB_TB_SKILLS." SET ".DB_F_SKILLS_BESCHREIBUNG." = \"" .mysqli_escape_string($this->con, $skill->getBeschreibung())."\" WHERE ".DB_F_SKILLS_PK_ID." = \"".$skill->getId()."\"")===TRUE;
	}
	
	function haartypUpdaten(Haartyp $haartyp){
		return $this->query("UPDATE ".DB_TB_HAARTYPEN." SET ".DB_F_HAARTYPEN_BEZEICHNUNG." = \"" .mysqli_escape_string($this->con, $haartyp->getBezeichnung())."\" WHERE ".DB_F_HAARTYPEN_PK_KUERZEL." = \"".mysqli_escape_string($this->con, $haartyp->getKuerzel())."\"")===TRUE;
	}
	
	function interesseUpdaten(Interesse $interesse){
		return $this->query("UPDATE ".DB_TB_INTERESSEN." SET ".DB_F_INTERESSEN_BEZEICHNUNG." = \"" .mysqli_escape_string($this->con, $interesse->getBezeichnung())."\" WHERE ".DB_F_INTERESSEN_PK_ID." = \"".$interesse->getId()."\"")===TRUE;
	}
	
	function arbeitsplatzausstattungUpdaten(Arbeitsplatzausstattung $ausstattung){
		return $this->query("UPDATE ".DB_TB_ARBEITSPLATZAUSSTATTUNGEN." SET ".DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME." = \"" .mysqli_escape_string($this->con, $ausstattung->getName())."\" WHERE ".DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID." = \"".$ausstattung->getId()."\"")===TRUE;
	}
	
	function produktUpdaten(Produkt $produkt){
		return $this->query("UPDATE ".DB_TB_PRODUKTE." SET ".DB_F_PRODUKTE_NAME." = \"" .mysqli_escape_string($this->con, $produkt->getName())."\", ".DB_F_PRODUKTE_HERSTELLER." = \"".mysqli_escape_string($this->con, $produkt->getHersteller())."\", ".DB_F_PRODUKTE_BESCHREIBUNG." = \"".mysqli_escape_string($this->con, $produkt->getBeschreibung())."\", ".DB_F_PRODUKTE_PRODUKTKATEGORIE." = \"".mysqli_escape_string($this->con, $produkt->getKategorie()->getKuerzel())."\", ".DB_F_PRODUKTE_PREIS." = \"".$produkt->getPreis()."\", ".DB_F_PRODUKTE_BESTAND." = \"".$produkt->getBestand()."\" WHERE ".DB_F_PRODUKTE_PK_ID." = \"".$produkt->getId()."\"")===TRUE;
	}
	
	function wochentagUpdaten(Wochentag $wochentag){
		return $this->query("UPDATE ".DB_TB_WOCHENTAGE." SET ".DB_F_WOCHENTAGE_BEZEICHNUNG." = \"" .mysqli_escape_string($this->con, $wochentag->getBezeichnung())."\" WHERE ".DB_F_WOCHENTAGE_PK_KUERZEL." = \"".mysqli_escape_string($this->con, $wochentag->getKuerzel())."\"")===TRUE;
	}
	
	function dienstzeitUpdaten(Dienstzeit $dienstzeit, Mitarbeiter $mitarbeiter){
		return $this->query("UPDATE ".DB_TB_DIENSTZEITEN." SET ".DB_F_DIENSTZEITEN_BEGINN." = \"" .$dienstzeit->getBeginn()->format(DB_FORMAT_TIME)."\", ".DB_F_DIENSTZEITEN_ENDE." = \"".$dienstzeit->getEnde()->format(DB_FORMAT_TIME)."\" WHERE ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE." = \"".mysqli_escape_string($this->con, $dienstzeit->getWochentag()->getKuerzel())."\" AND ".DB_F_DIENSTZEITEN_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\"")===TRUE;
	}
	
	function produktkategorieUpdaten(Produktkategorie $kategorie){
		return $this->query("UPDATE ".DB_TB_PRODUKTKATEGORIEN." SET ".DB_F_PRODUKTKATEGORIEN_BEZEICHNUNG." = \"".mysqli_escape_string($this->con, $kategorie->getBezeichnung())."\" WHERE ".DB_F_PRODUKTKATEGORIEN_PK_KUERZEL." = \"".mysqli_escape_string($this->con, $kategorie->getKuerzel())."\"")===TRUE;
	}
	
	
	
	function werbungUpdaten(Werbung $werbung){
		$werbung_alt=$this->getWerbung($werbung->getNummer());
		$success = $this->query("UPDATE ".DB_TB_WERBUNG." SET ".DB_F_WERBUNG_DATUM." = \"".$werbung->getDatum()->format(DB_FORMAT_DATETIME)."\", ".DB_F_WERBUNG_TITEL." = \"" .mysqli_escape_string($this->con, $werbung->getTitel())."\", ".DB_F_WERBUNG_TEXT." = \"".mysqli_escape_string($this->con, $werbung->getText())."\" WHERE ".DB_F_WERBUNG_PK_NUMMER." = \"".$werbung->getNummer()."\"")===TRUE;
		$interessenIds_alt = array();
		foreach ($werbung_alt->getInteressen() as $interesse_alt)
			array_push($interessenIds_alt,$interesse_alt->getId());
		$interessen_neu="";
		foreach ($werbung->getInteressen() as $interesse){
			if($interesse instanceof Interesse){
				$interessen_neu = $interessen_neu.", \"".$interesse->getId()."\"";
				if(!in_array($interesse->getId(),$interessenIds_alt))
					$success=$success?$this->interesseWerbungZuweisen($interesse, $werbung):$success;
			}
		}
		$interessen_neu = substr($interessen_neu,2);
		$success=$success?$this->query("DELETE FROM ".DB_TB_WERBUNG_INTERESSEN." WHERE ".DB_F_WERBUNG_INTERESSEN_PK_WERBUNG." = \"".$werbung->getNummer()."\" AND (".DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN." NOT IN(".$interessen_neu."))"):$success;
		return $success;
	}
	
	function arbeitsplatzUpdaten(Arbeitsplatz $arbeitsplatz){
		$success = true;
		$arbeitsplatz_alt=$this->getArbeitsplatz($arbeitsplatz->getNummer());
		$ausstattungenIds_alt = array();
		foreach ($arbeitsplatz_alt->getAusstattung() as $ausstattung_alt)
			array_push($ausstattungenIds_alt,$ausstattung_alt->getId());
		$ausstattungen_neu="";
		foreach ($arbeitsplatz->getAusstattung() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung){
				$ausstattungen_neu = $ausstattungen_neu.", \"".$ausstattung->getId()."\"";
				if(!in_array($ausstattung->getId(),$ausstattungenIds_alt))
					$success=$success?$this->arbeitsplatzausstattungArbeitsplatzZuweisen($ausstattung, $arbeitsplatz):$success;
			}
		}
		$ausstattungen_neu = substr($ausstattungen_neu,2);
		$success=$success?$this->query("DELETE FROM ".DB_TB_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN." WHERE ".DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZRESSOURCEN." = \"".$arbeitsplatz->getNummer()."\" AND (".DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN." NOT IN(".$ausstattungen_neu."))"):$success;
		return $success;
	}
	
	function mitarbeiterUpdaten(Mitarbeiter $mitarbeiter){
		$mitarbeiter_alt=$this->getMitarbeiter($mitarbeiter->getSvnr());
		$success = $this->query("UPDATE ".DB_TB_MITARBEITER." SET ".DB_F_MITARBEITER_NACHNAME." = \"" .mysqli_escape_string($this->con, $mitarbeiter->getNachname())."\", ".DB_F_MITARBEITER_VORNAME." = \"".mysqli_escape_string($this->con, $mitarbeiter->getVorname())."\", ".DB_F_MITARBEITER_MOTTO." = \"".mysqli_escape_string($this->con, $mitarbeiter->getMotto())."\", ".DB_F_MITARBEITER_ADMIN." = ".($mitarbeiter->getAdmin()?1:0)." WHERE ".DB_F_MITARBEITER_PK_SVNR." = \"".$mitarbeiter->getSvnr()."\"")===TRUE;
		
		$skillsIds_alt = array();
		
		foreach ($mitarbeiter_alt->getSkills() as $skill_alt)
			array_push($skillsIds_alt,$skill_alt->getId());
		
		$skills_neu="";
		
		foreach ($mitarbeiter->getSkills() as $skill){
			if($skill instanceof Skill){
				$skills_neu = $skills_neu.", \"".$skill->getId()."\"";
				if(!in_array($skill->getId(),$skillsIds_alt))
					$success=$success?$this->skillMitarbeiterZuweisen($skill, $mitarbeiter):$success;
			}
		}
		$skills_neu = substr($skills_neu,2);
		$success=$success?$this->query("DELETE FROM ".DB_TB_MITARBEITER_SKILLS." WHERE ".DB_F_MITARBEITER_SKILLS_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\" AND (".DB_F_MITARBEITER_SKILLS_PK_SKILLS." NOT IN(".$skills_neu."))"):$success;
		
		$urlaube_del=array();
		$urlaube_add=array();
		foreach ($mitarbeiter_alt->getUrlaube() as $urlaub_alt){
			$vorhanden = false;
			foreach ($mitarbeiter->getUrlaube() as $urlaub_neu)
			if ((strcmp($urlaub_neu->getBeginn()->format(DB_FORMAT_DATETIME),$urlaub_alt->getBeginn()->format(DB_FORMAT_DATETIME))==0) && (strcmp($urlaub_neu->getEnde()->format(DB_FORMAT_DATETIME),$urlaub_alt->getEnde()->format(DB_FORMAT_DATETIME))==0))
				$vorhanden=true;
			if (!$vorhanden)
				array_push($urlaube_del, $urlaub_alt);
		}
		foreach ($mitarbeiter->getUrlaube() as $urlaub_neu){
			$vorhanden = false;
			foreach ($mitarbeiter_alt->getUrlaube() as $urlaub_alt)
				if ((strcmp($urlaub_neu->getBeginn()->format(DB_FORMAT_DATETIME),$urlaub_alt->getBeginn()->format(DB_FORMAT_DATETIME))==0) && (strcmp($urlaub_neu->getEnde()->format(DB_FORMAT_DATETIME),$urlaub_alt->getEnde()->format(DB_FORMAT_DATETIME))==0))
					$vorhanden=true;
			if (!$vorhanden)
				array_push($urlaube_add, $urlaub_neu);
		}
		foreach ($urlaube_del as $urlaub)
			$success=$success?$this->urlaubEntfernen($urlaub, $mitarbeiter):$success;
		foreach ($urlaube_add as $urlaub)
			$success=$success?$this->urlaubEintragen($urlaub, $mitarbeiter):$success;
		
		$dienstzeiten_del=array();
		$dienstzeiten_add=array();
		foreach ($mitarbeiter_alt->getDienstzeiten() as $dienstzeit_alt){
			$vorhanden = false;
			foreach ($mitarbeiter->getDienstzeiten() as $dienstzeit_neu)
				if (strcmp($dienstzeit_alt->getWochentag()->getKuerzel(),$dienstzeit_neu->getWochentag()->getKuerzel())==0)
					$vorhanden=true;
			if (!$vorhanden)
				array_push($dienstzeiten_del, $dienstzeit_alt);
		}
		foreach ($mitarbeiter->getDienstzeiten() as $dienstzeit_neu){
			$vorhanden = false;
			foreach ($mitarbeiter_alt->getDienstzeiten() as $dienstzeit_alt)
				if (strcmp($dienstzeit_alt->getWochentag()->getKuerzel(),$dienstzeit_neu->getWochentag()->getKuerzel())==0)
					$vorhanden=true;
			if (!$vorhanden)
				array_push($dienstzeiten_add, $dienstzeit_neu);
		}
		foreach ($mitarbeiter->getDienstzeiten() as $dienstzeit){
			if (!in_array($dienstzeit, $dienstzeiten_add))
				$success=$success?$this->dienstzeitUpdaten($dienstzeit, $mitarbeiter):$success;
		}
		foreach ($dienstzeiten_del as $dienstzeit)
			$success=$success?$this->dienstzeitEntfernen($dienstzeit, $mitarbeiter):$success;
		foreach ($dienstzeiten_add as $dienstzeit)
			$success=$success?$this->dienstzeitEintragen($dienstzeit, $mitarbeiter):$success;
		return $success;
	}
	
	function kundeUpdaten(Kunde $kunde){
		$kunde_alt=$this->getKunde($kunde->getEmail());
		$success = $this->query("UPDATE ".DB_TB_KUNDEN." SET ".DB_F_KUNDEN_FOTO." = \"" .mysqli_escape_string($this->con, $kunde->getFoto()!=null?$kunde->getFoto():$kunde_alt->getFoto())."\", ".DB_F_KUNDEN_VORNAME." = \"".mysqli_escape_string($this->con, $kunde->getVorname()!=null?$kunde->getVorname():$kunde_alt->getVorname())."\", ".DB_F_KUNDEN_NACHNAME." = \"".mysqli_escape_string($this->con, $kunde->getNachname()!=null?$kunde->getNachname():$kunde_alt->getNachname())."\", ".DB_F_KUNDEN_TELNR." = \"".mysqli_escape_string($this->con, $kunde->getTelNr()!=null?$kunde->getTelNr():$kunde_alt->getTelNr())."\", ".DB_F_KUNDEN_FREISCHALTUNG." = \"".($kunde->getFreischaltung()!=null?($kunde->getFreischaltung()?1:0):($kunde_alt->getFreischaltung()?1:0))."\" WHERE ".DB_F_KUNDEN_PK_EMAIL." = \"".mysqli_escape_string($this->con, $kunde->getEmail())."\"")===TRUE;
		
		if(count($kunde->getInteressen())!=0){
			$interessenIds_alt = array();
			
			foreach ($kunde_alt->getInteressen() as $interesse_alt)
				array_push($interessenIds_alt,$interesse_alt->getId());
			
			$interessen_neu="";
			
			foreach ($kunde->getInteressen() as $interesse){
				if($interesse instanceof Interesse){
					$interessen_neu = $interessen_neu.", \"".$interesse->getId()."\"";
					if(!in_array($interesse->getId(),$interessenIds_alt))
						$success=$success?$this->interesseKundeZuweisen($interesse, $kunde):$success;
				}
			}
			$interessen_neu = substr($interessen_neu,2);
			$success=$success?$this->query("DELETE FROM ".DB_TB_KUNDEN_INTERESSEN." WHERE ".DB_F_KUNDEN_INTERESSEN_PK_KUNDEN." = \"".$kunde->getEmail()."\" AND (".DB_F_KUNDEN_INTERESSEN_PK_INTERESSEN." NOT IN(".$interessen_neu."))"):$success;
		}
		
		return $success;
	}
	
	function dienstleistungUpdaten(Dienstleistung $dienstleistung){
		$dienstleistung_alt=$this->getDienstleistung($dienstleistung->getKuerzel(), $dienstleistung->getHaartyp());
		$success = $this->query("UPDATE ".DB_TB_DIENSTLEISTUNGEN." SET ".DB_F_DIENSTLEISTUNGEN_NAME." = \"" .mysqli_escape_string($this->con, $dienstleistung->getName())."\", ".DB_F_DIENSTLEISTUNGEN_BENOETIGTEEINHEITEN." = \"".mysqli_escape_string($this->con, $dienstleistung->getBenoetigteEinheiten())."\", ".DB_F_DIENSTLEISTUNGEN_PAUSENEINHEITEN." = \"".mysqli_escape_string($this->con, $dienstleistung->getPausenEinheiten())."\", ".DB_F_DIENSTLEISTUNGEN_GRUPPIERUNG." = ".$dienstleistung->getGruppierung()." WHERE ".DB_F_DIENSTLEISTUNGEN_PK_KUERZEL." = \"".mysqli_escape_string($this->con, $dienstleistung->getKuerzel())." AND ".DB_F_DIENSTLEISTUNGEN_PK_HAARTYP." = \"".mysqli_escape_string($this->con, $dienstleistung->getHaartyp()->getKuerzel())."\"")===TRUE;
		
		$skillsIds_alt = array();
		
		foreach ($dienstleistung->getSkills() as $skill_alt)
			array_push($skillsIds_alt,$skill_alt->getId());
		
		$skills_neu="";
		
		foreach ($dienstleistung->getSkills() as $skill){
			if($skill instanceof Skill){
				$skills_neu = $skills_neu.", \"".$skill->getId()."\"";
				if(!in_array($skill->getId(),$skillsIds_alt))
					$success=$success?$this->skillDienstleistungZuweisen($skill, $dienstleistung):$success;
			}
		}
		$skills_neu = substr($skills_neu,2);
		$success=$success?$this->query("DELETE FROM ".DB_TB_DIENSTLEISTUNGEN_SKILLS." WHERE ".DB_F_DIENSTLEISTUNGEN_SKILLS_PK_DIENSTLEISTUNGEN." = \"".mysqli_escape_string($this->con, $dienstleistung->getKuerzel())."\" AND (".DB_F_DIENSTLEISTUNGEN_SKILLS_PK_SKILLS." NOT IN(".$skills_neu."))"):$success;
		
		
		$ausstattungenIds_alt = array();
		
		foreach ($dienstleistung_alt->getArbeitsplatzausstattungen() as $ausstattung_alt)
			array_push($ausstattungenIds_alt,$ausstattung_alt->getId());
		
		$ausstattungen_neu="";
		
		foreach ($dienstleistung->getArbeitsplatzausstattungen() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung){
				$ausstattungen_neu = $ausstattungen_neu.", \"".$ausstattung->getId()."\"";
				if(!in_array($ausstattung->getId(),$ausstattungenIds_alt))
					$success=$success?$this->arbeitsplatzausstattungDienstleistungZuweisen($ausstattung, $dienstleistung):$success;
			}
		}
		$ausstattungen_neu = substr($ausstattungen_neu,2);
		$success=$success?$this->query("DELETE FROM ".DB_TB_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN." WHERE ".DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_DIENSTLEISTUNGEN." = \"".mysqli_escape_string($this->con, $dienstleistung->getKuerzel())."\" AND (".DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN." NOT IN(".$ausstattungen_neu."))"):$success;
		
		return $success;
	}
	
	
	function authentifiziereKunde(Kunde $kunde, $passwort){
		
		try{
			$abf = $this->selectQuery(DB_TB_KUNDEN, DB_F_KUNDEN_PASSWORT, DB_F_KUNDEN_PK_EMAIL." = \"".mysqli_escape_string($this->con,$kunde->getEmail())."\"");
		} catch (Exception $e){
			throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$e->getMessage(), DB_ERR_VIEW_DB_FAIL);
		}
		
		if ($abf == false)
			return false;
			try{
				$row = mysqli_fetch_assoc($abf);
				if(isset($row[DB_F_KUNDEN_PASSWORT]))
				if(strcmp($row[DB_F_KUNDEN_PASSWORT],$passwort)==0)
					return true;
			}catch(Exception $e){
				throw new DB_Exception(500, "Unbekannter Fehler! Fehlermessage: ".$e->getMessage(), DB_ERR_VIEW_UK_FAIL);
			}
			
		return false;
	}
	
	function authentifiziereMitarbeiter(Mitarbeiter $mitarbeiter, $passwort){
	
		try{
			$abf = $this->selectQuery(DB_TB_MITARBEITER, DB_F_MITARBEITER_PASSWORT, DB_F_MITARBEITER_PK_SVNR." = \"".mysqli_escape_string($this->con,$mitarbeiter->getSvnr())."\"");
		} catch (Exception $e){
			throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$e->getMessage(), DB_ERR_VIEW_DB_FAIL);
		}
	
		if ($abf == false)
			return false;
		try{
			$row = mysqli_fetch_assoc($abf);
			if(isset($row[DB_F_MITARBEITER_PASSWORT]))
			if(strcmp($row[DB_F_MITARBEITER_PASSWORT],$passwort)==0)
				return true;
		}catch(Exception $e){
			throw new DB_Exception(500, "Unbekannter Fehler! Fehlermessage: ".$e->getMessage(), DB_ERR_VIEW_UK_FAIL);
		}
			
		return false;
	}
	
	
	function getWochentag($kuerzel){
		$abf = $this->selectQuery(DB_TB_WOCHENTAGE, "*", DB_F_WOCHENTAGE_PK_KUERZEL." = \"".$kuerzel."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Wochentag($row[DB_F_WOCHENTAGE_PK_KUERZEL],$row[DB_F_WOCHENTAGE_BEZEICHNUNG]);
	}
	
	function getInteresse($id){
		$abf = $this->selectQuery(DB_TB_INTERESSEN, "*", DB_F_INTERESSEN_PK_ID." = \"".$id."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Interesse($row[DB_F_INTERESSEN_PK_ID], $row[DB_F_INTERESSEN_BEZEICHNUNG]);
	}
	
	function getProduktkategorie($kuerzel){
		$abf = $this->selectQuery(DB_TB_PRODUKTKATEGORIEN, "*", DB_F_PRODUKTKATEGORIEN_PK_KUERZEL." = \"".$kuerzel."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Produktkategorie($kuerzel, $row[DB_F_PRODUKTKATEGORIEN_BEZEICHNUNG]);
	}
	
	function getProdukt($id){
		$abf = $this->selectQuery(DB_TB_PRODUKTE, "*", DB_F_PRODUKTE_PK_ID." = \"".$id."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Produkt($row[DB_F_PRODUKTE_PK_ID], $row[DB_F_PRODUKTE_NAME], $row[DB_F_PRODUKTE_HERSTELLER], $row[DB_F_PRODUKTE_BESCHREIBUNG], $row[DB_F_PRODUKTE_PREIS], $row[DB_F_PRODUKTE_BESTAND], $this->getProduktkategorie($row[DB_F_PRODUKTE_PRODUKTKATEGORIE]));
	}
	
	function getHaartyp($kuerzel){
		$abf = $this->selectQuery(DB_TB_HAARTYPEN, "*", DB_F_HAARTYPEN_PK_KUERZEL." = \"".$kuerzel."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Haartyp($row[DB_F_HAARTYPEN_PK_KUERZEL], $row[DB_F_HAARTYPEN_BEZEICHNUNG]);
	}
	
	function getArbeitsplatzausstattung($id){
		$abf = $this->selectQuery(DB_TB_ARBEITSPLATZAUSSTATTUNGEN, "*", DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID." = \"".$id."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Arbeitsplatzausstattung($row[DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID], $row[DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME]);
	}

	function getSkill($id){
		$abf = $this->selectQuery(DB_TB_SKILLS, "*", DB_F_SKILLS_PK_ID." = \"".$id."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$row = mysqli_fetch_assoc($abf);
		return new Skill($row[DB_F_SKILLS_PK_ID], $row[DB_F_SKILLS_BESCHREIBUNG]);
	}
	
	function getArbeitsplatz($nummer){
		$abf = $this->selectQuery(DB_TB_ARBEITSPLATZRESSOURCEN, "*", DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER." = \"".$nummer."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		$abf = $this->selectQuery(DB_VIEW_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN, "*", DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZRESSOURCEN." = \"".$nummer."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$ausstattungen = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($ausstattungen, $this->getArbeitsplatzausstattung($row[DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZAUSSTATTUNGEN]));
		}
		
		return new Arbeitsplatz($nummer, $main[DB_F_ARBEITSPLATZRESSOURCEN_NAME], $ausstattungen);
	}
	
	function getDienstleistung($kuerzel,Haartyp $haartyp){
		$abf = $this->selectQuery(DB_TB_DIENSTLEISTUNGEN, "*", DB_F_DIENSTLEISTUNGEN_PK_KUERZEL." = \"".$kuerzel."\" AND ".DB_F_DIENSTLEISTUNGEN_PK_HAARTYP." = \"".$haartyp->getKuerzel()."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		$abf = $this->selectQuery(DB_VIEW_ARBEITSPLATZAUSSTATTUNGEN_DIENSTLEISTUNGEN, "*", DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_DIENSTLEISTUNGEN." = \"".$kuerzel."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$ausstattungen = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($ausstattungen, new Arbeitsplatzausstattung($row[DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID], $row[DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME]));
		}
		
		$abf = $this->selectQuery(DB_VIEW_SKILLS_DIENSTLEISTUNGEN, "*", DB_F_DIENSTLEISTUNGEN_SKILLS_PK_DIENSTLEISTUNGEN." = \"".$kuerzel."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$skills = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($skills, new Skill($row[DB_F_SKILLS_PK_ID], $row[DB_F_SKILLS_BESCHREIBUNG]));
		}
		
		return new Dienstleistung($kuerzel, $haartyp, $main[DB_F_DIENSTLEISTUNGEN_NAME], $main[DB_F_DIENSTLEISTUNGEN_BENOETIGTEEINHEITEN], $main[DB_F_DIENSTLEISTUNGEN_PAUSENEINHEITEN], $skills, $ausstattungen, $main[DB_F_DIENSTLEISTUNGEN_GRUPPIERUNG]);
	}
	
	function getDienstzeit(Mitarbeiter $mitarbeiter, Wochentag $wochentag){
		$abf = $this->selectQuery(DB_TB_DIENSTZEITEN, "*", DB_F_DIENSTZEITEN_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\" AND ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE." = \"".$wochentag->getKuerzel()."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		return new Dienstzeit($wochentag, new DateTime($main[DB_F_DIENSTZEITEN_BEGINN]), new DateTime($main[DB_F_DIENSTZEITEN_ENDE]));
	}
	
	function getKunde($email){
		$abf = $this->selectQuery(DB_TB_KUNDEN, "*", DB_F_KUNDEN_PK_EMAIL." = \"".mysqli_escape_string($this->con,$email)."\"");
		//echo "<br>".mysqli_escape_string($this->con,$email)."<br>";
		//var_dump($abf);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		$abf = $this->selectQuery(DB_VIEW_INTERESSEN_KUNDEN, "*", DB_F_KUNDEN_INTERESSEN_PK_KUNDEN." = \"".$email."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$interessen = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($interessen, new Interesse($row[DB_F_INTERESSEN_PK_ID], $row[DB_F_INTERESSEN_BEZEICHNUNG]));
		}
		
	return new Kunde($email, $main[DB_F_KUNDEN_VORNAME], $main[DB_F_KUNDEN_NACHNAME], $main[DB_F_KUNDEN_TELNR], $main[DB_F_KUNDEN_FREISCHALTUNG], $main[DB_F_KUNDEN_FOTO], $interessen);
	}
	
	function getKundeToken(Kunde $kunde){
		$abf = $this->selectQuery(DB_TB_KUNDEN, DB_F_KUNDEN_TOKEN.", ".DB_F_KUNDEN_TIMESTAMP, DB_F_KUNDEN_PK_EMAIL." = \"".mysqli_escape_string($this->con,$kunde->getEmail())."\"");
		//echo "<br>".mysqli_escape_string($this->con,$email)."<br>";
		//var_dump($abf);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		return new Token($main[DB_F_KUNDEN_TOKEN], new DateTime($main[DB_F_KUNDEN_TIMESTAMP]));
	}
	
	function getMitarbeiter($svnr){
		$abf = $this->selectQuery(DB_TB_MITARBEITER, "*", DB_F_MITARBEITER_PK_SVNR." = \"".$svnr."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		$abf = $this->selectQuery(DB_VIEW_SKILLS_MITARBEITER, "*", DB_F_MITARBEITER_SKILLS_PK_MITARBEITER." = \"".$svnr."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$skills = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($skills, new Skill($row[DB_F_SKILLS_PK_ID], $row[DB_F_SKILLS_BESCHREIBUNG]));
		}
		
		$abf = $this->selectQuery(DB_TB_URLAUBE, "*", DB_F_URLAUBE_PK_MITARBEITER." = \"".$svnr."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$urlaube = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($urlaube, new Urlaub(new DateTime($row[DB_F_URLAUBE_PK_BEGINN]), new DateTime($row[DB_F_URLAUBE_ENDE])));
		}
		
		$abf = $this->selectQuery(DB_TB_DIENSTZEITEN, "*", DB_F_DIENSTZEITEN_PK_MITARBEITER." = \"".$svnr."\" ORDER BY CASE ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE." WHEN 'MO' THEN 1 WHEN 'DI' THEN 2 WHEN 'MI' THEN 3 WHEN 'DO' THEN 4 WHEN 'FR' THEN 5 WHEN 'SA' THEN 6 WHEN 'SO' THEN 7 END ASC");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$dienstzeiten = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($dienstzeiten, new Dienstzeit($this->getWochentag($row[DB_F_DIENSTZEITEN_PK_WOCHENTAGE]),new DateTime($row[DB_F_DIENSTZEITEN_BEGINN]) , new DateTime($row[DB_F_DIENSTZEITEN_ENDE])));
		}
		
		return new Mitarbeiter($svnr, $main[DB_F_MITARBEITER_VORNAME], $main[DB_F_MITARBEITER_NACHNAME], $main[DB_F_MITARBEITER_MOTTO], $skills, $main[DB_F_MITARBEITER_ADMIN], $urlaube, $dienstzeiten);
	}
	
	function getWerbung($nummer){
		$abf = $this->selectQuery(DB_TB_WERBUNG, "*", DB_F_WERBUNG_PK_NUMMER." = \"".$nummer."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		$abf = $this->selectQuery(DB_VIEW_INTERESSEN_WERBUNG, "*", DB_F_WERBUNG_INTERESSEN_PK_WERBUNG." = \"".$nummer."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$interessen = array();
		while ($row = mysqli_fetch_assoc($abf)){
			array_push($interessen, new Interesse($row[DB_F_INTERESSEN_PK_ID], $row[DB_F_INTERESSEN_BEZEICHNUNG]));
		}
		$res = new Werbung($nummer, $main[DB_F_WERBUNG_TITEL], $main[DB_F_WERBUNG_TEXT], new DateTime($main[DB_F_WERBUNG_DATUM]), $interessen);
		$res->updateFotos();
		return $res;
	}
	
	function getTermin(DateTime $zeitstempel, Mitarbeiter $mitarbeiter){
		$abf = $this->selectQuery(DB_TB_ZEITTABELLE, "*", DB_F_ZEITTABELLE_PK_ZEITSTEMPEL." = \"".$zeitstempel->format(DB_FORMAT_DATETIME)."\" AND ".DB_F_ZEITTABELLE_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\""); 
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		if($abf->num_rows == 0) return null;
		$main = mysqli_fetch_assoc($abf);
		
		return new Termin($zeitstempel, $mitarbeiter, $this->getArbeitsplatz($main[DB_F_ZEITTABELLE_ARBEITSPLATZ]), $this->getKunde($main[DB_F_ZEITTABELLE_KUNDE]), $main[DB_F_ZEITTABELLE_FRISURWUNSCH], $this->getDienstleistung($main[DB_F_ZEITTABELLE_DIENSTLEISTUNG], $this->getHaartyp($main[DB_F_ZEITTABELLE_DIENSTLEISTUNG_HAARTYP])));
	}
	
	
	
	function getAllWochentag(){
		$abf = $this->selectQueryField(DB_TB_WOCHENTAGE, DB_F_WOCHENTAGE_PK_KUERZEL);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getWochentag($row[DB_F_WOCHENTAGE_PK_KUERZEL]));
		return $res;
	}
	
	function getAllInteresse(){
		$abf = $this->selectQueryField(DB_TB_INTERESSEN, DB_F_INTERESSEN_PK_ID);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getInteresse($row[DB_F_INTERESSEN_PK_ID]));
		return $res;
	}
	
	function getAllProduktkategorie(){
		$abf = $this->selectQueryField(DB_TB_PRODUKTKATEGORIEN, DB_F_PRODUKTKATEGORIEN_PK_KUERZEL);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getProduktkategorie($row[DB_F_PRODUKTKATEGORIEN_PK_KUERZEL]));
		return $res;
	}
	
	function getAllProdukt(Produktkategorie $kategorie = null){
		$abf = $kategorie==null?$this->selectQueryField(DB_TB_PRODUKTE, DB_F_PRODUKTE_PK_ID):$this->selectQuery(DB_TB_PRODUKTE, DB_F_PRODUKTE_PK_ID, DB_F_PRODUKTE_PRODUKTKATEGORIE."=\"".mysqli_escape_string($this->con, $kategorie->getKuerzel())."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getProdukt($row[DB_F_PRODUKTE_PK_ID]));
		return $res;
	}
	
	function getAllHaartyp(){
		$abf = $this->selectQueryField(DB_TB_HAARTYPEN, DB_F_HAARTYPEN_PK_KUERZEL);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getHaartyp($row[DB_F_HAARTYPEN_PK_KUERZEL]));
		return $res;
	}
	
	function getAllArbeitsplatzausstattung(){
		$abf = $this->selectQueryField(DB_TB_ARBEITSPLATZAUSSTATTUNGEN, DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getArbeitsplatzausstattung($row[DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID]));
		return $res;
	}
	
	function getAllSkill(){
		$abf = $this->selectQueryField(DB_TB_SKILLS, DB_F_SKILLS_PK_ID);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getSkill($row[DB_F_SKILLS_PK_ID]));
		return $res;
	}
	
	
	function getAllArbeitsplatz(){
		$abf = $this->selectQueryField(DB_TB_ARBEITSPLATZRESSOURCEN, DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getArbeitsplatz($row[DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER]));
		return $res;
	}
	
	function getAllDienstleistung(){
		$abf = $this->selectQueryField(DB_TB_DIENSTLEISTUNGEN, DB_F_DIENSTLEISTUNGEN_PK_HAARTYP." , ".DB_F_DIENSTLEISTUNGEN_PK_KUERZEL);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getDienstleistung($row[DB_F_DIENSTLEISTUNGEN_PK_KUERZEL],$this->getHaartyp($row[DB_F_DIENSTLEISTUNGEN_PK_HAARTYP])));
		return $res;
	}
	
	function getAllDienstzeit(){
		$abf = $this->selectQueryField(DB_TB_DIENSTZEITEN, DB_F_DIENSTZEITEN_PK_WOCHENTAGE." , ".DB_F_DIENSTZEITEN_BEGINN." , ".DB_F_DIENSTZEITEN_ENDE);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,new Dienstzeit($this->getWochentag($row[DB_F_DIENSTZEITEN_PK_WOCHENTAGE]), new DateTime($row[DB_F_DIENSTZEITEN_BEGINN]), new DateTime($row[DB_F_DIENSTZEITEN_ENDE])));
		return $res;
	}
	
	function getAllKunde(){
		$abf = $this->selectQueryField(DB_TB_KUNDEN, DB_F_KUNDEN_PK_EMAIL);
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getKunde($row[DB_F_KUNDEN_PK_EMAIL]));
		return $res;
	}
	
	function getAllMitarbeiter($pw = true, $svnr = true){
		$abf = $this->selectQuery(DB_TB_MITARBEITER, DB_F_MITARBEITER_PK_SVNR, ($pw?DB_F_MITARBEITER_PASSWORT." IS NOT NULL AND ".DB_F_MITARBEITER_PASSWORT."!=\"\"":"").($pw&&$svnr?" AND ":"").($svnr?DB_F_MITARBEITER_PK_SVNR." > 1000010100":""));
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getMitarbeiter($row[DB_F_MITARBEITER_PK_SVNR]));
		return $res;
	}
	
	function getAllNoMitarbeiter($svnr = true){
		$abf = $this->selectQuery(DB_TB_MITARBEITER, DB_F_MITARBEITER_PK_SVNR, "(".DB_F_MITARBEITER_PASSWORT." IS NULL OR ".DB_F_MITARBEITER_PASSWORT."=\"\")".($svnr?" AND ".DB_F_MITARBEITER_PK_SVNR." > 1000010100":""));
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getMitarbeiter($row[DB_F_MITARBEITER_PK_SVNR]));
		return $res;
	}
	
	function getAllWerbung(DateTime $abdatum = null, array $interessen = null){
		$abf = $abdatum==null?$this->selectQueryField(DB_TB_WERBUNG, DB_F_WERBUNG_PK_NUMMER):$this->selectQuery(DB_TB_WERBUNG, DB_F_WERBUNG_PK_NUMMER, DB_F_WERBUNG_DATUM.">=\"".$abdatum->format(DB_FORMAT_DATETIME)."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$res = array();
		while($row = mysqli_fetch_assoc($abf))
			array_push($res,$this->getWerbung($row[DB_F_WERBUNG_PK_NUMMER]));
		if($interessen != null){
			if(is_array($interessen)){
				$interessenIds = array();
				foreach($interessen as $interesse)
					if(($interesse instanceof Interesse))
						array_push($interessenIds, $interesse->getId());
					else
						throw new DB_Exception(400, "Ein Objekt im Interessen-Array ist nicht vom Typ Interesse!", DB_ERR_VIEW_PARAM_FAIL);
				$temp = array();
				foreach ($res as $werbung){
					$in = false;
					foreach($werbung->getInteressen() as $interesse)
						if(in_array($interesse->getId(), $interessenIds))
							$in = true;
					if($in)
						array_push($temp, $werbung);
				}
				$res=$temp;
			}
		}
		return $res;
	}
	
	function getKundeMailBySessionId($session_id){
		$abf = $this->selectQuery(DB_TB_SESSION, DB_F_SESSION_KUNDEN, DB_F_SESSION_PK_ID."=\"".$session_id."\"");
		if($abf==false) throw new DB_Exception(500, "Datenbankfehler: Abfrage nicht möglich! Fehlermessage: ".$this->con->error, DB_ERR_VIEW_DB_FAIL);
		$row = mysqli_fetch_assoc($abf);
		return $row[DB_F_SESSION_KUNDEN];
	}
	
	
	
	function call($name, $params){
		$this->query("CALL ".$name."(".$params.", @out);");
		return $this->query("SELECT @out");
	}
	
	function selectQuery($name, $fields, $where_clause){
		return $this->query("SELECT ".$fields." FROM ".$name." WHERE ".$where_clause.";");
	}
	
	function selectQueryField($name, $fields){
		return $this->query("SELECT ".$fields." FROM ".$name);
	}
	
	function selectQueryTable($name){
		return $this->selectQuery($name, "*");
	}
	
	function query($query_string){
		 //var_dump($query_string);
		if(isset($this->con))
			return mysqli_query($this->con, $query_string);
		else
			throw new DB_Exception(503, "Keine Datenbankverbindung! Fehlermessage: {".$this->con->error."}", DB_ERR_VIEW_NO_CONNECTION);
	}
	
	
	function escape_string($string){
		return mysqli_escape_string($this->con, $string);
	}
	
	
	function __destruct(){
		if($this->con instanceof mysqli)
			mysqli_close($this->con);
	}
	
	function getTermineVonBis(DateTime $von, DateTime $bis) {
		if(!($von->format("U") <= $bis->format("U")))
			throw new DB_Exception(400, "Von-Wert größer als Bis-Wert. Werte: Von: ".$von->format("U")." Bis: ".$bis->format("U"), DB_ERR_VIEW_VON_BIS);
	
		$interval = new DateInterval('PT15M');
	
		$realEnd = $bis;
		$realEnd->add($interval);
	
		$period = new DatePeriod(
				$von,
				$interval,
				$realEnd
		);
		$db=new db_con("conf/db.php",true);
	
		foreach($period as $date) {
			$array[] = $date->format('d.m.Y H:i');
		}
	
		return $array;
	}
	
	function checkArbeitsplatzFree(DateTime $von, DateTime $bis, array $dienstleistungen) {
		$ausstattungen = array();
		var_dump($dienstleistungen);
		foreach($dienstleistungen as $dienstleistung)
		foreach ($dienstleistung->getArbeitsplatzausstattungen() as $ausstattung){
			$a = false;
			foreach ($ausstattungen as $av)
			if($av->getId() == $ausstattung->getId())
				$a=true;
			if(!a)array_push($ausstattungen, $ausstattung);
		}
		$arbeitsplaetze = $this->getAllArbeitsplatz();
		$arbeitsplaetzeOk = array();
		foreach ($arbeitsplaetze as $arbeitsplatz){
			$ok = true;
			foreach ($ausstattungen as $ausstattung){
				$a = false;
				foreach ($arbeitsplatz->getAusstattung() as $auss)
				if($auss->getId() == $ausstattung->getId())$a=true;
				if(!$a)
					$ok=false;
			}
			if($ok)
				array_push($arbeitsplaetzeOk, $arbeitsplatz);
		}
		$ret = array();
		foreach($arbeitsplaetzeOk as $arbeitsplatz){
			$abf = $this->query("SELECT * FROM ".DB_TB_ZEITTABELLE." WHERE ".DB_F_ZEITTABELLE_ARBEITSPLATZ." = \"".$arbeitsplatz->getNummer()."\" AND ".DB_F_ZEITTABELLE_PK_ZEITSTEMPEL." BETWEEN ".$von->format(DB_FORMAT_DATETIME)." AND ".$bis->format(DB_FORMAT_DATETIME));
			if($abf->num_rows == 0)
				array_push($ret, $arbeitsplatz);
		}
		return  $ret;
	}
	
	function getArbeitsplaetzeFuerDienstleistung(array $dienstleistungen) {
		$ausstattungen = array();
//		var_dump($dienstleistungen);
		foreach($dienstleistungen as $dienstleistung)
		foreach ($dienstleistung->getArbeitsplatzausstattungen() as $ausstattung){
			$a = false;
			foreach ($ausstattungen as $av)
			if($av->getId() == $ausstattung->getId())
				$a=true;
			if(!a)array_push($ausstattungen, $ausstattung);
		}
		$arbeitsplaetze = $this->getAllArbeitsplatz();
		$arbeitsplaetzeOk = array();
		foreach ($arbeitsplaetze as $arbeitsplatz){
			$ok = true;
			foreach ($ausstattungen as $ausstattung){
				$a = false;
				foreach ($arbeitsplatz->getAusstattung() as $auss)
				if($auss->getId() == $ausstattung->getId())$a=true;
				if(!$a)
					$ok=false;
			}
			if($ok)
				array_push($arbeitsplaetzeOk, $arbeitsplatz);
		}
		return $arbeitsplaetzeOk;
	}
	
	function getBelegteZeitenVonArbeitsplaetzen(DateTime $von, DateTime $bis, $arbeitsplaetze){
		$ret=array();
		$frees = array();
		foreach ($arbeitsplaetze as $arbeitsplatz){
			$zeiten = array();
			$abf = $this->query("SELECT ".DB_F_ZEITTABELLE_PK_ZEITSTEMPEL." FROM ".DB_TB_ZEITTABELLE." WHERE ".DB_F_ZEITTABELLE_ARBEITSPLATZ."=\"".$arbeitsplatz->getNummer()."\" AND ".DB_F_ZEITTABELLE_PK_ZEITSTEMPEL." BETWEEN ".$von->format(DB_FORMAT_DATETIME)." AND ".$bis->format(DB_FORMAT_DATETIME));
			while($row=mysqli_fetch_assoc($abf))
				array_push($zeiten, new DateTime($row[DB_F_ZEITTABELLE_PK_ZEITSTEMPEL]));
			$frees[$arbeitsplatz->getNummer()]=$zeiten;
		}
	
		// 		die zeiten suchen die in allen vorkommen und in ret speichern und returnen
	
	}
	
	function getMitarbeiterMitSkills(array $dienstleistungen) {
		//select svnr from mitarbeiter inner join mitarbeiter_has_skills on svnr = mitarbeiter_svnr WHERE skills_bez in (1,2,3) group by svnr having count(*) = 3
	}
	function getNotFreeMitarbeiterMitSkills(array $dienstleistungen) {
 		$abf = $this->query("select svnr, zeitstempel from (select svnr from mitarbeiter inner join mitarbeiter_has_skills on svnr = mitarbeiter_svnr WHERE skills_bez in (1,2,3) group by svnr having count(*) = 3) as cm inner join zeittabelle on svnr = zeittabelle.mitarbeiter left join dienstleistungen on dienstleistungen_kuerzel = kuerzel and haartypen_kuerzel = Dienstleistungen_Haartypen_Kuerzel");
		$array = array();
		while($row=mysqli_fetch_assoc($abf)){
			if(!isset($array[$row[DB_F_ZEITTABELLE_PK_MITARBEITER]])){
				$array[$row[DB_F_ZEITTABELLE_PK_MITARBEITER]] = array();
			}
			$array[$row[DB_F_ZEITTABELLE_PK_MITARBEITER]][] = new DateTime($row[DB_F_ZEITTABELLE_PK_ZEITSTEMPEL]);
		}
	}
	
	function getNotFreeArbeitsplatzMitAustattungen()
	 {
// 		select arbeitsplatznr, zeitstempel from (select arbeitsplatzressourcen_arbeitsplatznr from arbeitsplatzressourcen inner join arbeitsplatzressourcen_has_arbeitsplatzausstattungen on arbeitsplatznr = arbeitsplatzressourcen_arbeitsplatznr WHERE Arbeitsplatzausstattungen_ID in (6) group by arbeitsplatznr having count(*) = 1) as cm inner join zeittabelle on arbeitsplatzressourcen_arbeitsplatznr = arbeitsplatznr left join dienstleistungen on dienstleistungen_kuerzel = kuerzel and haartypen_kuerzel = Dienstleistungen_Haartypen_Kuerzel
	
	}

	
}
?>