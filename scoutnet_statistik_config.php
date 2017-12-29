<?php

	/*
	 * Returnera lista av letaravdelningar
	 * Platsen i arrayen där avdelningsnamnet står bestämmer i vilken kolumn som avdelningen visas
	 * kan vara bra för att enkelt illustrera att tisdagsspårarna flyttar som standard till tisdagsupptäckarna,
	 * så den kan skrivas i samma kolumn under respektive gren. Blir mer lättöverskådligt då.
	 */
	function get_avdelningar_letare($lang, $empty="")	{
		
		$letare = array("Bävrarna", "", "");
		
		if ('empty'==$empty)	{
			$letare = scoutnet_remove_empty_elements($letare);
		}
		
		if ('english'==$lang)	{
			$letare = scoutnet_convert_array_to_english_letters($letare);
		}		
		return 	$letare;
	}
	
	/*
	 * Skriv en formel på aktuell plats för att bestämma hur antal scouter nästa år ska vara
	 * skriv avdelningsnamn och vanliga räknetecken
	 * Om kolla i väntelistan i stället för avdelningsnamn så skriver du W.
	 * Värden kan avrundas upp eller ner, avsluta formeln med + eller -
	 */
	function scoutnet_get_letare_calc($avdelning_index)	{
		
		$letare = array("W", "", "","", "", "", "", "", "");
		return $letare[$avdelning_index];
	}
	/******************/
	
	/*
	 * Returnera lista av spåraravdelningar
	 */
	function get_avdelningar_sparare($lang, $empty="")	{
		
		$sparare = array("", "Insekterna", "Gnagarna", "");
		
		if ('empty'==$empty)	{
			$sparare = scoutnet_remove_empty_elements($sparare);
		}
		
		if ('english'==$lang)	{
			$sparare = scoutnet_convert_array_to_english_letters($sparare);
		}		
		return 	$sparare;
	}
	
	function scoutnet_get_sparare_calc($avdelning_index)	{
		
		$sparare = array("", "Bävrarna/2+", "Bävrarna/2-","", "", "", "", "", "");
		return $sparare[$avdelning_index];
	}
	
	/*******************/
	/*
	 * Returnera lista av upptäckaravdelningar
	 */
	function get_avdelningar_upptackare($lang, $empty="")	{
		
		$upptackare = array("", "Asarna", "Skogsbrynet","", "", "", "", "", "");
		
		if ('empty'==$empty)	{
			$upptackare = scoutnet_remove_empty_elements($upptackare);
		}		
		
		if ('english'==$lang)	{
			$upptackare = scoutnet_convert_array_to_english_letters($upptackare);
		}		
		return 	$upptackare;
	}
	
	function scoutnet_get_upptackare_calc($avdelning_index)	{
		
		$upptackare = array("", "Insekterna", "Gnagarna","", "", "", "", "", "");
		return $upptackare[$avdelning_index];
	}
	
	/***************/
	
	/*
	 * Returnera lista av äventyraravdelningar
	 */
	function get_avdelningar_aventyrare($lang, $empty="")	{
		
		$aventyrare = array("", "", "", "Stigfinnarna", "");
		
		if ('empty'==$empty)	{
			$aventyrare = scoutnet_remove_empty_elements($aventyrare);
		}
		
		if ('english'==$lang)	{
			$aventyrare = scoutnet_convert_array_to_english_letters($aventyrare);
		}		
		return 	$aventyrare;
	}
	
	function scoutnet_get_aventyrare_calc( $avdelning_index)	{
		
		$aventyrare = array("", "", "","Asarna+Skogsbrynet", "", "", "", "", "");
		return $aventyrare[$avdelning_index];
	}
	
	/***************/
	
	/*
	 * Returnera lista av utmanaravdelningar
	 */
	function get_avdelningar_utmanare($lang, $empty="")	{
		
		$utmanare = array("", "", "", "", "Seniorerna", "", "");
		
		if ('empty'==$empty)	{
			$utmanare = scoutnet_remove_empty_elements($utmanare);
		}
		
		if ('english'==$lang)	{
			$utmanare = scoutnet_convert_array_to_english_letters($utmanare);
		}		
		return 	$utmanare;
	}
	
	function scoutnet_get_utmanare_calc( $avdelning_index)	{
		
		$utmanare = array("", "", "","", "Stigfinnarna", "", "", "", "");
		return $utmanare[$avdelning_index];
	}
	
	
	/*
	 * Returnernar åldersintervallet för gren
	 */
	function get_age_gren($args)	{
		
		if ('letare'==$args)	{
			$min_year = 8;
			$max_year = 8;
			$antal_year = $max_year - $min_year + 1;
		}
		else if ('sparare'==$args)	{
			$min_year = 9;
			$max_year = 10;
			$antal_year = $max_year - $min_year + 1;
		}
		else if ('upptackare'==$args)	{
			$min_year = 11;
			$max_year = 12;
			$antal_year = $max_year - $min_year + 1;
		}
		else if ('aventyrare'==$args)	{
			$min_year = 13;
			$max_year = 15;
			$antal_year = $max_year - $min_year + 1;
		}
		else if ('utmanare'==$args)	{
			$min_year = 16;
			$max_year = 18;
			$antal_year = $max_year - $min_year + 1;
		}
		return array($min_year, $max_year, $antal_year);
	}
	
	/*
	 * Returnerar array med grenar
	 * Fast längd
	 * Ändras om man vill byta namn på grenar
	 */
	function scoutnet_get_my_grenar()	{
		
		$grenar = array("Letare", "Spårare", "Upptäckare", "Äventyrare", "Utmanare");
		return $grenar;
	}

?>