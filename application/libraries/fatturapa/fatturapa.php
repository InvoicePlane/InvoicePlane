<?php
/**
 * PHP FatturaPA
 * @author Storti Stefano
 * @copyright	Copyright (c) 2019, S2 Software di Storti Stefano
 * @license	http://opensource.org/licenses/MIT	MIT License
 * @link https://github.com/s2software/fatturapa
 */
class FatturaPA {
	
	const VERSION = '0.1.0';
	protected $_node = ['FatturaElettronicaHeader' => [], 'FatturaElettronicaBody' => []];
	protected $_schema = [];	// schema .xsd (nella generazione dell'XML va rispettato anche l'ordine dei nodi)
	
	/**
	 * Imposta il formato (utilizzare constanti definite in FatturaPA_Formato)
	 * https://github.com/s2software/fatturapa/wiki/Costanti#formato-trasmissione
	 * @param string $formato (default: FPR12 = Privati)
	 */
	public function __construct($formato = 'FPR12')
	{
		$this->_set_node('FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione', $formato);
		$this->_schema = $this->_build_schema();
	}
	
	/**
	 * Imposta dati trasmittente (es.: azienda o commercialista) (opzionale: copia dati mittente)
	 * @param array $data
	 */
	public function set_trasmiettente($data)
	{
		$map = array(
				'paese' => 'FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese',
				'piva' => 'FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice',
		);
		$this->_fill_node($map, $data);
	}
	
	/**
	 * Imposta il mittente/fornitore della fattura
	 * @param array $data
	 * - piva_paese: opzionale (copia dato paese)
	 * - regimefisc: https://github.com/s2software/fatturapa/wiki/Costanti#regime-fiscale
	 */
	public function set_mittente($data)
	{
		$node = &$this->_set_node('FatturaElettronicaHeader/CedentePrestatore', []);
		$this->_set_anagr($data, $node);
		
		// default mittente
		$this->_set_defaults([
				// trasmiettente - default: copia dati del mittente
				'FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese' =>
					$this->_get_node('FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdPaese'),
				'FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice' =>
					$this->_get_node('FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/IdFiscaleIVA/IdCodice'),
				// regimefisc è opzionale: default: RF01 = ordinario
				'FatturaElettronicaHeader/CedentePrestatore/DatiAnagrafici/RegimeFiscale' =>
					'RF01',
		]);
	}
	
	/**
	 * Imposta il destinatario/cliente della fattura
	 * @param array $data
	 * - piva_paese: opzionale (copia dato paese)
	 * - sdi_codice / sdi_pec sono alternativi
	 */
	public function set_destinatario($data)
	{
		$node = &$this->_set_node('FatturaElettronicaHeader/CessionarioCommittente', []);
		$this->_set_anagr($data, $node);
		
		$map = array(
				'sdi_codice' => 'FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario',
				'sdi_pec' => 'FatturaElettronicaHeader/DatiTrasmissione/PECDestinatario',
		);
		$this->_fill_node($map, $data);
		
		// default destinatario
		$this->_set_defaults([
				// set_destinatario > sdi_codice - default: 0000000
				'FatturaElettronicaHeader/DatiTrasmissione/CodiceDestinatario' =>
					'0000000',
		]);
	}
	
	/**
	 * 
	 * @param array $data
	 * @param string $type ('mittente'/'destinatario')
	 */
	protected function _set_anagr($data, &$node)
	{
		$map = array(
				'piva_paese' => 'DatiAnagrafici/IdFiscaleIVA/IdPaese',
				'piva' => 'DatiAnagrafici/IdFiscaleIVA/IdCodice',
				'codfisc' => 'DatiAnagrafici/CodiceFiscale',
				'ragsoc' => 'DatiAnagrafici/Anagrafica/Denominazione',
				'regimefisc' => 'DatiAnagrafici/RegimeFiscale',
				'indirizzo' => 'Sede/Indirizzo',
				'cap' => 'Sede/CAP',
				'comune' => 'Sede/Comune',
				'prov' => 'Sede/Provincia',
				'paese' => 'Sede/Nazione',
		);
		$this->_fill_node($map, $data, $node);
		
		// se è una partita iva
		if (isset($data['piva']))
		{
			$this->_set_defaults([
					// paese p.iva: default impostato quello della sede
					'DatiAnagrafici/IdFiscaleIVA/IdPaese' =>
						$this->_get_node('Sede/Nazione', $node)
			], $node);
		}
	}
	
