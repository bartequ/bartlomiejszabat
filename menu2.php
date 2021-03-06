

<!DOCTYPE html>

<?php 
    session_start();
    include('skrypt.php');
?>
<html lang="pl">
<head>
    <meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
  
    <script src="script.js"></script>
    <link rel="stylesheet" type="text/css" href="css/fontello.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="jquery.min.js"></script>
    <script type="text/javascript" src="jquery.maphilight.min.js"></script>
      <link rel="Stylesheet" type="text/css" href="styleee.css" />    
    <title>WC3 - Gra przeglądarkowa</title>
</head>
<body background = "Grafika/tlo.jpg">

<?php
obliczZasoby();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2"><div id="datka"></div></div>
        <div class="col-md-2"><div id=clock></div></div>
        <div class="col-md-2">Top złoto: 
            <?php

            $link = mysql_connect("localhost", "root", ""); 
            mysql_select_db("warcraft", $link);

            $result = mysql_query("SELECT user, zloto FROM uzytkownicy WHERE zloto = (SELECT MAX(zloto) FROM uzytkownicy)", $link);
            $row = mysql_fetch_array( $result );
            $topplayer = $row['user'];
            $topgold = $row['zloto'];

            echo $topgold." ";
            echo $topplayer;
            ?>
        </div>
        <div class="col-md-2">Aktywni</div>
        <div class="col-md-2">
            <button id="wiadomosciBtn">Wiadomosci</button>
            <!-- The Modal -->
                <div id="myModal" class="modal3">

                <!-- Modal content -->
                    <div class="modal3-content">
                        <span class="close">&times;</span>
                        <p> AKTUALNE WIADOMOŚCI:
                            <?php
                                $myfile = fopen("info.txt", "r") or die("Unable to open file!");
                                $pageText = fread($myfile,filesize("info.txt"));
                                echo nl2br($pageText);
                                fclose($myfile);
                            ?>
                        </p>
                    </div>
                </div>
            <script type="text/javascript" src="info.js"></script>      <!-- skrypt obslugujacy guziczek -->
        </div>
        <div class="col-md-2"><a href="logout.php">Wyloguj się!</a></div>
    </div>        
</div>

        <div id="zasoby"> 
            <div class="zasob" id="zasobzloto"
                 <?php if ($_SESSION['zloto'] < $_SESSION['magazynZlota_lvl']*5000) echo 'style="color:gold;"'; else echo 'style="color:red;"';?>>  
                <img src="Grafika/icon/gold.png"><?php echo "Złoto:".floor($_SESSION["zloto"])?>
            </div> 
            <div class="zasob" id="zasobdrewno"
                <?php if ($_SESSION['drewno'] < $_SESSION['magazynDrewna_lvl']*5000) echo 'style="color:darkgoldenrod;"'; else echo 'style="color:red;"';?>> 
                <img src="Grafika/icon/wood.png"><?php echo "Drewno:".floor($_SESSION["drewno"])?> 
            </div>
            <div class="zasob" 
                 <?php if ($_SESSION['zywnosc'] < $_SESSION['max_zywnosc']) echo 'style="color:coral;"'; else echo 'style="color:red;"';?>>
                <img src="Grafika/icon/meat.png"><?php echo "Żywność:".$_SESSION['zywnosc']."/".$_SESSION["max_zywnosc"]?>
            </div>
        </div>


    
    <div style="clear:both"></div>
        <div id="nav">
                <a href="podglad.php"><div id="button">Podgląd</div></a>
                <a href="surowce.php"><div id="button">Surowce</div></a>
                <a href="budynki.php"><div id="button">Budynki</div></a>
                <a href="badania.php"><div id="button">Badania</div></a>
                <a href="koszary.php"><div id="button">Koszary</div></a>
                <a href="garnizon.php"><div id="button">Garnizon</div></a>
                <a href="obrona.php"><div id="button">Obrona</div></a>
                <a href="mapa.php"><div id="button">Mapa</div></a>
        </div>
        
     <div id="center">
     
	 <script>
            var zloto = <?php echo json_encode($_SESSION['robotnicy_zloto']); ?>;
            var drewno = <?php echo json_encode($_SESSION['robotnicy_drewno']); ?>;
            var tdrewna = <?php echo json_encode($_SESSION['t_drewna']); ?>;
            var tzlota = <?php echo json_encode($_SESSION['t_wydobycia']); ?>;
			
			var aktualnezloto = <?php echo json_encode($_SESSION['zloto']); ?>;
			var aktualnedrewno = <?php echo json_encode($_SESSION['drewno']); ?>;
			
			var magazynZlota_lvl = <?php echo json_encode($_SESSION['magazynZlota_lvl']); ?>;
			var magazynDrewna_lvl = <?php echo json_encode($_SESSION['magazynDrewna_lvl']); ?>;
				
			var wydobycie_d= (1+(tdrewna/10))*drewno*3;
			var wydobycie_z= (1+(tzlota/10))*zloto*3;
			
			var pojemnosc_zloto = magazynZlota_lvl*5000;
			var pojemnosc_drewno = magazynDrewna_lvl*5000;
			
			setInterval(function(){
				if(aktualnezloto < pojemnosc_zloto){
					aktualnezloto = aktualnezloto + (wydobycie_z/60);
				} 
				else{ 
					aktualnezloto = pojemnosc_zloto; 
				}
				
				if(aktualnedrewno < pojemnosc_drewno){
					aktualnedrewno = aktualnedrewno + (wydobycie_d/60);
				} 
				else{
					aktualnedrewno = pojemnosc_drewno; 
				}
			document.getElementById("zasobzloto").innerHTML = "<img src='Grafika/icon/gold.png'>Złoto:"+Math.floor(aktualnezloto);
			document.getElementById("zasobdrewno").innerHTML = "<img src='Grafika/icon/wood.png'>Drewno:"+Math.floor(aktualnedrewno);
			}, 1000);
	</script>
