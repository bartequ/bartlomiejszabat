<?php 
require_once "connect.php";
	mysqli_report(MYSQLI_REPORT_STRICT);
	$user = $_SESSION['user'];

	if (mysql_connect($host, $db_user, $db_password) && mysql_select_db($db_name)) {
		$wynik = mysql_query("select * from uzytkownicy where user ='$user'")
		or die ("Błąd w zapytaniu");
		//mysql_close();
	}
	else echo "Nie mogę połączyć się z bazą.";
		
	
	while($ostLog = mysql_fetch_array($wynik)) { 
        echo "Ostatnie logowanie: ".$ostLog['ostatnieLogowanie']."<br />"; 
		$_SESSION['ostatnieLogowanie'] = $ostLog['ostatnieLogowanie'];
		$_SESSION['drewno'] = $ostLog['drewno'];
		$_SESSION['zloto'] = $ostLog['zloto'];
		$_SESSION['zywnosc'] = $ostLog['zywnosc'];
	}

	
	$dataczas = new DateTime();
	echo "Data i czas serwera: ".$dataczas->format('Y-m-d H:i:s');
	/*$ostatnie = DateTime::createFromFormat('Y-m-d H:i:s', $_SESSION['ostatnieLogowanie']);
	$roznica = $dataczas->diff($ostatnie);
	echo "<br/>Roznica: ".$roznica->format('%d dni, %h godz, %i min %s sek');*/
	
	$timeOstatni = strtotime($_SESSION['ostatnieLogowanie']);
	$timeTeraz = strtotime(date('Y-m-d H:i:s'));
	$roznicaSekundy = $timeTeraz - $timeOstatni;
	echo "<br>Roznica w sekundach: ".$roznicaSekundy;
	
	$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
	$sql = $polaczenie->query("update uzytkownicy set ostatnieLogowanie=now() where user = '$user'");
	//Teraz trzeba dodac jakies mnozniki, czas znajduje sie w $roznicaSekundy
	
	
	
	//Mnoznik do zmiany w zaleznosci od technologii i robotnikow
	$mnoznik = $roznicaSekundy;
	$_SESSION['mnoznik'] = $mnoznik;
	$sql = $polaczenie->query("update uzytkownicy set drewno=drewno+'$mnoznik', zloto=zloto+'$mnoznik', zywnosc=zywnosc+'$mnoznik' where user = '$user'");
	
	while($ostLog = mysql_fetch_array($wynik)) { 
		$_SESSION['drewno'] = $ostLog['drewno'];
		$_SESSION['zloto'] = $ostLog['zloto'];
		$_SESSION['zywnosc'] = $ostLog['zywnosc'];
	}
function oblicz($lvl,$budynek,$a){
    $pow=pow(1.5,$lvl);
    switch ($budynek){
        case 'castle': $zloto=500; $drewno=500; break;
        case 'barrack': $zloto=300; $drewno=350; break;
        case 'altar': $zloto=300; $drewno=150; break;
        case 'forge': $zloto=300; $drewno=300; break;
        case 'house': $zloto=250; $drewno=300; break;
    }
    $zloto=$zloto*$pow;
    $drewno=$drewno*$pow;
    if ($a){
        echo "Złoto: ".$zloto."<br> Drewno: ".$drewno;
    }
    $zasob['a']=$zloto;
    $zasob['b']=$drewno;
    return $zasob;    
}

function czas($lvl, $budynek){
    $pow=pow(1.5,$lvl);
    
    switch($budynek){
        case 'castle': $minuty=(int)($pow*60); break;
        case 'barrack': $minuty=(int)($pow*50);break;
        case 'altar': $minuty=(int)($pow*40);break;
        case 'forge': $minuty=(int)($pow*50);break;
        case 'house': $minuty=(int)($pow*40);break;
    }
    
    $godziny=$minuty/60;
    $godziny=floor($godziny);
    $minuty=$minuty%60;

    if ($godziny==0){ 
        echo $minuty."min.";
    }
    if ($minuty==0){ 
        echo $godziny."godź.";
    }
    else{ 
        echo $godziny."godź. <br>".$minuty."min.";
    }  
    
}

function wypisz($lvl,$budynek){
    $zasoby=oblicz($lvl,$budynek,false);  
    
    $zloto=$zasoby['a'];
    $drewno=$zasoby['b'];
    if($_SESSION['drewno']>=$drewno && $_SESSION['zloto']>=$zloto){
        echo  '<button type="button" class="btn btn-success">Ulepsz</button>';
    }
    else{
        echo  '<button type="button" class="btn btn-danger">Brak zasobów</button>';
    }
}





?>