	/**
	 * Imposta i dati di intestazione della fattura
	 * @param array $data
	 * - tipodoc: https://github.com/s2software/fatturapa/wiki/Costanti#tipo-documento (default: TD01 = Fattura)
	 * - progressivo: impostato di default allo stesso valore del campo 'numero'
	 * - causale: opzionale (max 200 caratteri)
	 */
	public function set_intestazione($data)
	{
		$map = array(
				'tipodoc' => 'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/TipoDocumento',
				'valuta' => 'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa',
				'data' => 'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Data',
				'numero' => 'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero',
				'progressivo' => 'FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio',
				'causale' => 'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Causale',
		);
		$this->_fill_node($map, $data);
		
		// imposta default
		$this->_set_defaults([
				// tipodoc - default: TD01 = Fattura
				'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/TipoDocumento' =>
					'TD01',
				// valuta - default: EUR
				'FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Divisa' =>
					'EUR',
				// progressivo - default: copia numero
				'FatturaElettronicaHeader/DatiTrasmissione/ProgressivoInvio' =>
					$this->_get_node('FatturaElettronicaBody/DatiGenerali/DatiGeneraliDocumento/Numero'),
		]);
	}
	
	/**
	 * Aggiunge una riga di dettaglio
	 * @param array $data
	 */
	public function add_riga($data)
	{
		$path = 'FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee';
		$map = array(
				'num' => 'NumeroLinea',
				'descrizione' => 'Descrizione',
				'prezzo' => 'PrezzoUnitario',
				'qta' => 'Quantita',
				'importo' => 'PrezzoTotale',
				'perciva' => 'AliquotaIVA',
		);
		$node = [];
		$this->_fill_node($map, $data, $node);
		$this->_add_node($path, $node);
	}
	
	/**
	 * Imposta i dati relativi al totale fattura
	 * @param array $data Possibilità di aggiungere più totali di riepilogo raggruppando per aliquote IVA diverse
	 * - esigiva: https://github.com/s2software/fatturapa/wiki/Costanti#esigibilita-iva
	 */
	public function set_totali($data)
	{
		if ($this->_is_assoc($data))
		{
			$this->add_totali($data);
		}
		else	// più aliquote iva
		{
			foreach ($data as $data1)
			{
				$this->add_totali($data1);
			}
		}
	}
	
	/**
	 * Aggiunge un nodo di riepilogo (nel caso di più aliquote iva vanno aggiunti più nodi di riepilogo raggruppando per aliquota IVA)
	 * @param array $data
	 * - esigiva: https://github.com/s2software/fatturapa/wiki/Costanti#esigibilita-iva
	 */
	public function add_totali($data)
	{
		$path = 'FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo';
		$map = array(
				'importo' => 'ImponibileImporto',
				'perciva' => 'AliquotaIVA',
				'iva' => 'Imposta',
				'esigiva' => 'EsigibilitaIVA',
		);
		$node = [];
		$this->_fill_node($map, $data, $node);
		$this->_add_node($path, $node);
	}
	
	/**
	 * Genera automaticamente i totali raggruppati per aliquota IVA, ritorna il totale da pagare IVA inclusa
	 * @param array $merge Merge campi calcolati con questi campi aggiuntivi
	 * - esigiva: https://github.com/s2software/fatturapa/wiki/Costanti#esigibilita-iva
	 * @return number
	 */
	public function set_auto_totali($merge)
	{
		// reset eventuali totali già impostati
		$node = &$this->_set_node('FatturaElettronicaBody/DatiBeniServizi/DatiRiepilogo', []);
		// raggruppo
		$righe = $this->_get_node('FatturaElettronicaBody/DatiBeniServizi/DettaglioLinee');
		$sommeImporti = [];	// somme importi suddivisi per aliquota iva
		if ($righe)
		{
			foreach ($righe as $riga)
			{
				$perciva = isset($riga['AliquotaIVA']) ? $riga['AliquotaIVA'] : 0;
				$importo = isset($riga['PrezzoTotale']) ? $riga['PrezzoTotale'] : 0;
				if (!isset($sommeImporti[$perciva]))
				{
					$sommeImporti[$perciva] = 0;
				}
				$sommeImporti[$perciva] += $importo;
			}
		}
		// aggiungo un gruppo di totale per ogni diversa aliquota IVA
		$totale = 0;
		foreach ($sommeImporti as $perciva => $sommaImporto)
		{
			$iva = round($sommaImporto * $perciva / 100, 2);	// è qui che l'iva va arrotondata
			$this->add_totali(array_merge([
					'perciva' => FatturaPA::dec($perciva),
					'importo' => FatturaPA::dec($sommaImporto),	// imponibile totale
					'iva' => FatturaPA::dec($iva),			// calcolo iva
			], $merge));
			$totale += $sommaImporto + $iva;
		}
		// ritorna il totale fattura iva inclusa
		return $totale;
	}
	
