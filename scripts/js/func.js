//l'ho fatto in 5 ore abbia pietà :( <3 NO

//lettura file no
function aggiungi(lista){
    listacomuni = lista;
    console.log("lista comuni letta e caricata!");
    //meglio non vederlo  --  console.log(listacomuni.length);
}

//funzioni di utilità NO
function removeChildren(n){
    while (n.firstChild) n.removeChild(n.lastChild);
}



//selezione comune NOOOOOOOOOOOOOO
function creascelte(){
    let lettere = document.getElementById("comune").value.toUpperCase();

    //characters
    let c = lettere.length;
    //c counter
    let cc = 0;

    let comunenome = "";
    let trovato = false;

    removeChildren(divv);

    //per velocizzare il processo di ricerca si potrebbe andare a
    //ridurre l'array con i comuni inutili pian piano si scrive
    //ma onestamente 1 non ho voglia 2 non ho tempo e 3 non voglio aggiungermi la fatica mentale
    //e poi su un Intel i7-14700k la differenza non si nota
    //contando pure che sono su FireFox che non è a base chromuium
    //e che "impossibilita" la cpu ad utilizzare tutte le sue capacità

    if (lettere != ""){
        console.log("entrato!");
            for (let i = 0; i  < listacomuni.length; i++){
                //si salva il nome del comune
                comunenome = comunenome + listacomuni.charAt(i);

                //cerca le lettere
                if (listacomuni.charAt(i) == lettere.charAt(cc)) cc++;
                else cc = 0;

                //controlla se ha trovato
                if (cc == c) trovato = true;

                //quando incontra un | si resetta
                if (listacomuni.charAt(i) == '|'){
                    cc = 0;
                    if (trovato) creabottonecomune(comunenome);
                    trovato = false;
                    comunenome = "";
                } 
            }
    }
}

// NO
function creabottonecomune(creare){
    let pcomune = true;
    let pcod = false;

    let com = "";
    let cod = "";
    
    for (let j = 0; j < creare.length; j++){
        if (creare.charAt(j) == '|') pcod = false;
        if (pcod) cod = cod + creare.charAt(j);
        if (creare.charAt(j) == ';'){
            pcomune = false;
            pcod = true;
        }
        if (pcomune) com = com + creare.charAt(j);
    }

    let input = document.createElement("input");
    input.setAttribute("type", "button");
    input.setAttribute("class", "buttondiv");
    input.setAttribute("value", "" + com);
    input.setAttribute("id", "" + cod);
    input.setAttribute("onclick", "selezionecomune(value, id)");

    divv.appendChild(input);
}

function selezionecomune(nomcom, codcom){
    removeChildren(divv);
    
    document.getElementById("comune").value = nomcom;
    codicecorrente = codcom;
    console.log("cambiato!");
}

//fine selezione comune! NOOOOO


//calcolo codice fiscale no, anzi questo mi serve
const cBanned = ['A', 'E', 'I', 'O', 'U'];


function cognome(){   
  let srn = document.getElementById("surname").value.toUpperCase();
  let srnres = "";
  
  //caso primo
  let k = 0;
  while ((srnres.length < 3) && (k < srn.length)){if (cBanned.includes(srn.charAt(k)) == false) srnres = srnres + srn.charAt(k); k++;}

  if (srnres.length < 3){
    //caso vocali
    let cvoc = 0;
    while ((srnres.length < 3) && (cvoc < srn.length)){if (cBanned.includes(srn.charAt(cvoc)) == true) srnres = srnres + srn.charAt(cvoc); cvoc++;}
  }

  if (srnres.length < 3){
    //caso x
    while ((srnres.length < 3)) srnres = srnres + 'X';
  }

  cCogn = srnres;
}

