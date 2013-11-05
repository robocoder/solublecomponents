<?php
namespace Soluble\FlexStore\Writer;
use Soluble\FlexStore\Writer\AbstractWriter;
use Soluble\FlexStore\Writer\Exception;

class CSV extends AbstractWriter {
	
	const SEPARATOR_TAB = "\t";
	const SEPARATOR_COMMA = ',';
	const SEPARATOR_SEMICOLON = ';';
	const SEPARATOR_NEWLINE_UNIX = "\n";
	const SEPARATOR_NEWLINE_WIN = "\r\n";
	
	/**
	 * @var array
	 */
	protected $options = array(
		'field_separator' => ";",
		'line_separator' => "\n",
		'enclosure' => '',
		'charset' => 'UTF-8',
		'escape' => '\\'
	);	


	/**
	 * @throws \Soluble\FlexStore\Writer\Exception\CharsetConversionException
	 * @return string csv encoded data
	 */
	function getData() {
		

		if (!function_exists('iconv')) {
			throw new Exception\RuntimeException('CSV writer requires iconv extension');
		}
		
		
		$csv = '';
		$data = $this->source->getData()->toArray();		
		
		if (count($data) == 0) {
			return $data;
		}
		
		$internal_encoding = strtoupper(iconv_get_encoding('internal_encoding'));
		$charset = strtoupper($this->options['charset']);
		$escape = $this->options['escape'];
		
		$header_line = join($this->options['field_separator'], array_keys($data[0]));
		$csv .= $header_line . $this->options['line_separator'];

		
		foreach ($data as $row) {

			
			switch ($this->options['field_separator']) {
				case self::SEPARATOR_TAB:
					array_walk($row, array($this, 'escapeTabDelimiter'));
					break;
				default:
					array_walk($row, array($this, 'escapeFieldDelimiter'));
					
			}

			array_walk($row, array($this, 'escapeLineDelimiter'));

			if ($this->options['enclosure'] != '') {
				array_walk($row, array($this, 'addEnclosure'));
			}

			$line = join($this->options['field_separator'], $row);
			
			if ($charset != $internal_encoding) {
				$l = (string) $line;
				if ($l != '') {
					$line = iconv($internal_encoding, $this->options['charset'], $l);
					if ($line === false) {
						throw new Exception\CharsetConversionException("Cannot convert the charset to " . $this->options['charset'] . ", value: '$l'.");
					}
				}
			}

			$csv .= $line . $this->options['line_separator'];
		}

		return $csv;
	}

	
	/**
	 * 
	 * @param \Soluble\FlexStore\Writer\SendHeaders $headers
	 */
	public function send(SendHeaders $headers=null) {
		if ($headers === null) $headers = new SendHeaders();
		ob_end_clean()
		//Content-Type: text/csv; name="filename.csv"
		//Content-Disposition: attachment; filename="filename.csv"		
		;
		$headers->setContentType('text/csv');
		$headers->printHeaders();
		$json = $this->getData();
		echo $json;
	}	

	/**
	 *
	 * @param string $item
	 * @param string $key 
	 */
	protected function escapeLineDelimiter(&$item, $key) {
		$item = str_replace(self::SEPARATOR_NEWLINE_WIN, " ", $item);
		$item = str_replace(self::SEPARATOR_NEWLINE_UNIX, " ", $item);
	}

	/**
	 *
	 * @param string $item
	 * @param string $key 
	 * @return string
	 */
	protected function escapeTabDelimiter(&$item, $key) {
		$item = str_replace("\t", " ", $item);
	}
	
	/**
	 *
	 * @param string $item
	 * @param string $key 
	 * @return string
	 */
	protected function escapeFieldDelimiter(&$item, $key) {
		$item = str_replace($this->options['field_separator'], $this->options['escape'] . $this->options['field_separator'], $item);
	}
	

	/**
	 *
	 * @param string $item
	 * @param string $key 
	 * @return void
	 */
	protected function addEnclosure(&$item, $key) {
		$enc = $this->options['enclosure'];
		//$item = $enc . str_replace($enc, '\\' . $enc, $item) . $enc;
		$item = $enc . str_replace($enc, '', $item) . $enc;
	}

}