	/**
	 * Imposta dati pagamento (opzionale)
	 * @param array $data
	 * @param array $mods Modalità (possibile più di una)
	 * - condizioni: https://github.com/s2software/fatturapa/wiki/Costanti#condizioni-pagamento (default: TP02 = completo)
	 * - $modes - modalita:  https://github.com/s2software/fatturapa/wiki/Costanti#modalit%C3%A0-pagamento
	 */
	public function set_pagamento($data, $modes)
	{
		$map = array(
				'condizioni' => 'FatturaElettronicaBody/DatiPagamento/CondizioniPagamento'
		);
		$this->_fill_node($map, $data);
		
		$path = 'FatturaElettronicaBody/DatiPagamento/DettaglioPagamento';
		$map = array(
				'modalita' => 'ModalitaPagamento',
				'totale' => 'ImportoPagamento',
				'scadenza' => 'DataScadenzaPagamento',
				'iban' => 'IBAN',
		);
		if ($this->_is_assoc($modes))	// assoc array to array of assoc array
		{
			$modes = [$modes];
		}
		foreach ($modes as $mode)
		{
			$node = [];
			$this->_fill_node($map, $mode, $node);
			$this->_add_node($path, $node);
		}
	}
	
	/**
	 * Imposta liberamente valore nodo dall'esterno
	 * @param string $path
	 * @param mixed $data
	 * @return mixed
	 */
	public function &set_node($path, $value)
	{
		return $this->_set_node($path, $value);
	}
	
	/**
	 * Aggiunge liberamento nodo dall'esterno
	 * @param string $path
	 * @param mixed $data
	 * @return mixed
	 */
	public function &add_node($path, $data)
	{
		return $this->_add_node($path, $data);
	}
	
	/**
	 * Ottiene nodo dall'esterno
	 * @param string $path
	 * @return mixed
	 */
	public function &get_node($path = '')
	{
		if (!$path)
			return $this->_node;
		return $this->_get_node($path);
	}
	
	/**
	 * Ottiene l'XML completo della fattura elettronica
	 */
	public function get_xml()
	{
		$formato = $this->get_node('FatturaElettronicaHeader/DatiTrasmissione/FormatoTrasmissione');
		
		$xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".
				'<p:FatturaElettronica versione="'.$formato.'" xmlns:ds="http://www.w3.org/2000/09/xmldsig#"'."\n".
				'xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2"'."\n".
				'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'."\n".
				'xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2'.
				' http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd">'."\n";
		
		$xml .= $this->_to_xml();
		
		$xml .= '</p:FatturaElettronica>';
		return $xml;
	}
	
	/**
	 * Produce XML da applicare nel template base
	 */
	public function to_xml()
	{
		return $this->_to_xml();
	}
	
	/**
	 * Nome file da generare in base ai dati passati (senza estensione xml)
	 * https://www.fatturapa.gov.it/export/fatturazione/it/c-11.htm
	 * @param string $progr Solo alfanumerici, massimo 5 caratteri
	 * @return string
	 */
	public function filename($progr)
	{
		$paese = $this->_get_node('FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdPaese');
		$piva = $this->_get_node('FatturaElettronicaHeader/DatiTrasmissione/IdTrasmittente/IdCodice');
		return $paese.$piva.'_'.$progr.'.xml';
	}
	
	/**
	 * Schema complexType XML FatturaPA
	 */
	public function get_schema()
	{
		return $this->_schema;
	}
	
