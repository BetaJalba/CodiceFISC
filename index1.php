<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="scripts/css/styles.css">

  <script type="text/javascript" src="media/files/lista-codici.js"></script>
  

  <title><s>Calcolatore</s>NO Codice Fiscale</title>
  <!-- this one's sanity must've already crumbled -->
</head>
<body onload="logsad()">
  <header>
        <h1>Calcolatore Codice Fiscale</h1>
  </header>
  <div class="container">  
    <?php
    function CalcSurname(string $surname) {
      $surname = strtoupper($surname);

      preg_match_all('/[BCDFGHJKLMNPQRSTVWXYZ]/', $surname, $cons);
      preg_match_all('/[AEIOU]/', $surname, $vocs);

      $merged = array_merge($cons[0], $vocs[0]); // usa l'indice 0 per ottenere array piatto

      return substr(implode('', $merged) . 'XXX', 0, 3);
    }

    function CalcName(string $name): string {
      $name = strtoupper($name);

      // stessa cosa per nome
      preg_match_all('/[BCDFGHJKLMNPQRSTVWXYZ]/', $name, $cons);
      preg_match_all('/[AEIOU]/', $name, $vocs);

      $cons = $cons[0];
      $vocs = $vocs[0];

      // caso 4 consonanti
      if (count($cons) >= 4) {
          $merged = [$cons[0], $cons[2], $cons[3]];
      } else {
          $merged = array_merge($cons, $vocs);
      }

      return substr(implode('', $merged) . 'XXX', 0, 3);
    }

    function CalcBirthdate(string $birthdate, string $sex) {
      // YYYY-MM-DD
      [$year, $month, $day] = explode('-', $birthdate);
      
      // per prendere le ultime 2 cifre
      $year = substr($year, -2);

      $monthMap = [
          1 => 'A', 
          2 => 'B', 
          3 => 'C', 
          4 => 'D', 
          5 => 'E', 
          6 => 'H',
          7 => 'L', 
          8 => 'M', 
          9 => 'P', 
          10 => 'R', 
          11 => 'S', 
          12 => 'T'
      ];

      $letter = $monthMap[(int)$month];

      // calcoli magici (riporto commento del 2023 boh)
      $day = (int)$day;
      if (strtoupper($sex) === 'F') {
          $day += 40;
      }

      // string print formatted, https://www.w3schools.com/php/func_string_sprintf.asp
      return sprintf("%02d%s%02d", $year, $letter, $day);
    }

    function CalcChecksum(string $partial): string {
      $oddValues = [
          '0'=>1, '1'=>0, '2'=>5, '3'=>7, '4'=>9, '5'=>13, '6'=>15, '7'=>17, '8'=>19, '9'=>21,
          'A'=>1, 'B'=>0, 'C'=>5, 'D'=>7, 'E'=>9, 'F'=>13, 'G'=>15, 'H'=>17, 'I'=>19, 'J'=>21,
          'K'=>2, 'L'=>4, 'M'=>18, 'N'=>20, 'O'=>11, 'P'=>3, 'Q'=>6, 'R'=>8, 'S'=>12, 'T'=>14,
          'U'=>16, 'V'=>10, 'W'=>22, 'X'=>25, 'Y'=>24, 'Z'=>23
      ];

      $evenValues = [
          '0'=>0, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6, '7'=>7, '8'=>8, '9'=>9,
          'A'=>0, 'B'=>1, 'C'=>2, 'D'=>3, 'E'=>4, 'F'=>5, 'G'=>6, 'H'=>7, 'I'=>8, 'J'=>9,
          'K'=>10, 'L'=>11, 'M'=>12, 'N'=>13, 'O'=>14, 'P'=>15, 'Q'=>16, 'R'=>17, 'S'=>18, 'T'=>19,
          'U'=>20, 'V'=>21, 'W'=>22, 'X'=>23, 'Y'=>24, 'Z'=>25
      ];

      $sum = 0;
      $partial = strtoupper($partial);

      for ($i = 0; $i < strlen($partial); $i++) {
          $ch = $partial[$i];
          if (($i + 1) % 2 == 0) {
              // posizione pari
              $sum += $evenValues[$ch];
          } else {
              // posizione dispari
              $sum += $oddValues[$ch];
          }
      }

      $remainder = $sum % 26;
      $controlChar = chr($remainder + 65);

      return $partial . $controlChar;
    }

    // calcolo codice
    $codFisc =  CalcSurname($_POST['surname'])
                . CalcName($_POST['name'])
                . CalcBirthdate($_POST['birthdate'], $_POST['sex'])
                . $_POST['place'];
    $codFisc = CalcChecksum($codFisc);

    echo <<<HTML
    <form>
      <div style="max-width: 40%; max-height: 40px; overflow:scroll ;margin: -5px; display: block; margin-bottom: -25.5%; margin-top: 17%; margin-left: 15%; padding: 0;">
        <p id="unione-sovietica-a-quanto-pare">{$codFisc}</p>
      </div>
      
      <div style="width: 13%; min-width: 130px; display: inline-block; margin-left: 62%; margin-top: 25.5%">
        <input type="date" id="dob" name="dob" value={$_POST['birthdate']} readonly>
      </div><br>

      <div style="width: 40%; display: block; margin-left: 15%; margin-top: -10%; padding: -15px;">
        <input type="text" id="surname" name="surname" onfocusout="cognome()" value={$_POST['surname']} readonly>
      </div>

      <div style="width: 40%; display: inline-block; margin-left: 15%;">
        <input type="text" id="name" name="name" onfocusout="nome()" value={$_POST['name']} readonly>
      </div>
      
      <div style="width: 13%; display: inline-block; margin-left: 30%;">
      <select id="gender" name="gender" readonly>
        <option value="M" selected="selected">{$_POST['sex']}</option>
        <option value="F">{$_POST['sex']}</option>
      </select>
      </div>

      <input type="button" style="width: 35%; float: right; margin-right: 1%; margin-top:1%; clear: both;" value="readonly" onclick = "">

      <div style="width: 40%; margin: -8%  15%; display: inline-block;">
        <input type="text" id="comune" name="comune" value={$_POST['placeDisplay']} readonly>
        <div class="divcomunisexy" id="lista-comuni" >
        </div>
      </div>
    </form>
    HTML;
    ?>
  </div>
</body>
</html>