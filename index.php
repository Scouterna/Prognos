<html>
<head>
<title>Prognos</title>
<link rel="stylesheet" href="scoutnet_prognos.css">

</head>
<body>
<?php

	$time = microtime();
	$time = explode(' ', $time);
	$time = $time[1] + $time[0];
	$start = $time;

	/*
		Begränsning till högst 3 år på en gren	
	*/
	
	/************Standardinställning för API:er***************/
	require_once('scoutnet_basic_config.php');
	
	/*************Här kan du göra dina egna inställningar***************/
	require_once('scoutnet_prognos_config.php');	
	
	
	
	/*
	 * Anropar funktion för att hämta funktion att exekvera på denna gren på denna plats
	 */
	function scoutnet_get_gren_calc($gren_index, $avdelning_index)	{
		
		if (0==$gren_index)	{
			return scoutnet_get_letare_calc($avdelning_index);
		}
		else if (1==$gren_index)	{
			return scoutnet_get_sparare_calc($avdelning_index);
		}
		else if (2==$gren_index)	{
			return scoutnet_get_upptackare_calc($avdelning_index);
		}
		else if (3==$gren_index)	{
			return scoutnet_get_aventyrare_calc($avdelning_index);
		}
		else if (4==$gren_index)	{
			return scoutnet_get_utmanare_calc($avdelning_index);
		}
		else	{
			return -1;
		}		
	}	
	
	function Scoutnet_do_some_calculations($input)	{	
		return $input;
	}	

	/*
	 *	theunit = the unit
	 *	string = how to do the calculations
	 */
	function Scoutnet_calculations($gren, $theunit, $theyear)	{
		
		global $avdelningar;
		global $scout_year;
		global $waitinglist;
		$string = "H+-ejsan+++";
		//echo "Min fina 1 " . $string;
		$avdelningsnamn = get_avdelningsnamn('english', 'age', 'array', 'empty');
		
		
		//echo "Min fina 2 " . $string;
		
		$tmp_grenar = scoutnet_get_grenar();
		$tmp_grenar = scoutnet_convert_array_to_english_letters($tmp_grenar);
		
		$antal_grenar = count($tmp_grenar);
		$gren_index = 0;
		$gren_index_fore = 0;
		$avdelning_index = 0;
		$gren_avdelningar;
		$avdelning_index = 0;
		$antal_avdelningar = 0;
		
		//Hitta grenindex för att veta var ska kolla
		for ($r = 0; $r < $antal_grenar; $r++)	{
			
			if ($gren==$tmp_grenar[$r])	{	//Denna gren, match
				
				$gren_avdelningar = get_avdelningar_gren($gren, 'english', 'no age', 'non empty');
				$antal_avdelningar = count($gren_avdelningar);
				
				$gren_index = $r;
				$gren_index_fore = $gren_index - 1;
				
				if ($gren_index_fore < 0)	{
					$gren_index_fore = 0;
				}
				//echo "Grenindex " . $gren_index;
			}			
		}
		//Hitta avdelningsindex för att veta var ska kolla
		for ($r = 0; $r < $antal_avdelningar; $r++)	{		
			if ($theunit==$gren_avdelningar[$r])	{				
				$avdelning_index = $r;
				//echo "Avdelningsindex " . $avdelning_index;			
			}		
		}
		
		//Vi ska nu på denna grenindex och avdelningsindex hitta en formel och exekvera på grenindex_fore
		
		$string_calculation = scoutnet_get_gren_calc($gren_index, $avdelning_index);	//Hitta rätt funktion
		//echo "String calculation " . $string_calculation;
		
		//$string_calculation = "hU+peArt/ex(t+langu*age-prog/ra1mm2ing";	//Test
		//$string_calculation = "(Bävrarna)/2";	//Test
		
		$last_char = substr($string_calculation, -1);		
		if ('-'==$last_char || '+'==$last_char)	{	//Ta reda på om standard, ceil eller floor
			$string_calculation = rtrim($string_calculation, "+-");
		}
		
		$string_calculation = scoutnet_convert_string_to_english_letters($string_calculation);
		
		//$string_calculation = strtolower($string_calculation);	//make the string lowercase
		/*
		Konvertera om formeln med A, B, mm till avdelningsnamn
		*/		
		//Dela upp formel i en array
		$array_calculation = preg_split("/ *([\W]) */", $string_calculation, -1, PREG_SPLIT_DELIM_CAPTURE);	//Matchar alla ej boktav eller siffra
		
		$ant_array_calculation = count($array_calculation);
		
		$array_total = array();
		$array_sex0 = array();
		$array_sex1 = array();
		$array_sex2 = array();
		$array_sex3 = array();
		
		for ($r = 0; $r < $ant_array_calculation; $r++)	{
			
			$tmp_element = $array_calculation[$r];	//this specific element
			
			if (array_key_exists($tmp_element, $avdelningsnamn))	{	//Kolla om detta element är en avdelning
				
				$array_total[$r] = $avdelningar[$tmp_element][$theyear]['total'];
				$array_sex0[$r] = $avdelningar[$tmp_element][$theyear]['sex'][0];
				$array_sex1[$r] = $avdelningar[$tmp_element][$theyear]['sex'][1];
				$array_sex2[$r] = $avdelningar[$tmp_element][$theyear]['sex'][2];
				$array_sex3[$r] = $avdelningar[$tmp_element][$theyear]['sex'][3];
				
				//echo "Hittade " . $tmp_element;
			}
			else if	('w'==$tmp_element)	{	//För väntelista
				
				$avdelningar[$tmp_element][$theyear] = $waitinglist[$theyear];	//global
				
				$array_total[$r] = $avdelningar[$tmp_element][$theyear]['total'];
				$array_sex0[$r] = $avdelningar[$tmp_element][$theyear]['sex'][0];
				$array_sex1[$r] = $avdelningar[$tmp_element][$theyear]['sex'][1];
				$array_sex2[$r] = $avdelningar[$tmp_element][$theyear]['sex'][2];
				$array_sex3[$r] = $avdelningar[$tmp_element][$theyear]['sex'][3];
				
				$temparray = $waitinglist[$theyear];
					
				$scout_year[$theyear] = $waitinglist[$theyear];		//global
				
				//echo "Waitinglist";
			}
			else	{	//Om räkneoperator, alltså tex +-*/()
				$array_total[$r] = $tmp_element;
				$array_sex0[$r] = $tmp_element;
				$array_sex1[$r] = $tmp_element;
				$array_sex2[$r] = $tmp_element;
				$array_sex3[$r] = $tmp_element;
				
				//echo "Ej " . $tmp_element;
			}			
		}
		
		//Vi har nu arrayer med uträknining för total, och respektive kön
		
		//Konvertera arrayerna till textsträngar
		$total = implode('', $array_total);
		$sex0 = implode('', $array_sex0);
		$sex1 = implode('', $array_sex1);
		$sex2 = implode('', $array_sex2);
		$sex3 = implode('', $array_sex3);
		
		//echo "<pre>";
		//print_r($sex1);
		//echo "</pre>";
				
		
		$math = new Math();
		//$total = '(2 + 3) * 4';
		$answer_total = $math->evaluate($total);	//Calculate the ansver
		$answer_sex0 = $math->evaluate($sex0);
		$answer_sex1 = $math->evaluate($sex1);
		$answer_sex2 = $math->evaluate($sex2);
		$answer_sex3 = $math->evaluate($sex3);
		
		//echo "<pre>";
		//echo $answer_total;
		//print_r($answer_total);
		//echo "</pre>";
		
		
		if ('+'==$last_char)	{	//Vi avrundar upp alla siffror
			$temparray['total'] = ceil($answer_total);
			$temparray['sex'][0] = ceil($answer_sex0);
			$temparray['sex'][1] = ceil($answer_sex1);
			$temparray['sex'][2] = ceil($answer_sex2);
			$temparray['sex'][3] = ceil($answer_sex3);
		}
		else if ('-'==$last_char)	{	//Vi avrundar upp alla siffror		
			$temparray['total'] = floor($answer_total);
			$temparray['sex'][0] = floor($answer_sex0);
			$temparray['sex'][1] = floor($answer_sex1);
			$temparray['sex'][2] = floor($answer_sex2);
			$temparray['sex'][3] = floor($answer_sex3);		
		}
		else	{	//Om ej specificerat om avrundning upp eller ner			
			$temparray['total'] = $answer_total;
			$temparray['sex'][0] = $answer_sex0;
			$temparray['sex'][1] = $answer_sex1;
			$temparray['sex'][2] = $answer_sex2;
			$temparray['sex'][3] = $answer_sex3;				
		}		
		
		//echo "<pre>";
		//print_r($tmp_avdelningar);
		//echo "</pre>";
		
		return $temparray;
	}
	 
	/*
	 * Räkna ut vilka som ska börja på avdelningen nästa år
	 */
	function CleanArray($gren, $thearray,$theyear,$theunit="") {
		global $avdelningar;
		global $waitinglist;
		global $scout_year;
		global $sum_unit;
		global $date;
		global $termin;
		
		if (!empty($theunit))	{	//Om avdelning, alltså ej summan till höger
			if (!empty($thearray[$theunit]))	{
				$thearray = $thearray[$theunit];
			}
		}
		//echo "ABCD " . $theyear . " ";
		
		if (empty($thearray[$theyear])) {					//Om åk 1
			
			if (!empty($theunit)) {	//Om avdelningen finns
				
				$temparray = Scoutnet_calculations($gren, $theunit, $theyear);
				
				$avdelningar[$theunit][$theyear] = $temparray;	//global
				$thearray[$theyear] = $temparray;	//Array för olika ålder
			}
			else	{	//Om åk1, men avdelningen finns ej
				echo "FEL";				
			}
		}
		
		if (!empty($theunit)) {	//Om avdelningen finns. Alltså för alla avdelningar
			
		
			if (!empty($sum_unit[$theunit][$theyear]))	{	//global, om kopiera innehåll för kommande år inom avdelning
				$sum_unit[$theunit][$theyear] = SumArray($thearray[$theyear],$sum_unit[$theunit][$theyear]);
				//echo "ainnehåll " . $thearray[$theyear] . " ";
			}
			else	{
				//echo "binnehåll " . $thearray[$theyear] . " ";
				$sum_unit[$theunit][$theyear] = $thearray[$theyear];
			}
		}
		$thearray = $thearray[$theyear];
		makeitbeautiful($thearray,1);
	}


	function getsum($thearray, $gren, $theyear="",$theunit="",$themode="") {
		global $avdelningar;
		global $waitinglist;
		global $scout_year;
		global $sum_unit;
		
		//$gren = 'letare';
		//		getsum($avdelningar, $gren, $thisyear-$gren_max_age);
		//
		$antal_year = 0;
		$avd_param = "ja";
			
		$antal_year = get_antal_year_gren($gren);
		
		
		if ($theunit=="")	{	//Om på grennivå
			//echo $antal_year;			
			
			if (1==$antal_year)	{	
				$thearray = $scout_year[$theyear];
			}
			else if (2==$antal_year)	{				
				$thearray = SumArray($scout_year[$theyear],$scout_year[$theyear+1]);				
			}
			else if (3==$antal_year)	{				
				$thearray = SumArray($scout_year[$theyear],$scout_year[$theyear+1]);
				$thearray = SumArray($scout_year[$theyear+2],$thearray);
			}		
		}		
		else	{	//Om på avdelningsnivå
			//echo $antal_year;			
			
			if (1==$antal_year)	{	
				$thearray = $thearray[$theunit][$theyear];
			}
			else if (2==$antal_year)	{		
				$thearray = SumArray($thearray[$theunit][$theyear],$thearray[$theunit][$theyear+1]);
			}
			else if (3==$antal_year)	{
				$thearray = SumArray($thearray[$theunit][$theyear],$scout_year[$theyear+1]);
				$thearray = SumArray($scout_year[$theyear+2],$thearray);		
			}				
		}		
		
		makeitbeautiful($thearray,$themode);		
	}


	/*
	 * FUNKTION FÖR INLÄSNING AV VÄNTELISTAN
	 * unit är uppdelad i födelseår och hur många i väntelista samt uppdelat i kille/tjej/annat
	 * Denna är kontrollerad och fungerar
	 */
	function scoutnet_newscouts() {
		global $tmp_year;
		
		$decoded = scoutnet_get_member_list("?waiting=1");
		$members = $decoded['data'];
		$unit = array();
		
		$max_year = $tmp_year-18;

		$tmparray = array();

		foreach ($members as $key => $member) {
			$member_year = substr($member['date_of_birth']['value'],0,4);
			if ($member_year >= $max_year) {
				$member_sex = $member['sex']['raw_value'];

				if (!array_key_exists($member_year,$unit)) {
					$unit[$member_year] = ['total'=>0,'sex' =>[0=>0,1=>0,2=>0,3=>0]];
				}
				$unit[$member_year]['total']++;
				$unit[$member_year]['sex'][$member_sex]++;
			}
		}
		
		/////////Test
		/*foreach($unit as $x => $x_value) {	//Lista alla avdelningar och deras åldersintervall
			
			$antscouter = $x_value['total'];
			$antkillar = $x_value['sex'][1];
			$anttjejer = $x_value['sex'][2];
			$antannat = $x_value['sex'][0]+$x_value['sex'][3];
			echo "Unit=" . $x . ", Total=" . $antscouter . ", kille=" . $antkillar . ", tjej=" . $anttjejer . ", annat=" . $antannat;
			echo "<br>";
		}*/
		/////Test		
		return $unit;
	}

	function scouternaplugins_fixthatstring($string) {
		return strtolower(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml|caron);~i', '$1', htmlentities($string, ENT_COMPAT, 'UTF-8')));
	}
	
	/*
	 * Konverterar en sträng så att åäö blir aao tex och ändras till gemener.
	 */
	function scoutnet_convert_string_to_english_letters($string)	{
		$string = scouternaplugins_fixthatstring($string);
		return iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
	}
	
	/*
	 * Konverterar en array så att åäö blir aao tex.
	 */
	function scoutnet_convert_array_to_english_letters($args)	{
		
		$arrlength = count($args);

		for($x = 0; $x < $arrlength; $x++) {
			$string = $args[$x];
			$string = scouternaplugins_fixthatstring($string);
			$args[$x] = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $string);
			
		}
		return $args;
	}
	
	/*
	 * Funktion för inläsning av medlemslista
	 */
	function scoutnet_create_list() {
		global $tmp_year;
		
		$decoded = scoutnet_get_member_list();
		$members = $decoded['data'];
		$unit = array();

		$max_year = $tmp_year-18;

		$tmparray = array();

		foreach ($members as $key => $member) {
			$member_year = substr($member['date_of_birth']['value'],0,4);	//Födelseår
			
			if ($member_year >= $max_year) {	//Om yngre än 18 år
				$member_sex = $member['sex']['raw_value'];	//kön, id kod
				$member_unit = $member['unit']['value'];	//Avdelningens namn
				
				////Räkna upp unit och dess variabler för varje medlem
				if (!in_array($member_unit.$member_year,$tmparray)) {
					$unit[$member_unit][$member_year] = ['total'=>0,'sex' =>[0=>0,1=>0,2=>0,3=>0]];
					$tmparray[] = $member_unit.$member_year;
				}
				$unit[$member_unit][$member_year]['total']++;
				$unit[$member_unit][$member_year]['sex'][$member_sex]++;
			}
		}	
		
		/**Fixa för under och överåringar**/
		//Om underåring på avdelning, alltså börjat ett år för tidigt. Ska då räknas som 1:a åring
		//Om överåring är man egentligen för gammal för avdelningen		
		
		$tmp_avdelningar = get_avdelningsnamn('swedish', 'age', 'matrix', 'empty');
		//echo "<pre>";
		//print_r($tmp_avdelningar);
		//echo "</pre>";
		$my_grenar = count($tmp_avdelningar);
		//echo "Antal grenar " . $my_grenar;
						
		for ($my_gren = 0; $my_gren < $my_grenar; $my_gren++)	{	//Varje gren
			//echo "<p><b>" . $tmp_avdelningar[$my_gren][0] . "</b></p>";	//Skriv ut trenens namn
			//echo "<ul>";
			$cols = count($tmp_avdelningar[$my_gren]);
			$grenens_namn = $tmp_avdelningar[$my_gren][0];	//Grenens namn
			
			
			$grenens_avdelningar = get_avdelningar_gren($grenens_namn, 'swedish', 'no age', 'empty');	//Vill bara ha en avdelningslista
			$cols = count($grenens_avdelningar);	//Antal avdelningar på grenen
			//echo "Antal col " . $cols;
			//echo "Namn på grenen " . $tmp_avdelningar[$my_gren][0];
		  
			for ($col = 0; $col < $cols; $col++) {	//Avdelningar på en gren							
				
				$tmp_avdelningsnamn = $grenens_avdelningar[$col];	//Avdelningens namn
				$min_year = $tmp_avdelningar[$my_gren][$tmp_avdelningsnamn][0];	//Min ålder på avdelningen
				$max_year = $tmp_avdelningar[$my_gren][$tmp_avdelningsnamn][1];	//Max ålder på avdelningen
				$antal_year = $tmp_avdelningar[$my_gren][$tmp_avdelningsnamn][2];	//Antal år på avdelningen
				
				$lowyear = $tmp_year - $min_year;	//Underåring om större än
				$highyear = $tmp_year - $max_year;	//Överåring om mindre än
				
				$antal_unitmember = count($unit[$tmp_avdelningsnamn]);	//Antal olika födelseårtal för en specifik avdelning i $unit
				
				if ($antal_unitmember > $antal_year)	{	//Det finns fler födelseår än det ska, alltså under/över-åring finns
					
					//echo "<br>";
					foreach($unit[$tmp_avdelningsnamn] as $y => $y_value) {
						
						$y_antscouter = $y_value['total'];
						$y_antkillar = $y_value['sex'][1];
						$y_anttjejer = $y_value['sex'][2];
						$y_antannat = $y_value['sex'][0]+$y_value['sex'][3];
						
						if ($y > $lowyear)	{	//Om underåring och född detta år y
								
							$unit[$tmp_avdelningsnamn][$lowyear]['total'] += $y_antscouter;
							$unit[$tmp_avdelningsnamn][$lowyear]['sex'][1] += $y_antkillar;
							$unit[$tmp_avdelningsnamn][$lowyear]['sex'][2] += $y_anttjejer;
							$unit[$tmp_avdelningsnamn][$lowyear]['sex'][3] += $y_antannat;
							
							unset($unit[$tmp_avdelningsnamn][$y]);	//Vi tar bort underåringen från sitt riktiga födelseår
							
							//echo "LOWYEAR " . $lowyear;
							//echo "DET FINNS EN UNDERÅRING PÅ AVDELNINGEN";
							//echo "<br>";
						}
						else if ($y < $highyear)	{
							
							$unit[$tmp_avdelningsnamn][$highyear]['total'] += $y_antscouter;
							$unit[$tmp_avdelningsnamn][$highyear]['sex'][1] += $y_antkillar;
							$unit[$tmp_avdelningsnamn][$highyear]['sex'][2] += $y_anttjejer;
							$unit[$tmp_avdelningsnamn][$highyear]['sex'][3] += $y_antannat;
							
							unset($unit[$tmp_avdelningsnamn][$y]);	//Vi tar bort överåringen från sitt riktiga födelseår
							
							//echo "DET FINNS EN ÖVER-ÅRING PÅ AVDELNINGEN";
							//echo "<br>";
						}
						
						//echo "Unit= " . $unit[$tmp_avdelningsnamn] . ", Year=" . $y . ", Total=" . $y_antscouter . ", kille=" . $y_antkillar . ", tjej=" . $y_anttjejer . ", annat=" . $y_antannat;
					//echo "<br>";	
					}					
				}
				
				//echo ",<p>" . $tmp_avdelningsnamn . ", min" . $min_year . ", max" . $max_year . ", antal year" . $antal_year . ", antal unit" . $antal_unitmember . "</p>";
								
				//echo "," . $tmp_avdelningar[$my_gren][$grenens_avdelningar];
				//echo "<li>".$tmp_avdelningar[$my_gren][$col]."</li>";	//Skriv ut avdelningens namn
				//echo "aa".$tmp_avdelningar[$my_gren][$col]."bb";	//Skriv ut avdelningens namn
				//Col = vilken veckodag eller liknande
				
			}
		//	echo "</ul>";
		}
		/////////Test
		/*echo "<br>";
		echo "--Funktion - scoutnet_create_list";
		echo "<br>";
		
		foreach($unit as $x => $x_value) {	//Lista alla avdelningar och deras åldersintervall
			
			foreach($x_value as $y => $y_value) {
				
				$antscouter = $y_value['total'];
				$antkillar = $y_value['sex'][1];
				$anttjejer = $y_value['sex'][2];
				$antannat = $y_value['sex'][0]+$y_value['sex'][3];
				
				echo "Unit= " . $x . ", Year=" . $y . ", Total=" . $antscouter . ", kille=" . $antkillar . ", tjej=" . $anttjejer . ", annat=" . $antannat;
			echo "<br>";	
			}			
		}
		echo "<br>";*/
		/////Test
		
		return $unit;
	}
	
	function getall ($startYear) {
		global $scout_year;
		$thearray = ['total'=>0,'sex' =>[0=>0,1=>0,2=>0,3=>0]];
		foreach($scout_year as $year=>$year_data) {
			if ($year < $startYear) {
				$thearray = SumArray($year_data, $thearray);
			}
		}
		
		//print_r($thearray);
		return $thearray;
	}
	/*
	 * Kön
	 * Ålder
	 * Avdelning
	 *
	 * Letare = 09
	 * Spåre = 07-08
	 * Upptäckare = 06-05
	 * Äventyrare = 04-02
	 * Utmanare = 01-99
	 */


	function RandColor() {
		return sprintf("#%06x",rand(0,16777215));
	}

	function makeitbeautiful ($thearray,$themode) {
		$antscouter = $thearray['total'];
		$antkillar = $thearray['sex'][1];
		$anttjejer = $thearray['sex'][2];
		$antannat = $thearray['sex'][0]+$thearray['sex'][3];
		if ($antkillar > 0)
			$procentkillar = round(($antkillar/$antscouter)*100);
		else
			$procentkillar = 0;
		if ($anttjejer > 0)
			$procenttjejer = round(($anttjejer/$antscouter)*100);
		else
			$procenttjejer = 0;
		if ($antannat > 0)
			$procentannat = round(($antannat/$antscouter)*100);
		else
			$procentannat = 0;
		if ($themode == 1) {
			echo "<div class=\"allaantal\">$antkillar/$anttjejer/$antannat</div>";
		?>
		<div class="graph">
		<div style="height: <?=$procentkillar?>%;" class="bar killar"></div><div style="height: <?=$procenttjejer?>%;" class="bar tjejer"></div><div style="height: <?=$procentannat?>%;" class="bar annat"></div>
		</div>
		<div class="scoutertotalt"><?=$antscouter?></div>
		<?php
		} else if ($themode == 2) {
			echo "<table>\r<tr>\r";
			echo "<td><div class=\"graph\"><div style=\"height: $procentkillar%;\" class=\"bar killar\"></div><div style=\"height: $procenttjejer%;\" class=\"bar tjejer\"></div><div style=\"height: $procentannat%;\" class=\"bar annat\"></div></div></td>\r";
			echo "<td>\r<b>K&ouml;nsf&ouml;rdelning</b><br>\rKillar: $antkillar<br>\rTjejer: $anttjejer<br>\rAnnat: $antannat</td>";
			echo "<tr><td colspan=\"2\">Totalt antal scouter: $antscouter</td></tr>";
			echo "</tr></table>";
		}
		else
			echo "<div class=\"scoutertotalt\">$antscouter</div>";
	}
	
	/*
	 * Sumarize two arrays
	 */ 
	function SumArray ($a,$b) {
		foreach ($a as $key => $value){
			if (is_array($value))
				foreach ($value as $key2 => $value2)
					$b[$key][$key2] += $value2;
			else
				$b[$key] += $value;
		}
		return $b;
	}
	
	/*
	 * Årtal, year = ett visst år
	 * lowyear, highyear. Högsta och lägsta åldersbegränsning för grenen
	 */
	function CheckYear($year,$lowyear,$highyear) {
		if ($lowyear-1 == $highyear) {
			if ($year == $lowyear || $year == $highyear)
				return true;
		} else {
			if ($year == $lowyear || $year == $lowyear-1 || $year == $highyear) {
				return true;
			}
		}
		return false;
	}	
	
	/*
	 * Remove empty elements of an array and creates a new array with new indexes
	 */
	function scoutnet_remove_empty_elements($array)	{		
		return array_values(array_filter($array));		
	}
		
	
	
	/*
	 * Get an associative array of alla avdelningar och vilken ålder de är för
	 */
	function get_avdelningsnamn($lang, $age, $matrix, $empty="")	{
		
		$avdelnings_namn = array();
		$letare = get_avdelningar_gren('letare', $lang, $age, $empty);
		$sparare = get_avdelningar_gren('sparare', $lang, $age, $empty);
		$upptackare = get_avdelningar_gren('upptackare', $lang, $age, $empty);
		$aventyrare = get_avdelningar_gren('aventyrare', $lang, $age, $empty);
		$utmanare = get_avdelningar_gren('utmanare', $lang, $age, $empty);
		
		if ('matrix'==$matrix)	{
			array_unshift($letare, 'letare');	//Grennamnet ska vara först och sedan listas vilka avdelningar
			array_unshift($sparare, 'sparare');
			array_unshift($upptackare, 'upptackare');
			array_unshift($aventyrare, 'aventyrare');
			array_unshift($utmanare, 'utmanare');
			
			$avdelnings_namn[] = $letare;
			$avdelnings_namn[] = $sparare;
			$avdelnings_namn[] = $upptackare;
			$avdelnings_namn[] = $aventyrare;
			$avdelnings_namn[] = $utmanare;
			
			
		}
		else	{
			$avdelnings_namn = $letare;
			$avdelnings_namn += $sparare;
			$avdelnings_namn += $upptackare;
			$avdelnings_namn += $aventyrare;
			$avdelnings_namn += $utmanare;
		}
		
		return $avdelnings_namn;
	}
	
	/*
	 * Get an associative array of avdelningar på en specifik gren
	 * If lang == english, the text is not using swedish letters
	 * If age == age, the gren min and max interval will be included
	 * If empty == empty, the blank/empty avdelning elements will be removed
	 */
	function get_avdelningar_gren($args, $lang, $age, $empty="")	{
		
		$lista_avdelningar_gren;

		if ('letare'==$args)	{
			$lista_avdelningar_gren = get_avdelningar_letare($lang, $empty);
		}
		else if ('sparare'==$args)	{
			$lista_avdelningar_gren = get_avdelningar_sparare($lang, $empty);
		}
		else if ('upptackare'==$args)	{
			$lista_avdelningar_gren = get_avdelningar_upptackare($lang, $empty);
		}
		else if ('aventyrare'==$args)	{
			$lista_avdelningar_gren = get_avdelningar_aventyrare($lang, $empty);
		}
		else if ('utmanare'==$args)	{
			$lista_avdelningar_gren = get_avdelningar_utmanare($lang, $empty);
		}
		
		if ('age'!=$age)	{	//If you only want a avdelningslista
			return $lista_avdelningar_gren;			
		}
		
		
		$arrlength = count($lista_avdelningar_gren);
		
		if (0==$arrlength)	{			
			return array();
		}
		
		$avdelningar_gren;
		
		for ($i = 0; $i < $arrlength; $i++)	{
			$avdelnings_namn = $lista_avdelningar_gren[$i];
			
			if (empty($avdelningar_gren))	{
				$avdelningar_gren = array($avdelnings_namn => get_age_gren($args));
			}
			else	{
				$avdelningar_gren += array($avdelnings_namn => get_age_gren($args));
			}
		}		
		return $avdelningar_gren;
	}
	
	/*
	 * Returnera minålder för en gren
	 */
	function get_age_min_gren($args)	{
		$gren_years = get_age_gren($args);		
		$min_year = $gren_years[0];
		return $min_year;
	}
	
	/*
	 * Returnera maxålder för en gren
	 */
	function get_age_max_gren($args)	{
		$gren_years = get_age_gren($args);		
		$max_year = $gren_years[1];
		return $max_year;
	}
	
	/*
	 * Returnera antal år som man går på en gren
	 */
	function get_antal_year_gren($args)	{
		$gren_years = get_age_gren($args);		
		$antal_year = $gren_years[2];
		return $antal_year;
	}
	
	
	
	/*
	 * Returnerar array med grenar
	 * Fast längd
	 * Rör ej
	 */
	function scoutnet_get_grenar()	{
		
		$grenar = array("letare", "sparare", "upptackare", "aventyrare", "utmanare");
		return $grenar;
	}	
	
	
	function scoutnet_print_matrix($arg)	{
		
		$rows = count($arg);
		for ($row = 0; $row < $rows; $row++)	{
		  echo "<p><b>Row number $row</b></p>";
		  echo "<ul>";
		  $cols = count($arg[$row]);
		  for ($col = 0; $col < $cols; $col++) {
			echo "<li>".$arg[$row][$col]."</li>";
		  }
		  echo "</ul>";
		}		
	}
	
	/*
	 * Skriv ut data för alla avdelningar på aktuell gren
	 */
	function scoutnet_print_gren($gren, $thisyear, $number_of_col, $gren_namn)	{
		
		global $avdelningar;
		global $scout_year;
		
		$colspan_left = 2;
		$colspan_of_one = 2;	//Bredden på en avdelning
			
		$lista_avdelningar_gren = get_avdelningar_gren($gren, 'english', 'no age', 'no empty');
		$antal_avdelningar = count($lista_avdelningar_gren);
		$lista_alla_grenar = scoutnet_get_grenar();
		
		$gren_age = get_age_gren($gren);
		$gren_min_age = $gren_age[0];
		$gren_max_age = $gren_age[1];
		$gren_antal_year =  $gren_age[2];
		
		$gren_unused = $gren . " unused";
		$gren_moved = $gren . " moved";
		
		$y = 0;	//räknare för varje årskull
		
		
		for ($arskull = 1; $arskull < $gren_antal_year+1; $arskull++)	{
			$tmp_since_last_match = 0;
			$ant_varv = 0;
			$colspan_right = 2;		//Bredd på avdelning vid steg till höger
		
			echo "<tr>";		
				if (1==$arskull)	{
					echo "<td rowspan=" . $gren_antal_year . "><div>" . $gren_namn . "</div></td>";
				}
				
				$tmp_y = $thisyear-$gren_min_age-$y;
				echo "<td><div class='smalltitle flip'>&aring;r " . $arskull . "<br><i>" . $tmp_y . "</i></div></td>";
				
				for ($k = 0; $k < $antal_avdelningar; $k++)	{
					
					if (!empty($lista_avdelningar_gren[$k]))	{	//Om avdelning i denna kolumn
				
						
						$tmp_colspan_left = $colspan_left * $tmp_since_last_match;
						//echo "      K =" . $k . "  LEFT=" . $tmp_colspan_left;
						
						if (empty($lista_avdelningar_gren[$k-1]))	{
							//Bara vänster färg om ej förra fanns
							
							if (0!=$tmp_since_last_match)	{	//Om avdelning längst till vänster
								echo "<td colspan=" . $tmp_colspan_left . " class='" . $gren_unused . "'></td>";
							}
						}
						
						echo "<td class='" . $gren . "'>";
						CleanArray($gren, $avdelningar,$thisyear-$gren_min_age-$y, $lista_avdelningar_gren[$k]);
						echo "</td>";
						//echo "Listan = " . $lista_alla_grenar[0] . $gren . "   ";
						if (1!=$arskull)	{	//Endast åk 1 ska vara flyttafärgen
							$gren_moved = $gren;	
						}
						if (1==$arskull && $gren==$lista_alla_grenar[0])	{//Första året på första grenen ska ha samma färg
							$gren_moved = $gren;
						}
						echo "<td class='" . $gren_moved . "'>";
						CleanArray($gren, $avdelningar,$thisyear-$gren_min_age+1-$y, $lista_avdelningar_gren[$k]);
						echo "</td>";
						
						/*****Detta är till höger om den högraste avdelningen*/	
						//Räkna hur många tomma till höger som finns
						$max_kolumn = $number_of_col;
						$ant_varv = 0;
						for ($m = $k; $m < $max_kolumn; $m++)	{
							//Kolla alla kolumner till höger. Om finns någon så varv=0
							
							if (empty($lista_avdelningar_gren[$m+1]))	{
								$ant_varv++;
							}	
							else	{
								$ant_varv = 0;
								$m = $antal_avdelningar;
							}
						}
						$ant_varv--;
						//echo "Antal tomma varv till höger " . $ant_varv . " ";
						//echo "Max col = " . $max_kolumn . " ";
				
						if ($ant_varv>0)	{
							//Bara höger färg om ej nästa finns
						//	echo "Antal tomma varv till höger " . $ant_varv . " ";
						//	echo "Max col = " . $max_kolumn . " ";
							
						}				
						$tmp_since_last_match = 0;			
						
						/*****Slut om till höger om den högraste avdelningen****/
						
					}
					else	{	//Om ej avdelning i denna kolumn
						$tmp_since_last_match++;						
					}			
				
				}
				
				$colspan_right = $colspan_right * $ant_varv;
				//echo "varv " . $ant_varv . "  ";
				
				if (0!=$ant_varv)	{	//Om avdelningen är längst till höger				
					echo "<td colspan=" . $colspan_right . " class='" . $gren_unused . "'></td>";
				}
				
				//Summan till höger
				echo "<td class='" . $gren . "'>";
				CleanArray($gren, $scout_year,$thisyear-$gren_min_age-$y);
				echo "</td>";
				echo "<td class='" . $gren . "'>";
				CleanArray($gren, $scout_year,$thisyear-$gren_min_age+1-$y);
				echo "</td>";				
			echo "</tr>";
			
			$y++;	//Öka ett för varje varv
		}		
	}	
	
	/*
	 * gren = specificera gren
	 * number_of_col = number of columns in the entire table
	 */
	function scoutnet_print_gren_sum($gren, $thisyear, $number_of_col)	{
		
		global $avdelningar;
		$gren_age = get_age_gren($gren);
		$gren_min_age = $gren_age[0];
		$gren_max_age = $gren_age[1];
		
		$lista_avdelningar_gren = get_avdelningar_gren($gren, 'english', 'no age', 'no empty');
		$antal_avdelningar = count($lista_avdelningar_gren);
		
		$gren_unused = $gren . " unused";
		
		$colspan_left = 2;
		$colspan_right = 2;		//Bredd på avdelning vid steg till höger
		$colspan_of_one = 2;	//Bredden på en avdelning
		$tmp_since_last_match = 0;
		$ant_varv = 0;
		
		echo "<tr>";
			echo "<td colspan=" . $colspan_of_one . " class='smalltitle'>Scouter/<br>Avdelning</td>";
			//echo "<td colspan=" . $colspan_left . " class='" . $gren_unused . "'></td>";
		
		for ($k = 0; $k < $antal_avdelningar; $k++)	{
			
			if (!empty($lista_avdelningar_gren[$k]))	{	//Om avdelning i denna kolumn
			
				$tmp_colspan_left = $colspan_left * $tmp_since_last_match;
				//echo "      K =" . $k . "  LEFT=" . $tmp_colspan_left;
				
				if (empty($lista_avdelningar_gren[$k-1]))	{
					//Bara vänster färg om ej förra fanns
					
					if (0!=$tmp_since_last_match)	{	//Om avdelning längst till vänster
						echo "<td colspan=" . $tmp_colspan_left . " class='" . $gren_unused . "'></td>";
					}
				}
				echo "<td class='" . $gren . "'>";
				getsum($avdelningar, $gren, $thisyear-$gren_max_age, $lista_avdelningar_gren[$k]);
				
				echo "</td>";
				echo "<td class='" . $gren . "'>";
				getsum($avdelningar, $gren, $thisyear-$gren_max_age+1, $lista_avdelningar_gren[$k]);
				
				echo "</td>";				
				
				//Räkna hur många tomma till höger som finns
				$max_kolumn = $number_of_col;
				$ant_varv = 0;
				for ($m = $k; $m < $max_kolumn; $m++)	{
					//Kolla alla kolumner till höger. Om finns någon så varv=0
					
					if (empty($lista_avdelningar_gren[$m+1]))	{
						$ant_varv++;
					}	
					else	{
						$ant_varv = 0;
						$m = $antal_avdelningar;
					}
				}
				$ant_varv--;
				//echo "Antal tomma varv till höger " . $ant_varv . " ";
				//echo "Max col = " . $max_kolumn . " ";
		
				if ($ant_varv>0)	{
					//Bara höger färg om ej nästa finns
					//echo "Antal tomma varv till höger " . $ant_varv . " ";
					//echo "Max col = " . $max_kolumn . " ";
					
				}				
				$tmp_since_last_match = 0;
			}
			else	{	//Om ej avdelning i denna kolumn
				$tmp_since_last_match++;
			}			
		}	
		$colspan_right = $colspan_right * $ant_varv;
	
		if (0!=$ant_varv)	{	//Om avdelningen är längst till höger				
			echo "<td colspan=" . $colspan_right . " class='" . $gren_unused . "'></td>";
		}
		
		//Summan till höger
		
		echo "<td class='" . $gren . "'>";
		getsum($avdelningar, $gren, $thisyear-$gren_max_age);		
		echo "</td>";
		
		echo "<td class='" . $gren . "'>";
		getsum($avdelningar, $gren, $thisyear-$gren_max_age+1);		
		echo "</td>";		
		
		echo "</tr>";		
	}
	
	function scoutnet_get_number_of_active_columns()	{
		
		$grenar = scoutnet_get_grenar();
		$ant_grenar = count($grenar);
		$antal_used_kolumner = 0;
		
		for ($w = 0; $w < $ant_grenar; $w++)	{
			$gren_namn = $grenar[$w];
			$avdelning_lista = get_avdelningar_gren($gren_namn, 'swedish', 'no age', 'non empty');
			$ant_avdelning_lista = count($avdelning_lista);
			
			for ($r = 0; $r < $ant_avdelning_lista; $r++)	{	//Kolla alla avdelningar på en specifik gren
				
				//Om kolumnen int är tom så uppdateras numret för den kolmnen längst till höger
				
				if (!empty($avdelning_lista[$r]))	{	//om tom kolumn
					
					if ($r > $antal_used_kolumner)	{	//Uppdatera vid behov
						
						$antal_used_kolumner = $r;
					}
					
				}				
			}		
		}
		$antal_used_kolumner++;	//Vi måste öka med ett
		//echo "Antal kolumner som används " . $antal_used_kolumner . "    ";
		return $antal_used_kolumner;
	}
	