	/**
	 * Schema XML FatturaPA (utilizza .xsd ufficiale)
	 * Serve in quanto nella generazione della fattura va rispettato anche l'ordine dei nodi!
	 * Ritorna un array ['FatturaElettronicaType' => [{name => 'FatturaElettronicaHeader', type => 'FatturaElettronicaHeaderType'}, ...], ...]
	 * https://www.fatturapa.gov.it/export/fatturazione/it/normativa/f-2.htm
	 */
	protected function _build_schema()
	{
		// https://www.phpflow.com/php/how-to-convert-xsd-into-array-using-php/
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = true;
		$doc->load(self::_filepath('schema/FatturaPA_1.2.1.xsd'));
		if (!file_exists(self::_filepath('schema/schema.xml')))
		{
			$doc->save(self::_filepath('schema/schema.xml'));
		}
		$xmlfile = file_get_contents(self::_filepath('schema/schema.xml'));
		$parseObj = str_replace($doc->lastChild->prefix.':',"",$xmlfile);
		$obj = simplexml_load_string($parseObj);
		$json = json_encode($obj);
		$data = json_decode($json, TRUE);
		// debug: visualizza albero
		/*echo "<pre>";
		print_r($data);
		exit();*/
		
		$node_types = [];
		// loop <xs:complexType (nodi con sottonodi)
		foreach ($data['complexType'] as $complexType)
		{
			if (!empty($complexType['@attributes']['name']))	// es.: <xs:complexType name="FatturaElettronicaType">
			{
				$type = $complexType['@attributes']['name'];
				$node_types[$type] = [];
				if (!empty($complexType['sequence']))			// <xs:sequence>
				{
					$elements = $this->_build_schema_find_elements($complexType['sequence']);
					foreach ($elements as $element)
					{
						if (!empty($element['@attributes']['name']))	// es.: <xs:element name="DatiTrasmissione" type="DatiTrasmissioneType"/>
						{
							$obj = (object)$element['@attributes'];		// ->name, ->type, (->minOccurs), (->maxOccurs)
							$node_types[$type][] = $obj;
						}
					}
				}
			}
		}
		return $node_types;
	}
	
	/**
	 * Trova tutti gli <xs:element> nello stesso ordine in cui vengono trovati nello schema
	 * (ricorsivo necessario ad esempio nel caso del nodo <xs:complexType name="AnagraficaType">)
	 * @param array $sequence
	 * @return array
	 */
	protected function _build_schema_find_elements($sequence)
	{
		$elements = [];
		foreach ($sequence as $key => $data)
		{
			if ($key == 'element')
			{
				if (is_array($data))
				{
					$elements = array_merge($elements, $data);
				}
				else
				{
					$elements[] = $data;
				}
			}
			elseif (is_array($data))
			{
				$elements = array_merge($elements, $this->_build_schema_find_elements($data));
			}
		}
		return $elements;
	}
	
	/**
	 * Produce XML
	 * @param array $node
	 * @param string $xml
	 * @param string $node_type Devo utilizzare lo schema dei complexType presenti nello schema XSD per mantenere lo stesso ordine dei nodi
	 */
	protected function _to_xml($node = NULL, $level = 1, $node_type = 'FatturaElettronicaType')
	{
		if ($node === NULL)
			$node = $this->_node;
		
		$xml = '';
		foreach ($this->_schema[$node_type] as $schema_child)
		{
			//$child_path = trim($node_path.'/'.$schema_child->name, '/');	// es.: FatturaElettronicaHeader / es.: FatturaElettronicaHeader/DatiTrasmissione
			$name = $schema_child->name;
			$type = $schema_child->type;
			$child = &$this->_get_node($name, $node);	// cerca nodo corrispondente nell'albero di questa fattura
			if ($child !== NULL)	// nell'albero ho questo nodo
			{
				$nodes = [$child];	// un solo nodo con lo stesso nome
				if (is_array($child) && !$this->_is_assoc($child))	// più nodi con lo stesso nome (es.: DettaglioLinee)
				{
					$nodes = $child;
				}
				$pad = str_repeat('  ', $level);
				foreach ($nodes as $i => $sub)	// loop in caso di nodi multipli con lo stesso nome, altrimenti fa solo un giro
				{
					$xml .= $pad."<{$name}>";
					if (is_array($sub))	// l'albero prosegue
					{
						$xml .= "\n";
						$xml .= $this->_to_xml($sub, $level+1, $type);
						$xml .= $pad;
					}
					else	// è un nodo finale: qui ho il valore
					{
						$xml .= htmlspecialchars($sub);
						
					}
					$xml .= "</{$name}>"."\n";
				}
			}
		}
		
		/*foreach ($node as $name => $child)
		{
			$nodes = [$child];
			if (is_array($child) && !$this->_is_assoc($child))	// più nodi con lo stesso nome (es.: DettaglioLinee)
			{
				$nodes = $child;
			}
			$pad = str_repeat('  ', $level);
			foreach ($nodes as $i => $sub)
			{
				$xml .= $pad."<{$name}>";
				if (is_array($sub))	// l'albero prosegue
				{
					$xml .= "\n";
					$xml .= $this->_to_xml($sub, $level+1);
					$xml .= $pad;
				}
				else	// è un nodo finale: qui ho il valore
				{
					$xml .= htmlspecialchars($sub);
					
				}
				$xml .= "</{$name}>"."\n";
			}
		}*/
		
		
		return $xml;
	}
	
