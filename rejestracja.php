<?php
	//session_start();
	if (isset($_POST['email'])){
		//Jesli udana walidacja
		$flaga = true;
		
		$rasa = $_POST['rasa'];
		//Poprawnosc nicku
		$nick = $_POST['nick'];
		if (strlen($nick)<3 || strlen($nick)>20) {
			$flaga = false;
			$_SESSION['e_nick'] = "Nick musi posiadać od 3 do 20 znaków.";
		}
		if ( !(ctype_alnum($nick)) ) {
			$flaga = false;
			$_SESSION['e_nick'] = "Nick nie może zawierać znaków specjalnych (w tym polskich ogonków).";
		}
		
		//Poprawnosc maila
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		if ( !(filter_var($email, FILTER_VALIDATE_EMAIL)) || $email != $emailB ) {
			$flaga = false;
			$_SESSION['e_email'] = "Wprowadź poprawny adres e-mail.";
		}
		
		//Poprawnosc hasla
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
		if (strlen($haslo1)<3 || strlen($haslo2)>20) {
			$flaga = false;
			$_SESSION['e_haslo'] = "Hasło powinno zawierać od 8 do 20 znaków.";
		}
		if ($haslo1 != $haslo2){
			$flaga = false;
			$_SESSION['e_haslo'] = "Podane hasła nie są identyczne.";
		}
		
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		//Sprawdzanie checkboxa
		if (!isset($_POST['regulamin'])) {
			$flaga = false;
			$_SESSION['e_regulamin'] = "Potwierdź akceptację regulaminu.";
		}
		
		
		
		//Laczenie z baza danych
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try {
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			$polaczenie1 = new mysqli($host, $db_user, $db_password, $db_name);
			$polaczenie2 = new mysqli($host, $db_user, $db_password, $db_name);
			$polaczenie3 = new mysqli($host, $db_user, $db_password, $db_name);
			$polaczenie4 = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0) {
				throw new Exception(mysqli_connect_errno());
			}
			else {
				//Czy email juz istnieje
				$rezultat = $polaczenie->query("select id from uzytkownicy where email='$email'");
				if (!$rezultat) throw new Exception($polaczenie->error);
				$ile_maili = $rezultat->num_rows;
				if ($ile_maili>0){
					$flaga = false;
					$_SESSION['e_email'] = "Istnieje już konto przypisane do podanego adresu e-mail";
				}
				
				//Czy nick juz istnieje
				$rezultat = $polaczenie->query("select id from uzytkownicy where user='$nick'");
				if (!$rezultat) throw new Exception($polaczenie->error);
				$ile_nickow = $rezultat->num_rows;
				if ($ile_nickow>0){
					$flaga = false;
					$_SESSION['e_nick'] = "Istnieje już gracz o podanym nicku.";
				}
				
				//Czy wspolrzedna istnieje <1;36>
				$rezultat = $polaczenie->query("select wspolrzedna from uzytkownicy");
				if (!$rezultat) throw new Exception($polaczenie->error);
				$ile_wspolrzednych = $rezultat->num_rows;

				
				
				if ($ile_wspolrzednych>0){
					while($r = $rezultat->fetch_assoc()) {
					//	echo $r['wspolrzedna']." ";
						$tab[] = $r['wspolrzedna'];
					}
				}
				for ($i=0 ; $i<36; $i++){
                    $tab2[$i]=$i+1;    
                }
                
                $tab3= array_diff($tab2,$tab);
                $tab3=array_values($tab3);
                $wspolrzedna=rand(0,count($tab3)-1);
                $wartosc=$tab3[$wspolrzedna];
                    
				//Jesli walidacja poprawna
				if ($flaga) {
					if ($polaczenie->query("insert into uzytkownicy values (NULL, '$nick', '$rasa', '$wartosc','$haslo_hash', '$email',1000, 1000, 0, now())") && $polaczenie1->query("insert into budynki values ('$nick',NULL, '$rasa', 0, 0, 1, 0, 1, 1, 1)") && $polaczenie3->query("insert into badania values ('$nick',NULL, 0, 0, 0, 0, 0, 0, 0)")&& $polaczenie3->query("insert into kolejki values (0, 0, 0, 0,0, 0,0,NULL,'$nick')") && $polaczenie4->query("insert into ranking values ('$nick',0,NULL)")){
						if ($rasa == "Orkowie" && $polaczenie2->query("insert into orkowie values('$nick',NULL,0,0,0,0,0,0,5,0,0)")) {
							$_SESSION['udanarejestracja'] = true;
                            header("Location: witamy.php");
						}
						else if ($rasa == "Ludzie" && $polaczenie2->query("insert into ludzie values('$nick',NULL,0,0,0,0,0,0,5,0,0)")) {
							$_SESSION['udanarejestracja'] = true;
                            header("Location: witamy.php");
						}
					}
					else {
						throw new Exception($polaczenie->error);
					}
				}
				$polaczenie->close();
			}
			
		} 
		catch (Exception $e) {
			echo '<span style="color:red">Błąd serwera, przepraszamy za niedogodności i prosimy o rejestrację w innym terminie.</span>';
			//echo '</br>Informacja developerska:'.$e;
		}
	}
?>

<?php
include ('menu.php');
?>
	<form method="post">
		Nick: </br><input type="text" name="nick"/> <br/>
		<?php
			if (isset($_SESSION['e_nick'])){
				echo '<div class="error">'.$_SESSION['e_nick'].'</div';
				unset($_SESSION['e_nick']);
			}
		?>
		E-mail: <br/><input type="text" name="email"/> <br/>
		<?php
			if (isset($_SESSION['e_email'])){
				echo '<div class="error">'.$_SESSION['e_email'].'</div';
				unset ($_SESSION['e_email']);
			}
		?>
		Twoje hasło: <br/><input type="password" name="haslo1"/> <br/>
		<?php
			if (isset($_SESSION['e_haslo'])) {
				echo '<div class="error">'.$_SESSION['e_haslo'].'</div';
				unset ($_SESSION['e_haslo']);
			}
		?>
		Powtórz hasło: <br/><input type="password" name="haslo2"/> </br>
		<?php
			if (isset($_SESSION['e_haslo'])) {
				echo '<div class="error">'.$_SESSION['e_haslo'].'</div';
				unset ($_SESSION['e_haslo']);
			}
		?>
		
		
		<label>
			<input type="checkbox" name="regulamin"/>Akceptuję regulamin
		</label></br>
		
		<?php
			if (isset($_SESSION['e_regulamin'])) {
				echo '<div class="error">'.$_SESSION['e_regulamin'].'</div';
				unset ($_SESSION['e_regulamin']);
			}
		?>
		Wybierz rasę: 
		<select type ="text" name="rasa" style="color:black">
			<option>Orkowie</option>
			<option>Ludzie</option>		
		</select>
		<br><br><br>
		<input type="submit" value="Zarejestruj się" style="color:black;" />
	</form>

<?php
	include ('stopka.php');
?>
