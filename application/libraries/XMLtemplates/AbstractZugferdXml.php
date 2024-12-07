<?php
abstract class AbstractZugferdXml
{
	protected DOMDocument $doc;
	protected DOMElement $root;
	
	public function __construct(
			protected array $params,
			protected string $currencyCode
			) {
				$this->init();
	}
	
	protected function init(): void
	{
		$this->doc = new DOMDocument('1.0', 'UTF-8');
		$this->doc->formatOutput = true;
	}
	
	public function generate(): void
	{
		$this->root = $this->createRoot();
		$this->appendSharedNodes();
		$this->appendVersionSpecificNodes();
		$this->doc->appendChild($this->root);
		$this->saveXml();
	}
	
	protected function saveXml(): void
	{
		$filename = $this->params['filename'];
		$this->doc->save(UPLOADS_FOLDER . "temp/{$filename}.xml");
	}
	
	abstract protected function createRoot(): DOMElement;
	
	abstract protected function appendVersionSpecificNodes(): void;
	
	protected function appendSharedNodes(): void
	{
		$this->root->appendChild($this->createDocumentContext());
		$this->root->appendChild($this->createHeader());
		$this->root->appendChild($this->createTradeTransaction());
	}
	
	protected function formattedDate(string $date): string
	{
		return DateTime::createFromFormat('Y-m-d', $date)?->format('Ymd') ?? '';
	}
	
	protected function formattedFloat(float $amount, int $decimals = 2): string
	{
		return number_format($amount, $decimals, '.', '');
	}
	
	abstract protected function createDocumentContext(): DOMElement;
	
	abstract protected function createHeader(): DOMElement;
	
	abstract protected function createTradeTransaction(): DOMElement;
}

