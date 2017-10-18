<?php
session_start();
require_once "connect.php";
$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
$id=$_POST['id'];

if ($polaczenie->connect_errno!=0) {
    throw new Exception(mysqli_connect_errno());
}
else {
    $rezulta=$polaczenie->query("select * from ruchy_wojsk where id='$id'");
         while($rezultat = $rezulta->fetch_assoc()){ 
            $z=$rezultat['z'];
            $do=$rezultat['do'];   
            $misja=$rezultat['misja'];   
            $czas_dotarcia=$rezultat['czas_dotarcia'];   
            $czas_wymarszu=$rezultat['czas_wymarszu'];      
            $zloto=$rezultat['zloto'];  
            $drewno=$rezultat['drewno'];   
            $faza=$rezultat['faza'];  
            $jednostka0=$rezultat['jednostka_0'];   
            $jednostka1=$rezultat['jednostka_1'];   
            $jednostka2=$rezultat['jednostka_2'];   
            $jednostka3=$rezultat['jednostka_3'];   
            $jednostka4=$rezultat['jednostka_4'];   
            $jednostka5=$rezultat['jednostka_5'];
            $wspolrzedna_z[0]=floor($do/6)+1;
            $wspolrzedna_z[1]=$do%6;
            $wspolrzedna_do[0]=floor($z/6)+1;
            $wspolrzedna_do[1]=$z%6;
            $czas=1200*sqrt((pow($wspolrzedna_z[0]-$wspolrzedna_do[0],2)+pow($wspolrzedna_z[1]-$wspolrzedna_do[1],2)));
            $czas_powrotu=date('o-m-d G:i:s',(time($czas_wymarszu)+$czas));
            if($faza==1){ 
                if($misja=="transportuj"){
                    $rezulta2=$polaczenie->query("select * from uzytkownicy where wspolrzedna='$do'");
                    while($rezultat2 = $rezulta2->fetch_assoc()){
                        $zloto_nowe=$rezultat2['zloto']+$zloto;
                        $drewno_nowe=$rezultat2['drewno']+$drewno;
                        $rezultat3=$polaczenie->query("update uzytkownicy set drewno='$drewno_nowe', zloto='$zloto_nowe' where wspolrzedna='$do'");
                    }
                    $rezultat3=$polaczenie->query("insert into ruchy_wojsk values (NULL,'$do','$z','$misja','0','0',0,'$czas_powrotu', '$czas_powrotu','$jednostka0','$jednostka1','$jednostka2','$jednostka3','$jednostka4','$jednostka5')"); 
                    $polaczenie->query("delete from ruchy_wojsk where id='$id'");
                }
                else if($misja=="stacjonuj"){
                    $polaczenie->query("update ruchy_wojsk set misja='stacjonowanie', czas_dotarcia='$czas_wymarszu' where id='$id'");
                }
                else if($misja=="stacjonowanie"){
                    $polaczenie->query("insert into ruchy_wojsk values (NULL,'$do','$z','$misja','0','0',0,'$czas_powrotu', '$czas_powrotu','$jednostka0','$jednostka1','$jednostka2','$jednostka3','$jednostka4','$jednostka5')");
                    $polaczenie->query("delete from ruchy_wojsk where id='$id'");
                }
                else{
                    $jednostki_atak[0]=$jednostka0;
                    $jednostki_atak[1]=$jednostka1;
                    $jednostki_atak[2]=$jednostka2;
                    $jednostki_atak[3]=$jednostka3;
                    $jednostki_atak[4]=$jednostka4;
                    $jednostki_atak[5]=$jednostka5;
                    $rasa2="";
                    $nick2="";
                    $rezulta7=$polaczenie->query("select * from uzytkownicy where wspolrzedna='$do'");
                    while($rezultat8 = $rezulta7->fetch_assoc()){
                        $rasa2=$rezultat8['rasa'];
                        $nick2=$rezultat8['user'];
                    }
                    $rezulta7=$polaczenie->query("select * from $rasa2 where nick='$nick2'");
                    $jednostki_obrona[0]=0;
                    while($rezultat8 = $rezulta7->fetch_assoc()){
                        if($rasa2=="ludzie"){
                        $jednostki_obrona[0]=$rezultat8['miecznicy'];   
                        $jednostki_obrona[1]=$rezultat8['strzelcy'];   
                        $jednostki_obrona[2]=$rezultat8['rycerze'];
                        $jednostki_obrona[3]=$rezultat8['kanonierzy'];   
                        $jednostki_obrona[4]=$rezultat8['czarodziejki'];   
                        $jednostki_obrona[5]=$rezultat8['zyrokoptery'];
                        }
                        else{
                        $jednostki_obrona[0]=$rezultat8['siepacze'];   
                        $jednostki_obrona[1]=$rezultat8['lowcy_glow'];   
                        $jednostki_obrona[2]=$rezultat8['taureni'];
                        $jednostki_obrona[3]=$rezultat8['kodo'];   
                        $jednostki_obrona[4]=$rezultat8['szamani'];   
                        $jednostki_obrona[5]=$rezultat8['jezdzcy'];
                        }
                    }
                    $rezulta7=$polaczenie->query("select * from ruchy_wojsk where do='$do' and misja='stacjonowanie'");
                    $x=2;
                    while($rezultat8 = $rezulta7->fetch_assoc()){
                        $jednostki_obrona[0]=+$rezultat['jednostka_0'];   
                        $jednostki_obrona[1]=+$rezultat['jednostka_1'];   
                        $jednostki_obrona[2]=+$rezultat['jednostka_2'];   
                        $jednostki_obrona[3]=+$rezultat['jednostka_3'];   
                        $jednostki_obrona[4]=+$rezultat['jednostka_4'];   
                        $jednostki_obrona[5]=+$rezultat['jednostka_5'];
                        $x++;
                    }
                    $atakujacy=0;
                    $obrona=0;
                    
                    for($z=0; $z<6; $z++){
                        $atakujacy=+$jednostki_atak[$z];
                        $obrona=+$jednostki_obrona[$z];   
                    }
                  walka($jednostki_atak, $jednostki_obrona);
                $polaczenie->query("delete from ruchy_wojsk where id='$id'");
                }
            }
            else{
                $rasa="";
                $nick="";
                $rezulta2=$polaczenie->query("select * from uzytkownicy where wspolrzedna='$do'");
                while($rezultat2 = $rezulta2->fetch_assoc()){
                    $zloto_nowe=$rezultat2['zloto']+$zloto;
                    $drewno_nowe=$rezultat2['drewno']+$drewno;
                    $rasa=$rezultat2['rasa'];
                    $nick=$rezultat2['user'];
                    $rezultat3=$polaczenie->query("update uzytkownicy set drewno='$drewno_nowe', zloto='$zloto_nowe' where wspolrzedna='$do'");
                }
                if($rasa=="Ludzie"){
                    $rezultat3=$polaczenie->query("select * from ludzie where nick='$nick'");
                    while($rezultat31=$rezultat3->fetch_assoc()){
                        $miecznicy=$rezultat31['miecznicy']+$jednostka1_0;
                        $strzelcy=$rezultat31['strzelcy']+$jednostka1_1;
                        $kanonierzy=$rezultat31['kanonierzy']+$jednostka1_2;
                        $zyrokoptery=$rezultat31['zyrokoptery']+$jednostka1_3;
                        $czarodziejki=$rezultat31['czarodziejki']+$jednostka1_4;
                        $rycerze=$rezultat31['rycerze']+$jednostka1_5;
                        $rezultat9 = $polaczenie->query("update ludzie set miecznicy='$miecznicy', strzelcy='$strzelcy', rycerze='$rycerze', kanonierzy='$kanonierzy', czarodziejki='$czarodziejki', zyrokoptery='$zyrokoptery' where nick='$nick'");   
                    }
                }
                else{  
                    $rezultat3=$polaczenie->query("select * from ludzie where nick='$nick'");
                    while($rezultat31=$rezultat3->fetch_assoc()){
                        $siepacze=$rezultat31['siepacze']+$jednostka1_0;
                        $lowcy_glow=$rezultat31['lowcy_glow']+$jednostka1_1;
                        $kodo=$rezultat31['kodo']+$jednostka1_2;
                        $szamani=$rezultat31['szamani']+$jednostka1_3;
                        $taureni=$rezultat31['taureni']+$jednostka1_4;
                        $jezdzcy=$rezultat31['jezdzcy']+$jednostka1_5;
                        $rezultat9 = $polaczenie->query("update orkowie set siepacze='$siepacze', lowcy_glow='$lowcy_glow', taureni='$taureni', kodo='$kodo', szamani='$szamani', jezdzcy='$jezdzcy' where nick='$nick'"); 
                    }
                }
                $polaczenie->query("delete from ruchy_wojsk where id='$id'");
            }
        }
}
$polaczenie->close();