function nome(){   
    let nom = document.getElementById("name").value.toUpperCase();
    let nomres = "";
    
    let f = 0;
    let cons = 0;

    while ((nomres.length < 3) && (f < nom.length)){
        if ((cBanned.includes(nom.charAt(f)) == false) && (cons != 1)){
            nomres = nomres + nom.charAt(f);
            cons++;
        }
        else if (cons == 1) cons++;
        f++;
    }

    if (nomres.length < 3){
        //casi cognome

        nomres = "";

        //caso primo
        let k = 0;
        while ((nomres.length < 3) && (k < nom.length)){if (cBanned.includes(nom.charAt(k)) == false) nomres = nomres + nom.charAt(k); k++;}
  
        if (nomres.length < 3){
            //caso vocali
            let cnvoc = 0;
            while ((nomres.length < 3) && (cnvoc < nomres.length)){if (cBanned.includes(nom.charAt(cnvoc)) == true) nomres = nomres + nom.charAt(cnvoc); cnvoc++;}
        }
  
        if (nomres.length < 3){
            //caso x
            while ((nomres.length < 3)) nomres = nomres + 'X';
        }
    }
    cNome = nomres;
  }

//months to letters per coloro che non sanno
const mtl = ["A", "B", "C", "D", "E", "H", "L", "M", "P", "R", "S", "T"];
function dataNasci(){
    let dob = document.getElementById("dob").value;
    let dobris = ""

    const yob = dob.substring(2, 4);
    let mob = parseInt(dob.substring(5, 7));
    //giorno of birth per quelli più formali ;(
    let gob = dob.substring(8, 10);

    //calcoli magici
    let mobres = mtl[mob - 1];
    if (document.getElementById("gender").value == "F") gob += 40;

    dobris = yob + mobres + gob;
    cDoB =  dobris;
    console.log(cDoB);

    //per sicurezza
    cognome();
    nome();

    //si procede al controllo dell'errore e alla visualizzazione a schermo del codice fiscale
    completacodice();
}

const checkpari = [1, 0, 5, 7, 9, 13, 15, 17, 19, 21, 2, 4, 18, 20, 11, 3, 6, 8, 12, 14, 16, 10, 22, 25, 24, 23];

function completacodice(){
    //controllo errore
    let pcodice = document.getElementById("unione-sovietica-a-quanto-pare");

    let nomerr = false;
    let cognerr = false;
    let doberr = false;
    let coderr = false;

    if (cNome == "") nomerr = true;
    if (cCogn == "") cognerr = true;
    if (cDoB == "") doberr = true;
    if (codicecorrente == "") coderr = true;

    if ((nomerr) || (cognerr) || (doberr) || (coderr)){
        pcodice.innerHTML = "Dati insufficienti; Errori {Nome: "+ nomerr + "; Cognome: " + cognerr + "; Data of nascita: " + doberr + "; Scelta comune: " + coderr + "}";
        return;
    }

    //calcolo checkdigit
    let preCODFISC = cCogn + cNome + cDoB + codicecorrente;
    let CODsomma = 0;

    for (let i = 1; i <= preCODFISC.length; i++){
        if (i%2 == 0){
            if ((preCODFISC.charCodeAt(i-1) >= 48) && (preCODFISC.charCodeAt(i-1) <= 57)) 
                CODsomma += parseInt(preCODFISC.charCodeAt(i-1)) - 48;
            if ((preCODFISC.charCodeAt(i-1) >= 65) && (preCODFISC.charCodeAt(i-1) <= 90)) 
                CODsomma += parseInt(preCODFISC.charCodeAt(i-1)) - 65;
        }

        if (i%2 != 0){
            if ((preCODFISC.charCodeAt(i-1) >= 48) && (preCODFISC.charCodeAt(i-1) <= 57)) CODsomma += checkpari[parseInt(preCODFISC.charCodeAt(i-1)) - 48];
            if ((preCODFISC.charCodeAt(i-1) >= 65) && (preCODFISC.charCodeAt(i-1) <= 90)) CODsomma += checkpari[parseInt(preCODFISC.charCodeAt(i-1)) - 65];
        }
    }

    //divisione
    CODsomma = CODsomma % 26;

    let checkDGT = String.fromCharCode(CODsomma + 65);

    pcodice.innerHTML = preCODFISC + checkDGT;
}