

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
    <link rel="Stylesheet" type="text/css" href="styleee.css" />    
    <script src="script.js"></script>
    <link rel="stylesheet" type="text/css" href="css/fontello.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body background = "Grafika/tlo.jpg">

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

            echo $topgold;
            echo ' gracz: ';
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
        <div class="col-md-2">Notatki</div>
    </div>        
</div>

        <div id="zasoby"> 
            <div class="zasob" style="color:gold;">  <img src="Grafika/icon/gold.png"><?php echo "Złoto:".$_SESSION["zloto"]?></div> 
            <div class="zasob" style="color:darkgoldenrod;"> <img src="Grafika/icon/wood.png"><?php echo "Drewno:".$_SESSION["drewno"]?> </div>
            <div class="zasob" style="color:coral;"><img src="Grafika/icon/meat.png"><?php echo "Żywność:".$_SESSION["zywnosc"]?></div>
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
     