<?php
include_once("conf/db_const.php");
include_once("classes/Arbeitsplatz.php");
include_once("classes/Arbeitsplatzausstattung.php");
include_once("classes/Dienstleistung.php");
include_once("classes/Dienstzeit.php");
include_once("classes/Haartyp.php");
include_once("classes/Interesse.php");
include_once("classes/Kunde.php");
include_once("classes/Mitarbeiter.php");
include_once("classes/Produkt.php");
include_once("classes/Skill.php");
include_once("classes/Termin.php");
include_once("classes/Urlaub.php");
include_once("classes/Werbung.php");
include_once("classes/Wochentag.php");

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
	
	
	function __construct($conf_file, $admin){
		// include config
		$this->changeConfig($conf_file);
		$this->connect($admin);
	}
	
	
	function changeConfig($conf_file){ // throws Exception
		// include config
		include $conf_file;
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
		else throw new Exception("Ung�ltige Konfigurationsdatei!");
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
			throw new Exception("Verbindung zu Datenbank konne nicht hergestellt werden! Fehlermessage: {".$e->getMessage()."}");
		}
	}
	
	
	function terminEintragen($beginn, $dienstleistungId, $mitarbeiterId, $kundeId, $arbeitsplatzId, $foto){
		
		return $this->call(DB_PC_TERMIN_EINTRAGEN, mysqli_escape_string($this->con,$beginn).",".mysqli_escape_string($this->con,$dienstleistungId).",".$mitarbeiterId.",".mysqli_escape_string($this->con,$kundeId).",".$arbeitsplatzId.",".mysqli_escape_string($this->con,$foto));
	}
	
	
	function getFreieTermine(DateTime $von, DateTime $bis){
		if($von->format("U") <= $bis->format("U"))
			return  $this->call(DB_PC_FREIE_TERMINE, $von->format(DB_FORMAT_DATETIME)." , ".$bis->format(DB_FORMAT_DATETIME));
		else
			throw new Exception("Von-Wert darf nicht gr��er sein als Bis-Wert!");
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
	
	function produktEintragen(Produkt $produkt){
		return $this->query("INSERT INTO ".DB_TB_PRODUKTE." (".DB_F_PRODUKTE_PK_ID.", ".DB_F_PRODUKTE_NAME.", ".DB_F_PRODUKTE_HERSTELLER.", ".DB_F_PRODUKTE_BESCHREIBUNG.", ".DB_F_PRODUKTE_PREIS.", ".DB_F_PRODUKTE_BESTAND.") VALUES (\"".$produkt->getId()."\", \"".mysqli_escape_string($this->con,$produkt->getName())."\", \"".mysqli_escape_string($this->con,$produkt->getHersteller())."\", \"".mysqli_escape_string($this->con,$produkt->getBeschreibung())."\", \"".$produkt->getPreis()."\", \"".$produkt->getBestand()."\")")===TRUE;
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
		$success = $this->query("INSERT INTO ".DB_TB_WERBUNG." (".DB_F_WERBUNG_PK_NUMMER.") VALUES (\"".$werbung->getNummer()."\")")===TRUE;
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
		$success = $this->query("INSERT INTO ".DB_TB_MITARBEITER." (".DB_F_MITARBEITER_SVNR.", ".DB_F_MITARBEITER_VORNAME.", ".DB_F_MITARBEITER_NACHNAME.", ".DB_F_MITARBEITER_ADMIN.") VALUES (\"".$mitarbeiter->getSvnr()."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getVorname())."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getNachname())."\", \"".$mitarbeiter->getAdmin()."\")")===TRUE;
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
		$success = $this->query("INSERT INTO ".DB_TB_KUNDEN." (".DB_F_KUNDEN_EMAIL.", ".DB_F_KUNDEN_VORNAME.", ".DB_F_KUNDEN_NACHNAME.", ".DB_F_KUNDEN_TELNR.", ".DB_F_KUNDEN_FREISCHALTUNG.", ".DB_F_KUNDEN_FOTO.") VALUES (\"".mysqli_escape_string($this->con,$kunde->getEmail())."\", \"".mysqli_escape_string($this->con,$kunde->getVorname())."\", \"".mysqli_escape_string($this->con,$kunde->getNachname())."\", \"".mysqli_escape_string($this->con,$kunde->getTelNr())."\", \"".$kunde->getFreischaltung()."\", \"".mysqli_escape_string($this->con,$kunde->getFoto())."\")")===TRUE;
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
		return $this->query("UPDATE ".DB_TB_PRODUKTE." SET ".DB_F_PRODUKTE_NAME." = \"" .mysqli_escape_string($this->con, $produkt->getName())."\", ".DB_F_PRODUKTE_HERSTELLER." = \"".mysqli_escape_string($this->con, $produkt->getHersteller())."\", ".DB_F_PRODUKTE_BESCHREIBUNG." = \"".mysqli_escape_string($this->con, $produkt->getBeschreibung())."\", ".DB_F_PRODUKTE_PREIS." = \"".$produkt->getPreis()."\", ".DB_F_PRODUKTE_BESTAND." = \"".$produkt->getBestand()."\" WHERE ".DB_F_PRODUKTE_PK_ID." = \"".$produkt->getId()."\"")===TRUE;
	}
	
	function wochentagUpdaten(Wochentag $wochentag){
		return $this->query("UPDATE ".DB_TB_WOCHENTAGE." SET ".DB_F_WOCHENTAGE_BEZEICHNUNG." = \"" .mysqli_escape_string($this->con, $wochentag->getBezeichnung())."\" WHERE ".DB_F_WOCHENTAGE_PK_KUERZEL." = \"".mysqli_escape_string($this->con, $wochentag->getKuerzel())."\"")===TRUE;
	}
	
	function dienstzeitUpdaten(Dienstzeit $dienstzeit, Mitarbeiter $mitarbeiter){
		return $this->query("UPDATE ".DB_TB_DIENSTZEITEN." SET ".DB_F_DIENSTZEITEN_BEGINN." = \"" .$dienstzeit->getBeginn()->format(DB_FORMAT_TIME)."\", ".DB_F_DIENSTZEITEN_ENDE." = \"".$dienstzeit->getEnde()->format(DB_FORMAT_TIME)."\" WHERE ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE." = \"".mysqli_escape_string($this->con, $dienstzeit->getWochentag()->getKuerzel())."\" AND ".DB_F_DIENSTZEITEN_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\"")===TRUE;
	}
	
	
	function interesseWerbungZuweisungUpdaten(Werbung $werbung){
		$success=true;
		$interessenIds="";		
		$abf=$this->selectQuery(DB_TB_WERBUNG_INTERESSEN, DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN, DB_F_WERBUNG_INTERESSEN_PK_WERBUNG." = \"".$werbung->getNummer()."\"");
		$interessenIdsAltArr=array();
		while($row = mysqli_fetch_row($abf)){
			array_push($interessenIdsAltArr,$row->{DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN});
		}
		foreach ($werbung->getInteressen() as $interesse){
			if($interesse instanceof Interesse){
				$interessenIds = $interessenIds.", ".$interesse->getId();
				if(!in_array($interesse->getId(),$interessenIdsAltArr)){
					$success=$success?$this->interesseWerbungZuweisen($interesse, $werbung):$success;
				}
			}
		}
		$interessenIds=substr($interessenIds,2);
		$success=$success?$this->query("DELETE FROM ".DB_TB_WERBUNG_INTERESSEN." WHERE ".DB_F_WERBUNG_INTERESSEN_PK_WERBUNG." = \"".$werbung->getNummer()."\" AND ".DB_F_WERBUNG_INTERESSEN_PK_INTERESSEN." NOT IN( ".$interessenIds." )")===TRUE:$success;
		return $success;
	}
	
	//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
	
	function arbeitsplatzUpdaten(Arbeitsplatz $arbeitsplatz){
		$success = $this->query("INSERT INTO ".DB_TB_ARBEITSPLATZRESSOURCEN." (".DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER.", ".DB_F_ARBEITSPLATZRESSOURCEN_NAME.") VALUES (\"".$arbeitsplatz->getNummer()."\", \"".mysqli_escape_string($this->con,$arbeitsplatz->getName())."\")")===TRUE;
		foreach ($arbeitsplatz->getAusstattung() as $ausstattung){
			if($ausstattung instanceof Arbeitsplatzausstattung)
				$success=$success?$this->arbeitsplatzausstattungArbeitsplatzZuweisen($ausstattung, $arbeitsplatz):$success;
		}
		return $success;
	}
	
	function mitarbeiterUpdaten(Mitarbeiter $mitarbeiter){
		$success = $this->query("INSERT INTO ".DB_TB_MITARBEITER." (".DB_F_MITARBEITER_SVNR.", ".DB_F_MITARBEITER_VORNAME.", ".DB_F_MITARBEITER_NACHNAME.", ".DB_F_MITARBEITER_ADMIN.") VALUES (\"".$mitarbeiter->getSvnr()."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getVorname())."\", \"".mysqli_escape_string($this->con,$mitarbeiter->getNachname())."\", \"".$mitarbeiter->getAdmin()."\")")===TRUE;
		foreach ($mitarbeiter->getSkills() as $skill){
			if($skill instanceof Skill)
				$success=$success?$this->skillMitarbeiterZuweisen($skill, $mitarbeiter):$success;
		}
		foreach ($mitarbeiter->getUrlaube() as $urlaub){
			if($urlaub instanceof Urlaub)
				$success=$success?$this->urlaubUpdaten($urlaub, $mitarbeiter):$success;
		}
		foreach ($mitarbeiter->getDienstzeiten() as $dienstzeit){
			if($dienstzeit instanceof Dienstzeit)
				$success=$success?$this->dienstzeitUpdaten($dienstzeit, $mitarbeiter):$success;
		}
		return $success;
	}
	
	function kundeUpdaten(Kunde $kunde){
		$success = $this->query("INSERT INTO ".DB_TB_KUNDEN." (".DB_F_KUNDEN_EMAIL.", ".DB_F_KUNDEN_VORNAME.", ".DB_F_KUNDEN_NACHNAME.", ".DB_F_KUNDEN_TELNR.", ".DB_F_KUNDEN_FREISCHALTUNG.", ".DB_F_KUNDEN_FOTO.") VALUES (\"".mysqli_escape_string($this->con,$kunde->getEmail())."\", \"".mysqli_escape_string($this->con,$kunde->getVorname())."\", \"".mysqli_escape_string($this->con,$kunde->getNachname())."\", \"".mysqli_escape_string($this->con,$kunde->getTelNr())."\", \"".$kunde->getFreischaltung()."\", \"".mysqli_escape_string($this->con,$kunde->getFoto())."\")")===TRUE;
		foreach ($kunde->getInteressen() as $interesse){
			if($interesse instanceof Interesse)
				$success=$success?$this->interesseKundeZuweisen($interesse, $kunde):$success;
		}
		return $success;
	}
	
	function dienstleistungUpdaten(Dienstleistung $dienstleistung){
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
	
	
	
	
	//-------------------------------------
	
	
	
	
	function authentifiziereKunde($kundeEmail){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_KUNDEN, DB_F_KUNDEN_FREISCHALTUNG, DB_F_KUNDEN_PK_EMAIL." = ".mysqli_escape_string($this->con,$kundeEmail)));
		if(isset($row[0]))
			if($row[0]){
				return true;
			}
			else
				throw new Exception("User ".$kundeEmail." nicht freigeschalten!");
		else
			throw new Exception("User ".$kundeEmail." nicht gefunden!");
		return false;
	}
	
	
	function getWochentag($kuerzel){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_WOCHENTAGE, "*", DB_F_WOCHENTAGE_PK_KUERZEL." = \"".$kuerzel."\""));
		
		return new Wochentag($row->{DB_F_WOCHENTAGE_PK_KUERZEL},$row->{DB_F_WOCHENTAGE_BEZEICHNUNG});
	}
	
	function getInteresse($id){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_INTERESSEN, "*", DB_F_INTERESSEN_PK_ID." = \"".$id."\""));
		
		return new Interesse($row->{DB_F_INTERESSEN_PK_ID}, $row->{DB_F_INTERESSEN_BEZEICHNUNG});
	}
	
	function getProdukt($id){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_PRODUKTE, "*", DB_F_PRODUKTE_PK_ID." = \"".$id."\""));
		
		return new Produkt($row->{DB_F_PRODUKTE_PK_ID}, $row->{DB_F_PRODUKTE_NAME}, $row->{DB_F_PRODUKTE_HERSTELLER}, $row->{DB_F_PRODUKTE_BESCHREIBUNG}, $row->{DB_F_PRODUKTE_PREIS}, $row->{DB_F_PRODUKTE_BESTAND});
	}
	
	function getHaartyp($kuerzel){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_HAARTYPEN, "*", DB_F_HAARTYPEN_PK_KUERZEL." = \"".$kuerzel."\""));
		
		return new Haartyp($row->{DB_F_HAARTYPEN_PK_KUERZEL}, $row->{DB_F_HAARTYPEN_BEZEICHNUNG});
	}
	
	function getArbeitsplatzausstattung($id){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_ARBEITSPLATZAUSSTATTUNGEN, "*", DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID." = \"".$id."\""));
		
		return new Arbeitsplatzausstattung($row->{DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID}, $row->{DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME});
	}

	function getSkill($id){
		$row = mysqli_fetch_row($this->selectQuery(DB_TB_SKILLS, "*", DB_F_SKILLS_PK_ID." = \"".$id."\""));
		
		return new Skill($row->{DB_F_SKILLS_PK_ID}, $row->{DB_F_SKILLS_BESCHREIBUNG});
	}
	
	
	function getArbeitsplatz($nummer){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_ARBEITSPLATZRESSOURCEN, "*", DB_F_ARBEITSPLATZRESSOURCEN_PK_NUMMER." = \"".$nummer."\""));
		
		$abf = $this->selectQuery(DB_VIEW_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN, "*", DB_F_ARBEITSPLATZRESSOURCEN_ARBEITSPLATZAUSSTATTUNGEN_PK_ARBEITSPLATZRESSOURCEN." = \"".$nummer."\"");
		$ausstattungen = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($ausstattungen, new Arbeitsplatzausstattung($row->{DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID}, $ausstattung->{DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME}));
		}
		
		return new Arbeitsplatz($nummer, $main->{DB_F_ARBEITSPLATZRESSOURCEN_NAME}, $ausstattung);
	}
	
	function getDienstleistung($kuerzel,Haartyp $haartyp){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_DIENSTLEISTUNGEN, "*", DB_F_DIENSTLEISTUNGEN_PK_KUERZEL." = \"".$kuerzel."\" AND ".DB_F_DIENSTLEISTUNGEN_PK_HAARTYP." = \"".$haartyp->getKuerzel()."\""));
		
		$abf = $this->selectQuery(DB_VIEW_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN, "*", DB_F_DIENSTLEISTUNGEN_ARBEITSPLATZAUSSTATTUNGEN_PK_DIENSTLEISTUNGEN." = \"".$kuerzel."\"");
		$ausstattungen = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($ausstattungen, new Arbeitsplatzausstattung($row->{DB_F_ARBEITSPLATZAUSSTATTUNGEN_PK_ID}, $ausstattung->{DB_F_ARBEITSPLATZAUSSTATTUNGEN_NAME}));
		}
		
		$abf = $this->selectQuery(DB_VIEW_DIENSTLEISTUNGEN_SKILLS, "*", DB_F_DIENSTLEISTUNGEN_SKILLS_PK_DIENSTLEISTUNGEN." = \"".$kuerzel."\"");
		$skills = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($skills, new Skill($row->{DB_F_SKILLS_PK_ID}, $row->{DB_F_SKILLS_BESCHREIBUNG}));
		}
		
		return new Dienstleistung($kuerzel, $haartyp, $main->{DB_F_DIENSTLEISTUNGEN_NAME}, $main->{DB_F_DIENSTLEISTUNGEN_BENOETIGTEEINHEITEN}, $main->{DB_F_DIENSTLEISTUNGEN_PAUSENEINHEITEN}, $skills, $ausstattungen, $main->{DB_F_DIENSTLEISTUNGEN_GRUPPIERUNG});
	}
	
	function getDienstzeit(Mitarbeiter $mitarbeiter, Wochentag $wochentag){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_DIENSTZEITEN, "*", DB_F_DIENSTZEITEN_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\" AND ".DB_F_DIENSTZEITEN_PK_WOCHENTAGE." = \"".$wochentag->getKuerzel()."\""));
		return new Dienstzeit($wochentag, new DateTime($main->{DB_F_DIENSTZEITEN_BEGINN}), new DateTime($main->{DB_F_DIENSTZEITEN_ENDE}));
	}
	
	function getKunde($email){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_KUNDEN, "*", DB_F_KUNDEN_PK_EMAIL." = \"".$email."\""));
		
		$abf = $this->selectQuery(DB_VIEW_KUNDEN_INTERESSEN, "*", DB_F_KUNDEN_INTERESSEN_PK_KUNDEN." = \"".$email."\"");
		$interessen = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($interessen, new Interesse($row->{DB_F_INTERESSEN_PK_ID}, $row->{DB_F_INTERESSEN_BEZEICHNUNG}));
		}
		
		return new Kunde($email, $main->{DB_F_KUNDEN_VORNAME}, $main->{DB_F_KUNDEN_NACHNAME}, $main->{DB_F_KUNDEN_TELNR}, $main->{DB_F_KUNDEN_FREISCHALTUNG}, $main->{DB_F_KUNDEN_FOTO}, $interessen);
	}
	
	function getMitarbeiter($svnr){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_MITARBEITER, "*", DB_F_MITARBEITER_PK_SVNR." = \"".$svnr."\""));
		
		$abf = $this->selectQuery(DB_VIEW_MITARBEITER_SKILLS, "*", DB_F_MITARBEITER_SKILLS_PK_MITARBEITER." = \"".$svnr."\"");
		$skills = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($skills, new Skill($row->{DB_F_SKILLS_PK_ID}, $row->{DB_F_SKILLS_BESCHREIBUNG}));
		}
		
		$abf = $this->selectQuery(DB_TB_URLAUBE, "*", DB_F_URLAUBE_PK_MITARBEITER." = \"".$svnr."\"");
		$urlaube = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($urlaube, new Urlaub($row->{DB_F_URLAUBE_PK_BEGINN}, $row->{DB_F_URLAUBE_ENDE}));
		}
		
		$abf = $this->selectQuery(DB_TB_DIENSTZEITEN, "*", DB_F_DIENSTZEITEN_PK_MITARBEITER." = \"".$svnr."\"");
		$dienstzeiten = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($dienstzeiten, new Dienstzeit($this->getWochentag($row->{DB_F_DIENSTZEITEN_PK_WOCHENTAGE}),new DateTime($row->{DB_F_DIENSTZEITEN_BEGINN}) , new DateTime($row->{DB_F_DIENSTZEITEN_ENDE})));
		}
		
		return new Mitarbeiter($svnr, $main->{DB_F_MITARBEITER_VORNAME}, $main->{DB_F_MITARBEITER_NACHNAME}, $skills, $main->{DB_F_MITARBEITER_ADMIN}, $urlaube, $dienstzeiten);
	}
	
	function getWerbung($nummer){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_WERBUNG, "*", DB_F_WERBUNG_PK_NUMMER." = \"".$nummer."\""));
		
		$abf = $this->selectQuery(DB_VIEW_WERBUNG_INTERESSEN, "*", DB_F_WERBUNG_INTERESSEN_PK_WERBUNG." = \"".$nummer."\"");
		$interessen = array();
		while ($row = mysqli_fetch_row($abf)){
			array_push($interessen, new Interesse($row->{DB_F_INTERESSEN_PK_ID}, $row->{DB_F_INTERESSEN_BEZEICHNUNG}));
		}
		
		return new Werbung($nummer, $interessen);
	}
	
	function getTermin(DateTime $zeitstempel, Mitarbeiter $mitarbeiter){
		$main = mysqli_fetch_row($this->selectQuery(DB_TB_ZEITTABELLE, "*", DB_F_ZEITTABELLE_PK_ZEITSTEMPEL." = \"".$zeitstempel->format(DB_FORMAT_DATETIME)."\" AND ".DB_F_ZEITTABELLE_PK_MITARBEITER." = \"".$mitarbeiter->getSvnr()."\"")); 
		
		return new Termin($zeitstempel, $mitarbeiter, $this->getArbeitsplatz($main->{DB_F_ZEITTABELLE_ARBEITSPLATZ}), $this->getKunde($main->{DB_F_ZEITTABELLE_KUNDE}), $main->{DB_F_ZEITTABELLE_FRISURWUNSCH}, $this->getDienstleistung($main->{DB_F_ZEITTABELLE_DIENSTLEISTUNG}, $this->getHaartyp($main->{DB_F_ZEITTABELLE_DIENSTLEISTUNG_HAARTYP})));
	}
	
	
	
	function call($name, $params){
		return $this->query("CALL ".$name."(".$params.");");
	}
	
	function selectQuery($name, $fields, $where_clause){
		return $this->query("SELECT * FROM ".$name." WHERE ".$where_clause.";");
	}
	
	function selectQueryField($name, $fields){
		return $this->query("SELECT ".$fields." FROM ".$name);
	}
	
	function selectQueryTable($name){
		return $this->selectQuery($name, "*");
	}
	
	function query($query_string){
		if(isset($this->con))
			return mysqli_query($this->con, $query_string);
		else
			throw new Exception("Keine Verbindung zur Datenbank!");
	}
	
	function __destruct(){
		if($this->con instanceof mysqli)
			mysqli_close($this->con);
	}
}
?>