function walka ($x, $y){
    $kolejnosc[0]=3;
    $kolejnosc[1]=1;
    $kolejnosc[2]=5;
    $kolejnosc[3]=4;
    $kolejnosc[4]=2;
    $kolejnosc[5]=0;
    
    $jednostki_atak_2=sprawdz_jednostki($x);
    $jednostki_obrona_2=sprawdz_jednostki($y);

   // while (sprawdz_armie($x) && sprawdz_armie($y)){
        for($i=0; $i<6; $i++){
            
            if(czy_jest($kolejnosc[$i],$jednostki_atak_2)){
                $atakowana=losuj_jednostke(jednostki_obrona_2);
                atak($jednostki_atak[kolejnosc[$i]],jednostki_obrona[atakowana]);
                atak(jednostki_obrona[atakowana],$jednostki_atak[kolejnosc[$i]]);
            }
            if(czy_jest($kolejnosc[$i],$jednostki_obrona_2)){
                $atakowana=losuj_jednostke(jednostki_atak_2);
                atak(jednostki_obrona[kolejnosc[$i]],$jednostki_atak[atakowana]);
                atak($jednostki_atak[atakowana],jednostki_obrona[kolejnosc[$i]]);
            }
        }
    
    
}
function sprawdz_jednostki($x){
    for ($i=0; $i<6 ; $i++){
        $jednostki[0]=false;
        $jednostki[1]=false;
        $jednostki[2]=false;
        $jednostki[3]=false;
        $jednostki[4]=false;
        $jednostki[5]=false;
    }
    $y=0;
    for($i=0; $i<6; $i++){
        if($x[$i]>0) $jednostki[$y]=true;
        else $jednostki[$y]=false;
    }
    return $jednostki;
}

