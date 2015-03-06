<?php
	include_once 'DB_Con.php';
	
	function o(){
		echo "\n<br>\n";
	}
	
	function l(){
		echo "\n<br>\n<br>\n";
	}
	echo "<h1>Teste Klassen:</h1>";
	$ausstattung1 = new Arbeitsplatzausstattung(999, "OlisArbeitsplatzausstattung");
	var_dump($ausstattung1);
	l();
	
	$ausstattungen1 = array($ausstattung1);
	var_dump($ausstattungen1);
	l();
	
	$arbeitsplatz1 = new Arbeitsplatz(999, "OlisPlatz", $ausstattungen1);
	var_dump($arbeitsplatz1);
	l();
	
	$haartyp1 = new Haartyp("OHT", "OlisHaarTyp");
	var_dump($haartyp1);
	l();
	
	$skill1 = new Skill(999, "OlisSkill");
	var_dump($skill1);
	l();
	
	$skills1 = array($skill1);
	var_dump($skills1);
	l();
	
	$dienstleistung1 = new Dienstleistung("OK", $haartyp1, "OlisArbeit", 4, 1, $skills1, $ausstattungen1, 999);
	var_dump($dienstleistung1);
	l();
	
	$wochentag1 = new Wochentag("OT", "OlisTag");
	var_dump($wochentag1);
	l();
	
	$beginn1 = new DateTime("10:00");
	var_dump($beginn1);
	o();
	$ende1 = new DateTime("16:00");
	var_dump($ende1);
	o();
	$dienstzeit1 = new Dienstzeit($wochentag1, $beginn1, $ende1);
	var_dump($dienstzeit1);
	l();
	
	$dienstzeiten1 = array($dienstzeit1);
	var_dump($dienstzeiten1);
	l();
	
	$interesse1 = new Interesse(999, "OlisInteresse");
	var_dump($interesse1);
	l();
	
	$interessen1 = array($interesse1);
	var_dump($interessen1);
	l();
	
	$kunde1 = new Kunde("olis@mail.at", "Olis", "Kunde", "012345678901", false, "127.0.0.1/PHD_TEST/fotos/oli1234.jpg", $interessen1);
	var_dump($kunde1);
	l();
	
	$beginn2 = new DateTime("2015-06-09");
	var_dump($beginn2);
	o();
	$ende2 = new DateTime("2015-08-09");
	var_dump($ende2);
	o();
	$urlaub1 = new Urlaub($beginn2, $ende2);
	var_dump($urlaub1);
	l();
	
	$urlaube1 = array($urlaub1);
	var_dump($urlaube1);
	l();
	
	$mitarbeiter1 = new Mitarbeiter(1234230895, "Olis", "Mitarbeiter", $skills1, false, $urlaube1, $dienstzeiten1);
	var_dump($mitarbeiter1);
	l();
	
	$produkt1 = new Produkt(999, "OlisProdukt", "OlisHersteller", "OlisBeschriebung", 99.99, 999);
	var_dump($produkt1);
	l();
	
	$werbung1 = new Werbung(999, $interessen1);
	var_dump($werbung1);
	l();
	
	$zeitstempel1 = new DateTime("2015-02-14 08:15");
	var_dump($zeitstempel1);
	o();
	$termin1 = new Termin($zeitstempel1, $mitarbeiter1, $arbeitsplatz1, $kunde1, "127.0.0.1/PHD_TEST/fotos/olisWunsch123.jpg", $dienstleistung1);
	var_dump($termin1);
	l();
?>