	/**
	 * Applica default
	 * @param array $defaults
	 */
	protected function _set_defaults($defaults, &$node = NULL)
	{
		if ($node === NULL)
			$node = &$this->_node;
		
		foreach ($defaults as $path => $value)
		{
			if ($this->_get_node($path, $node) === NULL)	// se il nodo non è impostato
			{
				$this->_set_node($path, $value, $node);
			}
		}
	}
	
	/**
	 * Compila la struttura con $data guardando la $map
	 * @param array $map
	 * @param array $data
	 * @param array $node (optional)
	 * @return mixed
	 */
	protected function &_fill_node($map, $data, &$node = NULL)
	{
		if ($node === NULL)
			$node = &$this->_node;
		
		foreach ($map as $field => $path)
		{
			if (isset($data[$field]))
			{
				$this->_set_node($path, $data[$field], $node);
			}
		}
		return $node;
	}
	
	/**
	 * Aggiunge nodo alla struttura
	 * @param mixed $path Percorso nodo
	 * @param mixed $value Dato da scrivere nel nodo
	 * @param array $node Base array nodi
	 * @return mixed
	 */
	protected function &_set_node($path, $value, &$node = NULL)
	{
		if (!$path)
			return;
		
		if (!is_array($path))
			$path = explode('/', $path);
		
		if ($node === NULL)
			$node = &$this->_node;
		
		$null = NULL;
		$name = $path[0];	// nodo da inizializzare se non presente
		$child_path = array_slice($path, 1);	// percorso successivo
		if ($child_path)	// se il percorso continua, richiama ricorsivamente questa funzione
		{
			if (!isset($node[$name]))	// inizializza il nodo se non esiste
				$node[$name] = [];
			return $this->_set_node($child_path, $value, $node[$name]);
		}
		else	// altrimenti, se sono arrivato al nodo finale, scrivo il valore
		{
			$node[$name] = $value;
			return $node[$name];
		}
		return $null;
	}
	
	/**
	 * Ritorna nodo dalla struttura
	 * @param mixed $path
	 * @param array $node Base array nodi
	 * @return mixed
	 */
	protected function &_get_node($path, &$node = NULL)
	{
		if (!$path)
			return NULL;
		
		if (!is_array($path))
			$path = explode('/', $path);
		
		if ($node === NULL)
			$node = &$this->_node;
		
		$null = NULL;
		$name = $path[0];
		$child_path = array_slice($path, 1);
		if (isset($node[$name]))
		{
			if ($child_path)	// se il percorso continua, richiama ricorsivamente questa funzione
			{
				return $this->_get_node($child_path, $node[$name]);
			}
			else
			{
				return $node[$name];
			}
		}
		return $null;
	}
	
	/**
	 * Aggiunge nodo (caso più nodi con lo stesso nome)
	 * @param string $path
	 * @param mixed $data
	 * @return mixed
	 */
	protected function &_add_node($path, $data)
	{
		$node = &$this->_get_node($path);
		if (!$node)
		{
			$node = &$this->_set_node($path, []);
		}
		$node[] = $data;
		return $node[count($node)-1];
	}
	
	/**
	 * Is associative array?
	 * @param array $arr
	 * @return boolean
	 */
	protected function _is_assoc($arr)
	{
		return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	/**
	 * Path assoluto
	 * @param string $path Relativo alla posizione di questo script
	 */
	static protected function _filepath($path)
	{
		$path = str_replace('/', DIRECTORY_SEPARATOR, $path);
		return __DIR__.DIRECTORY_SEPARATOR.$path;
	}
	
	/**
	 * Format decimal
	 * @param float $value
	 */
	static function dec($value)
	{
		return number_format($value, 2, '.', '');
	}
}