/****Slut detta ska ligga utanför funktionen******************************************************/
		$avdelningar;
		$scout_year;
		$sum_unit;
		$date;
		$tmp_year;
		$termin;
		
		global $avdelningar;
		global $scout_year;
		global $sum_unit;
		global $waitinglist;
		global $date;
		global $tmp_year;
		
		require_once 'Math.php';
		
		$date = new DateTime();
		//$date->setDate(2016,12,18);	//För testning
		//$date->setDate(2017,04,25);
		//$date->setDate(2018,04,25);
		//$date->setDate(2017,12,25);
		
		$termin="vt";		
		
		if ($date->format("n") > 8)	{
			$termin="ht";
		}
		if ($date->format("n") > 8 && $date->format("j") > 10)	{	//Om senare än 10 augusti på året.
			$termin="ht";
			//echo "Hösttermin";
			$tmp_ar = $date->format("Y")+1;	//Om höst så flyttar vi fram datumet till våren för enkelhetens skull
			//echo "Mitt year is " . $tmp_ar;
			$date->setDate($tmp_ar,02,22);	//22 februari nästa år	
			
			$termin="vt";
		}		
		
		//$termin="ht";
		if(isset($_GET['termin'])) {
			$intermin = $_GET['termin'];
			if ($intermin == "vt" || $intermin == "ht")
				$termin = $intermin;
		}
		
		if ($termin == "vt")	{	//Om våren (och hösten)
			$thisyear = $date->format("Y")-0;
			$nextyear = $date->format("Y")+1;
			
			$year2 = $date->format("y");			
			
			$dispyear1 = "ht ".($year2-1)."/<br>vt ".$year2;
			$dispyear2 = "ht ".$year2."/<br>vt ".($year2+1);
		}	
		
		
		$tmp_year = $date->format("Y");
		
		$waitinglist = scoutnet_newscouts();	//Fördelad på årtal och antal, total, kille, tjej, annat
		
		$avdelningsnamn = get_avdelningsnamn('english', 'age', 'array', 'empty');
		
		//$avdelningsnamn = ["bavrarna"=>[8,8],"insekterna"=>[9,10],"gnagarna"=>[9,10],"asarna"=>[11,12],"skogsbrynet"=>[11,12],"stigfinnarna"=>[13,15],"seniorerna"=>[16,18]];

		
		/*test*/
			$arr = get_avdelningar_letare('english', 'empty');
			for($x = 0; $x < count($arr); $x++) {
			//	echo $arr[$x];
			}			
			
			foreach($avdelningsnamn as $x => $x_value) {	//Lista alla avdelningar och deras åldersintervall
				//echo "Key=" . $x . ", Value=" . $x_value[0] . ", " . $x_value[1];
				//echo "<br>";
			}		
		/*slut test*/

		$test = scoutnet_create_list();	//Returnerar lista över avdelningar, vilka som är födda vilka år mm
		//echo "<pre>";
		//print_r($test);		
		//echo "</pre>";				
		
		foreach($test as $unit => $unitvalues) {	//För varje avdelning
			if (array_key_exists(scouternaplugins_fixthatstring($unit),$avdelningsnamn)) {	//Om i vår avdelningslista
				$avdelning = scouternaplugins_fixthatstring($unit);
				$lowyear = $tmp_year-$avdelningsnamn[$avdelning][0];	//The youngest birthyear i avdelningen.tex 2017-9=2008
				$highyear = $tmp_year-$avdelningsnamn[$avdelning][1];	//The oldest birthyear i avdelningen = 2017-10=2007
				
				foreach($unitvalues as $year => $values) {	//Födesleårsvärden för en avdelning [avdelningens namn][år]
					//$year är de olika födelseåren, t.ex 2008,2009
					//values är de värden som är förknippade till en avdelning ett visst födelseår
					//highyear är en lägre siffra än lowyear. tex high=2008 och low=2009
					
					if (CheckYear($year,$lowyear,$highyear)) {
						if (isset($avdelningar[$avdelning][$year]))	{
							echo "<h1><b>WE GOT PROBLEMS!!!</b></h1>";
							echo "Detta år på avdelningen " . $avdelning . " och året " . $year . "är redan satt";
						}
						$avdelningar[$avdelning][$year] = $values;
						
						if (isset($scout_year[$year]))	{
							$scout_year[$year] = SumArray($scout_year[$year], $values);
						}
						else	{
							$scout_year[$year] = $values;
						}
						//echo "Händer detta någon gång year=" . $year . ", " . $lowyear . ", " . $highyear;

					}
					else	{	//Om åk 1, flytta ett år
							//Denna ålder < grenens lägsta ålder
						if ($year < $tmp_year-$lowyear) {
							
							$avdelningar[$avdelning][$lowyear] = SumArray($unitvalues[$year], $unitvalues[$lowyear]);
							unset($unitvalues[$year]);
							
							if (isset($scout_year[$lowyear]))	{
								$scout_year[$lowyear] = SumArray($scout_year[$lowyear], $values);
							}
							else	{
								$scout_year[$lowyear] = $values;
							}
						}
						else	{	//Om åk 1
							
							$avdelningar[$avdelning][$highyear] = SumArray($unitvalues[$year], $unitvalues[$highyear]);
							unset($unitvalues[$year]);
							if (isset($scout_year[$highyear]))	{	
								$scout_year[$highyear] = SumArray($scout_year[$highyear], $values);
							}
							else	{
								$scout_year[$highyear] = $values;	//Åk 1 i högerspalten summa
							}
						}
					}
				}
			}
		}		
		//echo "<pre>";
		//print_r($avdelningar);	//Stämmer
		//print_r($scout_year);
		//echo "</pre>";
		
		$b_color = RandColor();
		$g_color = RandColor();
		$o_color = RandColor();
		
		?>
		<div class="area main">
			<table>
				<tr>
				
					
					<?php											
						
						echo "<th colspan='2'>&nbsp;</th>";
						
						$tmp_avdelningar = get_avdelningsnamn('swedish', 'no age', 'matrix', 'no empty');
							
						$max_active_col = scoutnet_get_number_of_active_columns();
					
						$title_matrix;
						
						$rows = count($tmp_avdelningar);
						
						for ($row = 0; $row < $rows; $row++)	{	//Varje gren
						//	echo "<p><b>" . $tmp_avdelningar[$row][0] . "</b></p>";	//Skriv ut grenens namn
						//	echo "<ul>";
							$cols = count($tmp_avdelningar[$row]);
						  
							for ($col = 1; $col < $cols; $col++) {	//Avdelningar på en gren							
						//		echo "<li>".$tmp_avdelningar[$row][$col]."</li>";	//Skriv ut avdelningens namn
								//Col = vilken veckodag eller liknande
								
								for ($k = 0; $k < $max_active_col; $k++)	{	//Vi gör minus som indexvärde
									
									if (empty($title_matrix[$k][$col]))	{	//Om det är tomt på denna rad
									
										$title_matrix[$k][$col] = $tmp_avdelningar[$row][$col];	//Utplacering
										$k = $max_active_col;
									}									
								}
							}
						//	echo "</ul>";
						}
						//echo "Max kolumn" . $max_active_col;
						
						$tmp_max_height_col = count($title_matrix)-1;
						//echo "MAX " . $tmp_max_height_col;
						
						//Varje kolumn i avdelningstitel i denna nya matris
						
						for ($col = 1; $col < $max_active_col+1; $col++)	{						
							
							$tmp_print = "<th colspan='2'><span class='avdelningar'>";
							$my_br = "";
							$my_avdelningar = "";
							for ($row = 0; $row < $tmp_max_height_col+1; $row++)	{			
								
								if (!empty($title_matrix[$row][$col]))	{	//Om finns avdelning
									
									//echo $title_matrix[$row][$col].", ";
										
									if (!empty($title_matrix[$tmp_max_height_col][$col]) && $row==0)	{
										//Om denna kolumn har samma längd som den längsta och inte är första raden i kolumnen
									
									}
									if ($row!=0)	{
										
										$my_avdelningar .= "<br>";
										//$my_avdelningar .= "NR1";
									}									
									$my_avdelningar .= $title_matrix[$row][$col];									
								}
								else	{	
									$my_br .= "<br>";
								}
							}
							$tmp_print .= $my_br;
							$tmp_print .= $my_avdelningar;
							//$tmp_print = chop($tmp_print, "<br>");
							$tmp_print .= "</span></th>";
							echo $tmp_print;			
							
							//echo "NYYYY rad";						
						}						
					/*
					<th colspan="2"><span class="avdelningar">    <br><br>B&auml;vrarna    </span></th>
					<th colspan="2"><span class="avdelningar">  <br>F&aring;glarna     <br>Asarna    </span></th>
					<th colspan="2"><span class="avdelningar">  Gnagarna <br>Skogsbrynet <br>Stalle</span></th>
					<th colspan="2"><span class="avdelningar"><br>Stigfinnarna</span></th>
					<th colspan="2"><span class="avdelningar"><br>Seniorerna</span></th>
					*/					
					?>					
					<th colspan="2">Totalt</th>
				</tr>
				<tr class="yearrow">
										
					<?php
						echo "<td colspan='2'>&nbsp;</td>";
						for ($r = 0; $r<$max_active_col+1; $r++)	{	//Årtalen som visar i toppen							
								echo "<td>" . $dispyear1 . "</td>";
								echo "<td>" . $dispyear2 . "</td>";							
						}
					//echo "Antal gånger " . $max_active_col;
					?>					
				</tr>
				</tr>				
				<?php
				
		//echo "<pre>";
		//print_r($avdelningar);	//Stämmer
		//print_r($scout_year);		
		//echo "</pre>";
					//Skriver ut stora stabellen med siffror på  gren/åk1, 2 mm samt grensumma
					$my_grenar = scoutnet_get_my_grenar();					
					$ant_my_grenar = count($my_grenar);					
				//	$ant_my_grenar = 2;					
					$grenar = scoutnet_get_grenar();
					
					for ($w = 0; $w < $ant_my_grenar; $w++)	{
						
						$my_gren = $my_grenar[$w];
						$gren = $grenar[$w];
						
						if (!empty($my_gren))	{	//Skriv bara ut valda grenar						
							scoutnet_print_gren($gren, $thisyear, $max_active_col, $my_gren);
							scoutnet_print_gren_sum($gren, $thisyear, $max_active_col);
						}						
					}
					
		//echo "<pre>";
		//print_r($avdelningar);	//Stämmer
		//print_r($scout_year);		
		//echo "</pre>";
					
				?>				
				<tr>
					<td colspan="11" class=""></td>
					<td><?$totalscouts?></td>
					<td></td>
				</tr>
			</table>
		</div>
		
		<?php
		//echo "<pre>";
		//print_r($waitinglist);
		//echo "</pre>";
		?>
		</div>
		<div class="area stats">
			<h2>Statistik</h2>
			<table>
				<tr>
					<th>Scouter p&aring; avdelningen just nu</th>
					<th>Prognos n&auml;sta h&ouml;st</th>
				</tr>
				<tr>
					<?php
					
					for ($m = 0; $m<2; $m++)	{
						
						$what_year = $thisyear; //The first lap, show for this year
						if ($m == 1)	{	//The second lap, show for next year
							$what_year = $nextyear;
						}
						echo "<td>";						
							echo "<table>";	
								echo "<tr>";
									
									$sv_avdelningsnamn = get_avdelningsnamn('svenska', 'age', 'array', 'empty');
									foreach($sv_avdelningsnamn as $unit => $unit_age) {
										//Skriver ut avdelningsnamnen under statistik
										echo "<th>" . $unit . "</th>";
									}								
										/*echo "<th>F&aring;glarna</th>
										<th>Gnagarna</th>
										<th>Asarna</th>
										<th>Skogsbrynet</th>
										<th>Stigfinnarna</th>
										<th>Seniorerna</th>";*/
								
									echo "</tr>";
									echo "<tr>";
								
									$grenar = scoutnet_get_grenar(); //indexed array
									//print_r($grenar);
									
									$ant_grenar = count($grenar);
										
									for ($i = 0; $i < $ant_grenar; $i++)	{	//For each gren		
										
										//Grenens avdelningar
										$temp_avdelningar = get_avdelningar_gren($grenar[$i], 'english', 'age', 'empty');
										$ant_avdelningar = count($temp_avdelningar);
										
										//print_r($temp_avdelningar);									
										
										foreach($temp_avdelningar as $temp_avdelning => $avdelning_value) {	//For each avdelning
										
											//print_r ($temp_avdelning);
											$max_year = get_age_max_gren($grenar[$i]);	//Get the max age of this gren
											
											echo "<td class='" . $grenar[$i] ."'>";	//Colour as the gren
											getsum($avdelningar, $grenar[$i], $what_year-$max_year,$temp_avdelning,1);
											echo "</td>";
										}
									}
									/*
									<td class='letare'><?=getsum($avdelningar, 'letare', $thisyear-8,'bavrarna',1)?></td>
									<td class="sparare"><?=getsum($avdelningar, 'sparare', $thisyear-10,'faglarna',1)?></td>
									<td class="sparare"><?=getsum($avdelningar, 'sparare', $thisyear-10,'gnagarna',1)?></td>
									<td class="upptackare"><?=getsum($avdelningar,'upptackare', $thisyear-12,'asarna',1)?></td>
									<td class="upptackare"><?=getsum($avdelningar, 'upptackare', $thisyear-12,'skogsbrynet',1)?></td>
									<td class="aventyrare"><?=getsum($avdelningar, 'aventyrare', $thisyear-15,'stigfinnarna',1)?></td>
									<td class="utmanare"><?=getsum($avdelningar, 'utmanare', $thisyear-18,'seniorerna',1)?></td>*/
									
								echo "</tr>";							
							echo "</table>";						
						echo "</td>";
					}
					?>					
				</tr>
				<tr>
					<td class="stor">
						<?php
						$startyear = $thisyear - scoutnet_get_start_age();
						makeitbeautiful(getall($startyear),2);
						//getsum(getall($thisyear),"no gren","","",1)
						?>
					</td>
					<td class="stor">
						<?php
						$startyear = $nextyear - scoutnet_get_start_age();
						makeitbeautiful(getall($startyear),2);
						//getsum(getall($thisyear),"no gren","","",1)
						?>
					</td>
				</tr>
			</table>
			<?php
			/*echo "<pre>";
			print_r($sum_unit);
			echo "</pre>";

			echo "<pre>";
			print_r($waitinglist);
			echo "</pre>";*/
			?>
		
		</div>
		<div id="right">
		<div class="area legend">
		<i>F&ouml;rklaringar:</i><br>
		Hur m&aring;nga av varje k&ouml;n: 0/0/0 = killar/tjejer/annat<br>
		<span class="killar">#</span> = Killar <span class="tjejer">#</span> = Tjejer <span class="annat">#</span> = Annat<br>
		<i>Dessa f&auml;rger slumpas fram. Uppdatera sidan om f&auml;rgerna &auml;r f&ouml;r lika.</i>
		</div>
		<div class="area queue">
		<h2>K&ouml;</h2>
		<i><u>Av dessa scouter &auml;r det bara den yngsta grenen som l&auml;ggs till i prognoslistan.</u></i>
		<table>
		<tr><th>F&ouml;dd:</th><th>Antal:</th></tr>
		<?php
		$startyear = $thisyear - scoutnet_get_start_age();
		ksort($waitinglist);
		foreach($waitinglist as $year => $values) {
			$antal = $values['total'];
			if ($year == $startyear)	{
				echo "<tr class=\"rightage\"><td>$year</td><td>$antal</td></tr>";
			}
			else if ($year < $startyear)	{
				echo "<tr class=\"overage\"><td>$year</td><td>$antal</td></tr>";
			}
			else	{
				echo "<tr><td>$year</td><td>$antal</td></tr>";
			}
		}
		
		echo "</table>";		
		echo "<pre>";
		//print_r($waitinglist);
		echo "</pre>";
		?>
		</div>
		</div>		
		
		<?php
		/************Ta bort allt nedanför detta vid flytt till riktiga plugin************/

		$time = microtime();
		$time = explode(' ', $time);
		$time = $time[1] + $time[0];
		$finish = $time;
		$total_time = round(($finish - $start), 4);
		echo '<footer>Page generated in '.$total_time.' seconds.</footer>';
		?>	
	
</body>
</html>
