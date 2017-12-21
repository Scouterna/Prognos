# Statistik
Fristående statistikprogram som visar antalet på respektive avdelning nu och antal nästa år

## Begränsningar
- Max antal år på en avdelning är 3 år
- Avdelningsnamn ska endast innehålla bokstäver

## Övrigt
Om underåring (börjat för tidigt på avdelning) eller överåring (är egentligen för gammal) så räknas man som 1:a åring respektive sistaåring (normalt sett 2:a åring eller 3:e åring beroende på inställning)

Följande behöver ställas in för respektive kår
- scoutnet_get_option_kar_id()
  - Ändra kårens id-nummer. Hittas i Scoutnet under Webbkoppling->Get a detailed csv/xls/json list of all members
- scoutnet_get_option_api_nyckel_kar_full()
  -Ändra api-nyckeln. Hittas i Scoutnet under Webbkoppling->Get a detailed csv/xls/json list of all members
- Ändra avdelningslistor på grennivå. Varje gren har en array som säger vilka avdelningar som finns på varje gren. Beroende på vilken plats (hur många "", som skrivs innan) så skrivs detta i olika kolumner i tabellen. Nedan listas vilka funktioner som dessa listor är i och som du måste ändra i. Om du t.ex inte har någon avdelning på grenen letare så tar du bort Bävrarna ur listan. Om du vill kalla denna gren för något annat som t.ex Familjescouting eller något annat så går det att ändra på annat ställe.
  - get_avdelningar_letare($lang, $empty="")
  - get_avdelningar_sparare($lang, $empty="")
  - get_avdelningar_upptackare($lang, $empty="")
  - get_avdelningar_aventyrare($lang, $empty="")
  - get_avdelningar_utmanare($lang, $empty="")
- Ändra uträkningar för kommande års scouter. Du skriver in namn på den avdelning som scouterna kommer i från och om det är flera så skriver du in flera. De flesta uträckningar funkar, så testa dig fram. Om du på grenen innan bara har en avdelning som i standardinställningen (Bävrarna) och på grenen spårare har två avdelningar så vill du antagligen fördela dessa mellan de två spåraravdelningarna. Om det är ojämnt antal så vill du antaligen kunna avrunda upp eller ner dessa på någon av avdelningarna. Skriv då + eller - som sista tecken i din formel. Tex Bävrarna/2+. Om du vill räkna in för kölistan så skriver du in W.
  - scoutnet_get_letare_calc($avdelning_index)
  - scoutnet_get_sparare_calc($avdelning_index)
  - scoutnet_get_upptackare_calc($avdelning_index)
  - scoutnet_get_aventyrare_calc( $avdelning_index)
  - scoutnet_get_utmanare_calc( $avdelning_index)
- Ändra åldersintervall på grenen och indirekt då också antalet år på grenen. Lägg till ett år på värdena.
  - get_age_gren($args)
- Använd egna grenbeteckningar. Du kan här byta namn på vad du kallar grenarna. Om du inte vill använda en viss gren tar du bort namnet på den här, men lämna kvar citationstecknen där namnet stod.
  - scoutnet_get_my_grenar()
