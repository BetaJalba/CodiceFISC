<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Autocomplete Names from File</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 2em;
      background: #f8f8f8;
    }
    input, select {
      width: 20vw;
      padding: 10px;
      font-size: 1em;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
  </style>
</head>
<body>
  <h2>Bel form formattato con CSS</h2>

  <form method="post" action="<?php echo $_SERVER["PHP_SELF"]?>">
    
    <input type="text" id="nameInput" name="name" maxlength="50" placeholder="Inserisci nome..." required>
    <input type="text" id="surnameInput" name="surname" maxlength="50" placeholder="Inserisci nome..." required>
    <input type="radio" id="maleInput" name="sex" value="M">
    <label for="maleInput">Maschio</label>
    <input type="radio" id="femaleInput" name="sex" value="F">
    <label for="femaleInput">Femmina</label>
    <input list="names" id="placeInput" name="placeDisplay" placeholder="Inserisci comune..." required>
    <datalist id="names"></datalist>
    <input type="hidden" id="placeToken" name="place">
    <input type="date" id="dateInput" name="birthdate" required>
    <select id="ateneoInput" name="ateneo" required>
        <option value="">--Scegli un ateneo--</option>
        <option value="PD">Università di Padova</option>
        <option value="VR">Università di Verona</option>
        <option value="VE">Università di Venezia</option>
    </select>
    <input type="text" id="numberInput" name="cap" inputmode="numeric" pattern="\d*" placeholder="Inserisci CAP..." required>
    <input type="checkbox" id="workerInput">
    <label for="workerInput">Studente lavoratore</label>
    <div id="workerText"></div>
    <input type="submit">
    <input type="reset">
  </form>

  <?php
    if ($_SERVER['REQUEST_METHOD'] != 'POST')
      return;
    
    function CalcSurname(string $surname) {
      $surname = strtoupper($surname);

      preg_match_all('/[BCDFGHJKLMNPQRSTVWXYZ]/', $surname, $cons);
      preg_match_all('/[AEIOU]/', $surname, $vocs);

      $merged = array_merge($cons[0], $vocs[0]); // usa l'indice 0 per ottenere array

      return substr(implode('', $merged) . 'XXX', 0, 3);
    }

    function CalcName(string $name) {
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
          if (($i + 1) % 2 == 0)
            $sum += $evenValues[$ch];
          else
            $sum += $oddValues[$ch];
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
      <div>
        <p id="unione-sovietica-a-quanto-pare">{$codFisc}</p>
      </div>
      
      <div>
        <input type="date" id="dob" name="dob" value={$_POST['birthdate']} readonly>
      </div><br>

      <div>
        <input type="text" id="surname" name="surname" onfocusout="cognome()" value={$_POST['surname']} readonly>
      </div>

      <div>
        <input type="text" id="name" name="name" onfocusout="nome()" value={$_POST['name']} readonly>
      </div>
      
      <div>
      <select id="gender" name="gender" readonly>
        <option value="M" selected="selected">{$_POST['sex']}</option>
        <option value="F">{$_POST['sex']}</option>
      </select>
      </div>

      <input type="button" style="width: 35%; float: right; margin-right: 1%; margin-top:1%; clear: both;" value="readonly" onclick = "">

      <div>
        <input type="text" id="comune" name="comune" value={$_POST['placeDisplay']} readonly>
        <div class="divcomunisexy" id="lista-comuni" >
        </div>
      </div>
    </form>
    HTML;
    ?>

  <script>
    let codeMap = {};

    // fetch, quello che uso per importare dati da file, restituisce una promise
    // "imparate" con una libreria in lua (e un po' di stackOverflow in questo caso) ;)
    // fetch non funziona con file:// ma solo quando hostato su server
    // motivo per cui in terza dovevamo caricare il file manualmente (che non facevo)
    fetch('media/files/lista-codici.txt')
      .then(resp => resp.text()) // ignora
      .then(data => {
        const list = document.getElementById('names');
        const lines = data.split(/\r?\n/); // regex, il ? messo dopo al \r intendesi zero o una occorrenza dell'escape, windows usa \r\n per line feed, linux usa \n
        lines.forEach(line => {
          const [name, code] = line.split(';'); 
          if (name && code) {
            let trimmedName = name.trim().toUpperCase();
            let trimmedCode = code.trim().toUpperCase();

            codeMap[name] = code;

            const option = document.createElement('option');
            
            option.value = name;
            option.innerText = name.trim().toUpperCase();

            list.appendChild(option);
          }
        });
      })
      .then(() => console.log('Codici caricati.'))
      .catch(err => console.error('AAAAAAA', err));
      

    document.getElementById('nameInput').addEventListener('input', function(e){
        this.value = this.value.replace(/[^a-zA-Z]/g, '');
    })

    document.getElementById('surnameInput').addEventListener('input', function(e){
        this.value = this.value.replace(/[^a-zA-Z]/g, '');
    })
    
    document.getElementById('numberInput').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '').slice(0, 5); // regex, questa volta toglie tutte le occorrenze di caratteri non numerici
    });

    document.getElementById('workerInput').addEventListener('change', function(e){
        const textDiv = document.getElementById('workerText');

        if (this.checked){
            const textArea = document.createElement('textArea');
            textArea.setAttribute('required', true);
            textArea.setAttribute('name', 'work')
            textDiv.appendChild(textArea);
        }
        else
            textDiv.removeChild(textDiv.firstChild);
    });

    document.getElementById('placeInput').addEventListener('input', function(e){
        this.value = this.value.replace(/[^a-zA-Z\s]/g, '');
    })

    document.getElementById('placeInput').addEventListener('blur', function(e){
        const placeToken = document.getElementById('placeToken');

        if (!codeMap[this.value]){
            this.value = '';
            placeToken.value = '';
        }
        else
            placeToken.value = codeMap[this.value];
    })
  </script>
</body>
</html>