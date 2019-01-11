<?php
require_once('../fatturapa.php');
$fatturapa = new FatturaPA();

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
		// Regime fiscale - https://github.com/s2software/fatturapa/wiki/Costanti#regime-fiscale (default: RF01 = ordinario)
		'regimefisc' => "RF19",
]);

// Imposta destinatario (cliente)
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
		'sdi_codice' => '1234567',		// Codice destinatario - da impostare in alternativa alla PEC
		'sdi_pec' => 'pec@test.com',	// PEC destinatario - da impostare in alternativa al Codice
		
]);

// Imposta dati intestazione fattura
$fatturapa->set_intestazione([
		// Tipo documento - https://github.com/s2software/fatturapa/wiki/Costanti#tipo-documento (default = TD01 = fattura)
		'tipodoc' => "TD01",
		// Valuta (default = EUR)
		'valuta' => "EUR",
		// Data e numero fattura
		'data' => "2019-01-07",
		'numero' => "2019/01",
]);

// Aggiungi righe dettaglio
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
			'importo' => FatturaPA::dec($impX),	// imponibile riga
			// % aliquota IVA
			'perciva' => FatturaPA::dec(22),
	]);
	$impTot += $impX;
}

// Imposta i totali
/*$iva = $impTot/100*22;
$fatturapa->set_totali([
		'importo' => FatturaPA::dec($impTot),	// imponibile totale
		'perciva' => FatturaPA::dec(22),
		'iva' => FatturaPA::dec($iva),			// calcolo iva
		'esigiva' => 'I',	// Esigibilità IVA - https://github.com/s2software/fatturapa/wiki/Costanti#esigibilit%C3%A0-iva
]);*/
$totale = $fatturapa->set_auto_totali([
		'esigiva' => 'I',	// Esigibilità IVA - https://github.com/s2software/fatturapa/wiki/Costanti#esigibilit%C3%A0-iva
]);

// Imposta dati pagamento (opzionale)
//$totale = $impTot+$iva;
$fatturapa->set_pagamento([
		// Condizioni pagamento - https://github.com/s2software/fatturapa/wiki/Costanti#condizioni-pagamento (default: TP02 = completo)
		'condizioni' => "TP02"
],
[	// Modalità (possibile più di una) https://github.com/s2software/fatturapa/wiki/Costanti#modalit%C3%A0-pagamento
		[
				'modalita' => "MP05",	// bonifico
				'totale' => FatturaPA::dec($totale),	// totale iva inclusa
				'scadenza' => "2019-02-07",
				'iban' => 'IT88A0123456789012345678901'
		],
		[
				'modalita' => "MP08",	// carta di pagamento
				'totale' => FatturaPA::dec($totale),
				'scadenza' => "2019-02-07",
		],
]
);

// Debug struttura XML in array
echo '<pre>';
print_r($fatturapa->get_node());
echo '</pre>';

// Scrive l'XML
@mkdir('Risultato');
$filename = $fatturapa->filename('00001');	// progressivo da applicare al nome file (univoco, alfanumerico, max 5 caratteri)
$xml = $fatturapa->get_xml();
$file = fopen('Risultato/'.$filename, 'w');
fwrite($file, $xml);
fclose($file);