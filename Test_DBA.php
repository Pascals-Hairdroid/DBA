<?php

include_once("DB_Con.php");
$kunde = new Kunde("my@mail.at", null, null, null, null, null, array());
var_dump($kunde);

/*
Serialize Test:
include_once("DB_Con.php");
$kunde = new Kunde("my@mail.at","oli","king","01234678910",true,"meinpfad/meinfoto0815.jpg",array(new Interesse(13, "Mein Interesse"),new Interesse(17, "Mein anderes Interesse")));
var_dump($kunde);
echo "\n<br><br>\n";
$json = serialize($kunde);
echo $json;
echo "\n<br><br>\n";
$kunde_neu = unserialize($json);
var_dump($kunde_neu);
echo "\n<br><br>\n";

echo "\n<br><br>\n";

$a=false;
var_dump ($a);
echo "\n<br><br>\n";;
$aa = serialize($a);
echo $aa;
echo "\n<br><br>\n";
$aaa = unserialize($aa);
var_dump($aaa);
echo "\n<br><br>\n";
exit();
*/
/*
$ch = curl_init();
if($un=="oli" && $pw=="test"){
curl_setopt($ch, CURLOPT_URL,"localhost/PHD/DBA/login.php");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt ($ch, CURLOPT_COOKIEFILE, null);
curl_setopt($ch, CURLOPT_POST, 2);
curl_setopt($ch, CURLOPT_POSTFIELDS, "username=".urlencode($un)."&"."passwort=".md5($pw));
if (curl_errno($ch)){
	echo curl_errno($ch);
	echo curl_error($ch);
}
else{
	$response =  curl_exec($ch);
}
}
$test = "
<args>
	<Kunde>
			<email>my@mail.at</email>
			<vorname>oli</vorname>
			<nachname>king</nachname>
			<telNr>012345678910</telNr>
			<freischaltung>true</freischaltung>
			<foto>meinpfad/foto0815.jpg</foto>
			<Interessen>
				<Interesse>
					<id>5</id>
					<bezeichnung>Mien Interesse</bezeichnung>
				</Interesse>
				<Interesse>
					<id>7</id>
					<bezeichnung>Mien anderes Interesse</bezeichnung>
				</Interesse>
			</Interessen>
	</Kunde>
</args>
";

curl_setopt($ch, CURLOPT_URL,"localhost/PHD/DBA/DBA.php?f=kundeEintragen");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "xmlRequest=".$test);
if (curl_errno($ch)){
	echo curl_errno($ch);
	echo curl_error($ch);
}
else{
	echo "OK";
	$response =  curl_exec($ch);
	print_r($response);
	curl_close($ch);
}
*/
?>