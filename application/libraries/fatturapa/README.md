# FatturaPA
Libreria SDK PHP per la generazione veloce di una Fattura elettronica in XML (formato FatturaPA)

## Esempio utilizzo

### Include e inizializza classe
```php
require_once('/path/to/fatturapa.php');
$fatturapa = new FatturaPA('FPR12');	// Formato - https://git.io/fhm9g (default: FPR12 = Privati)
```
#### Costanti
- [`formato`](https://github.com/s2software/fatturapa/wiki/Costanti#formato-trasmissione) (opzionale da passare al costruttore)

### Imposta mittente (fornitore)
```php
// Imposta mittente (fornitore)
$fatturapa->set_mittente([
  // Dati azienda emittente fattura
  'ragsoc' => "La Mia Ditta Srl",
  'indirizzo' => "Via Italia 12",
  'cap' => "00100",
  'comune' => "Roma",
  'prov' => "RM",
  'paese' => "IT",
  'piva' => '01234567890',
  // Regime fiscale - https://git.io/fhmMd (default: RF01 = ordinario)
  'regimefisc' => "RF19",
]);
```
#### Costanti
- [`regimefisc`](https://github.com/s2software/fatturapa/wiki/Costanti#regime-fiscale)

### Imposta destinatario (cliente)
```php
$fatturapa->set_destinatario([
  // Dati cliente destinatario fattura
  'ragsoc' => "Il Mio Cliente Spa",
  'indirizzo' => "Via Roma 24",
  'cap' => "20121",
  'comune' => "Milano",
  'prov' => "MI",
  'paese' => "IT",
  'piva' => '12345678901',
  // Dati SdI (Sistema di Interscambio) del destinatario/cliente
  'sdi_codice' => '1234567',    // Codice destinatario - da impostare in alternativa alla PEC
  'sdi_pec' => 'pec@test.com',  // PEC destinatario - da impostare in alternativa al Codice		
]);
```

### Imposta altri dati intestazione fattura
```php
// Imposta dati intestazione fattura
$fatturapa->set_intestazione([
  // Tipo documento - https://git.io/fhmMb (default = TD01 = fattura)
  'tipodoc' => "TD01",
  // Valuta (default = EUR)
  'valuta' => "EUR",
  // Data e numero fattura
  'data' => "2019-01-07",
  'numero' => "2019/01",
]);
```
#### Costanti
- [`tipodoc`](https://github.com/s2software/fatturapa/wiki/Costanti#tipo-documento)

### Composizione righe dettaglio
```php
$imp[1] = 1200;
$imp[2] = 300;
$impTot = 0;
foreach ($imp as $n => $impX)
{
  $fatturapa->add_riga([
    // Numero progressivo riga dettaglio
    'num' => $n,
    // Descrizione prodotto/servizio
    'descrizione' => "Realizzazione sito internet $n",
    // Prezzo unitario del prodotto/servizio
    'prezzo' => FatturaPA::dec($impX),
    // Quantità
    'qta' => FatturaPA::dec(1),
    // Prezzo totale (prezzo x qta)
    'importo' => FatturaPA::dec($impX), // imponibile riga
    // % aliquota IVA
    'perciva' => FatturaPA::dec(22),
  ]);
  $impTot += $impX;
}
```

### Impostazione totali
```php
$iva = $impTot/100*22;
$fatturapa->set_totali([
  'importo' => FatturaPA::dec($impTot), // imponibile totale
  'perciva' => FatturaPA::dec(22),
  'iva' => FatturaPA::dec($iva), // calcolo iva
  'esigiva' => 'I',              // Esigibilità IVA - https://git.io/fhmDq
]);
```
#### Nota
In caso di più aliquote IVA, è necessario impostare più totali raggruppando per aliquota: passare un array multiplo alla `set_totali` o utilizzare la `add_totali`.
#### Costanti
- [`esigiva`](https://github.com/s2software/fatturapa/wiki/Costanti#esigibilit%C3%A0-iva)

### Imposta dati pagamento (opzionale)
```php
// Imposta dati pagamento (opzionale)
$fatturapa->set_pagamento([
  // Condizioni pagamento - https://git.io/fhmD8 (default: TP02 = completo)
  'condizioni' => "TP02"
],
[ // Modalità (possibile più di una) https://git.io/fhmDu
  [
    'modalita' => "MP05",	// bonifico
    'totale' => FatturaPA::dec($impTot+$iva),	// totale iva inclusa
    'scadenza' => "2019-02-07",
    'iban' => 'IT88A0123456789012345678901'
  ],
  [
    'modalita' => "MP08",	// carta di pagamento
    'totale' => FatturaPA::dec($impTot+$iva),
    'scadenza' => "2019-02-07",
  ],
]
);
```
#### Costanti
- [`condizioni`](https://github.com/s2software/fatturapa/wiki/Costanti#condizioni-pagamento)
- [`modalita`](https://github.com/s2software/fatturapa/wiki/Costanti#modalit%C3%A0-pagamento)

### Aggiunta libera di altri nodi nell'XML FatturaPA
È possibile impostare/aggiungere ulteriori nodi nell'XML, rispettando le specifiche del formato [FatturaPA](https://www.fatturapa.gov.it/export/fatturazione/it/normativa/f-2.htm).
```php
// Impostazione libera nodo singolo
$fatturapa->set_node('FatturaElettronicaHeader/CedentePrestatore/Contatti/Telefono', '+39123456789');
// Aggiunta libera a un elenco (più nodi con lo stesso nome)
$fatturapa->add_node('FatturaElettronicaBody/DatiGenerali/DatiDDT', ['NumeroDDT' => '1', 'DataDDT' => '2019-01-07']);
$fatturapa->add_node('FatturaElettronicaBody/DatiGenerali/DatiDDT', ['NumeroDDT' => '2', 'DataDDT' => '2019-01-10']);
```

### Genera e salva l'XML
```php
@mkdir('Risultato');
$filename = $fatturapa->filename('00001'); // progressivo da applicare al nome file (univoco, alfanumerico, max 5 caratteri)
$xml = $fatturapa->get_xml();
$file = fopen('Risultato/'.$filename, 'w');
fwrite($file, $xml);
fclose($file);
```