function losuj_jednostke($x){
    $jednostki[0]=0;
    $y=0;
    for($i=0; $i<count($x); $i++){
        if($x[$i]==true){
            $jednostki[$y]=$x[$i];
            $y++;
        }
    }
    $los=rand($y);
    return $jednostki[$los];
}
function atak($x, $y){
    $parametry1=ustaw_parametry($x);
    $parametry2=ustaw_parametry($y);
    
}
function ustaw_parametry($x){
    $parametr[0]=0;
    switch($x){
        case 0: $parametr[0]=4;  $parametr[1]=5;  $parametr[2]=25;  break;
        case 1: $parametr[0]=4;  $parametr[1]=2;  $parametr[2]=15;  break;
        case 2: $parametr[0]=6;  $parametr[1]=7;  $parametr[2]=30;  break;
        case 3: $parametr[0]=6;  $parametr[1]=0;  $parametr[2]=15;  break;
        case 4: $parametr[0]=3;  $parametr[1]=2;  $parametr[2]=25;  break;
        case 5: $parametr[0]=6;  $parametr[1]=2;  $parametr[2]=20;  break;
    }
    return $parametr;
}
function czy_jest ($x, $y){
    if($y[$x]) return true;
    else return false;
}
function sprawdz_armie($x){
    for($i=0; $i<6; $i++){
        if($x[$i]>0) return true;
    }
    return false;
}

?>
