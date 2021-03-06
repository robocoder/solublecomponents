<?php

namespace Soluble\FlexStore\Writer\Excel;

use Soluble\FlexStore\Writer\AbstractSendableWriter;
use Soluble\FlexStore\Writer\Exception;
use Soluble\Spreadsheet\Library\LibXL;
use Soluble\FlexStore\Writer\Http\SimpleHeaders;
use Soluble\FlexStore\Column\ColumnType;
use Soluble\FlexStore\Options;
use Soluble\FlexStore\StoreInterface;
use ExcelBook;
use ExcelFormat;
use ArrayObject;

class LibXLWriter extends AbstractSendableWriter
{
    /**
     * Cache for currency formats
     * @var ArrayObject
     */
    protected $currency_formats;

    /**
     * Cache for unit formats
     * @var ArrayObject
     */
    protected $unit_formats;
    
    /**
     *
     * @var SimpleHeaders
     */
    protected $headers;
    protected $column_width_multiplier = 1.7;
    protected $column_max_width = 75;

    /**
     *
     * @var array
     */
    protected static $default_license;

    /**
     *
     * @var ExcelBook
     */
    protected $excelBook;

    /**
     *
     * @var string
     */
    protected $file_format = LibXL::FILE_FORMAT_XLSX;

    /**
     *
     * @var array
     */
    protected $currencyMap = array(
        'EUR' => '€',
        'GBP' => '£',
        'CNY' => 'CN¥',
        'USD' => '$',
        'CAD' => 'CA$'
    );

    /**
     *
     * @var array
     */
    protected $typeMap = array(
        ColumnType::TYPE_BIT => 'number',
        ColumnType::TYPE_BLOB => 'text',
        ColumnType::TYPE_BOOLEAN => 'number',
        ColumnType::TYPE_DATE => 'date',
        ColumnType::TYPE_DATETIME => 'datetime',
        ColumnType::TYPE_DECIMAL => 'number',
        ColumnType::TYPE_INTEGER => 'number',
        ColumnType::TYPE_STRING => 'text',
        ColumnType::TYPE_TIME => 'text',
    );

    /**
     *
     * @param StoreInterface|null $store
     * @param array|\Traversable|null $options
     */
    public function __construct(StoreInterface $store = null, $options = null)
    {
        $this->currency_formats = new ArrayObject();
        $this->unit_formats = new ArrayObject();
        parent::__construct($store, $options);
    }

    /**
     * Set file format (xls, xlsx), default is xlsx
     *
     * @param string $file_format
     * @return LibXLWriter
     * @throws Exception\InvalidArgumentException
     */
    public function setFormat($file_format)
    {
        if (!LibXL::isSupportedFormat($file_format)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . " Unsupported format given '$file_format'");
        }
        $this->file_format = $file_format;
        return $this;
    }


    /**
     *
     * @param ExcelBook $book
     * @param string $currency
     * @param int $decimals
     * @return ExcelFormat
     */
    protected function getCurrencyFormat(ExcelBook $book, $currency, $decimals)
    {
        $id = "$currency/$decimals";
        if (!$this->currency_formats->offsetExists($id)) {
            if (array_key_exists($currency, $this->currencyMap)) {
                $symbol = $this->currencyMap[$currency];
            } else {
                $symbol = $currency;
            }
            
            
            $formatString = '#,##0';

            if ($decimals > 0) {
                $zeros = str_repeat("0", $decimals);
                $formatString = $formatString . '.' . $zeros;
            }
            $formatString = $formatString . ' "' . $symbol . '"_-';
            //$formatString = $formatString . ' [$€-80C]_-';
            //$format_code = '"C$"' . $format_code . '_-';

            $cfid = $book->addCustomFormat($formatString);
            $format = $book->addFormat();
            $format->numberFormat($cfid);
            $this->currency_formats->offsetSet($id, $format);
        }
        return $this->currency_formats->offsetGet($id);
    }
    


    /**
     *
     * @param ExcelBook $book
     * @param string $unit
     * @param int $decimals
     * @return ExcelFormat
     */
    protected function getUnitFormat(ExcelBook $book, $unit, $decimals)
    {
        $id = "$unit/$decimals";
        if (!$this->unit_formats->offsetExists($id)) {
            $symbol = $unit;
            
            $formatString = '#,##0';

            if ($decimals > 0) {
                $zeros = str_repeat("0", $decimals);
                $formatString = $formatString . '.' . $zeros;
            }
            $formatString = $formatString . ' "' . $symbol . '"_-';

            $cfid = $book->addCustomFormat($formatString);
            $format = $book->addFormat();
            $format->numberFormat($cfid);
            $this->unit_formats->offsetSet($id, $format);
        }
        return $this->unit_formats->offsetGet($id);
    }
    
    

    /**
     *
     * @throws Exception\ExtensionNotLoadedException
     * @throws Exception\InvalidArgumentException
     *
     * @param string $file_format
     * @param string $locale default to en_US.UTF-8
     *
     * @return ExcelBook
     */
    public function getExcelBook($file_format = null, $locale = 'en_US.UTF-8')
    {
        if (!extension_loaded('excel')) {
            throw new Exception\ExtensionNotLoadedException(__METHOD__ . ' LibXLWriter requires excel (php_exccel) extension to be loaded');
        }

        if ($this->excelBook === null) {
            $libXL = new LibXL();
            if (is_array(self::$default_license)) {
                $libXL->setLicense(self::$default_license);
            }
            if ($file_format === null) {
                $file_format = $this->file_format;
            } elseif (!LibXL::isSupportedFormat($file_format)) {
                throw new Exception\InvalidArgumentException(__METHOD__ . " Unsupported format given '$file_format'");
            }

            $this->excelBook = $libXL->getExcelBook($file_format, $locale);
        }
        return $this->excelBook;
    }

    /**
     * @param Options $options
     * @return string
     */
    public function getData(Options $options = null)
    {
        if ($options === null) {
            // Take store global/default options
            $options = $this->store->getOptions();
        }

        // Get unformatted data when using excel writer
        $options->getHydrationOptions()->disableFormatters();
        
        // Some colu
        $options->getHydrationOptions()->disableColumnExclusion();

        $book = $this->getExcelBook();
        $this->generateExcel($book, $options);
        //$book->setLocale($locale);
       
        $temp_dir = sys_get_temp_dir();
        if (!is_dir($temp_dir)) {
            throw new \Exception(__METHOD__ . " System temporary directory '$temp_dir' does not exists.");
        }

        if (!is_writable($temp_dir)) {
            throw new \Exception(__METHOD__ . " System temporary directory '$temp_dir' is not writable.");
        }
        
        $filename = tempnam($temp_dir, 'libxl');

        $book->save($filename);

        $data = file_get_contents($filename);
        unlink($filename);
        return $data;
    }

    /**
     * @return ArrayObject
     */
    protected function getMetadataSpecs(ExcelBook $book)
    {
        $hide_thousands_separator = true;


        $specs = new ArrayObject();
        $cm = $this->store->getColumnModel();
        $metadata = $cm->getMetadata();

        $columns = $cm->getColumns();
        foreach ($columns as $name => $column) {
            $decimals = null;
            $format = null;
            $custom_column = null;
            $formatter = $column->getFormatter();

            if ($formatter instanceof \Soluble\FlexStore\Formatter\FormatterNumberInterface) {
                $type = 'number';
                $decimals = $formatter->getDecimals();
                if ($formatter instanceof \Soluble\FlexStore\Formatter\CurrencyFormatter) {
                    $currency = $formatter->getCurrencyCode();
                    if ($currency instanceof \Soluble\FlexStore\Formatter\RowColumn) {
                        // TODO better handling of callbacks
                        $format = function (ExcelBook $book, $currency, $decimals) {
                            return $this->getCurrencyFormat($book, $currency, $decimals);
                        };
                        $custom_column = $currency->getColumnName();
                    } else {
                        $format = $this->getCurrencyFormat($book, $currency, $decimals);
                    }
                } elseif ($formatter instanceof \Soluble\FlexStore\Formatter\UnitFormatter) {
                    $unit = $formatter->getUnit();
                    if ($unit instanceof \Soluble\FlexStore\Formatter\RowColumn) {
                        // TODO better handling of callbacks
                        $format = function (ExcelBook $book, $unit, $decimals) {
                            return $this->getUnitFormat($book, $unit, $decimals);
                        };
                        $custom_column = $unit->getColumnName();
                    } else {
                        $format = $this->getUnitFormat($book, $unit, $decimals);
                    }
                }
            } else {
                $model_type = $column->getType()->getName();
                //$spec['meta_type'] = $model_type;
                if ($model_type == ColumnType::TYPE_INTEGER) {
                    $decimals = 0;
                }
                if (array_key_exists($model_type, $this->typeMap)) {
                    $type = $this->typeMap[$model_type];
                } else {
                    $type = "text";
                }
            }

            // We now have the type
            if ($type == "number" && $decimals === null && $metadata !== null && $metadata->offsetExists($name)) {
                // try to guess from metadata
                $decimals = $metadata->offsetGet($name)->getNumericPrecision();
                if (!$decimals) {
                    $decimals = 0;
                }
            }

            // Let's make the format

            if ($format === null) {
                switch ($type) {
                    case 'date':
                        $mask = 'd/mm/yyyy';
                        $cfid = $book->addCustomFormat($mask);
                        $format = $book->addFormat();
                        $format->numberFormat($cfid);
                        break;
                    case 'datetime':
                        $mask = 'd/mm/yyyy h:mm';
                        $cfid = $book->addCustomFormat($mask);
                        $format = $book->addFormat();
                        $format->numberFormat($cfid);
                        break;
                    case 'number':
                        if ($hide_thousands_separator) {
                            $formatString = '0';
                        } else {
                            $formatString = '#,##0';
                        }
                        if ($decimals > 0) {
                            $zeros = str_repeat("0", $decimals);
                            $formatString = $formatString . '.' . $zeros;
                        }
                        $cfid = $book->addCustomFormat($formatString);
                        $format = $book->addFormat();
                        $format->numberFormat($cfid);

                        break;
                    default:
                        $format = null;
                }
            }
            
            if ($format === null) {
                $format = $this->getDefaultTextFormat($book);
            } else {
                /* Not yet supported, waiting php_excel 1.1
                $format->horizontalAlign($this->getFormatStyle('horizontalAlign'));
                $format->verticalAlign($this->getFormatStyle('verticalAlign'));
 
 */
            }

            // Save the spec
            $spec = new ArrayObject();
            $spec['name'] = $name;
            $spec['header'] = $column->getHeader();
            $spec['type'] = $type;
            $spec['decimals'] = $decimals;
            $spec['format'] = $format;
            $spec['custom_column'] = $custom_column;
            $specs->offsetSet($name, $spec);
        }

        //var_dump((array) $specs);
        return $specs;
    }
    
    protected function getDefaultTextFormat(ExcelBook $book)
    {
        $format = $book->addFormat();
/* Not yet supported, waiting php_excel 1.1
        $format->horizontalAlign($this->getFormatStyle('horizontalFormat'));
        $format->verticalAlign($this->getFormatStyle('verticalFormat'));
 *
 */
        return $format;
    }

    protected function getHeaderFormat(ExcelBook $book)
    {

        // Font selection
        $headerFont = $book->addFont();
        $headerFont->name("Tahoma");
        $headerFont->size(12);
        $headerFont->color(ExcelFormat::COLOR_WHITE);

        $headerFormat = $book->addFormat();

        $headerFormat->setFont($headerFont);

        $headerFormat->borderStyle(ExcelFormat::BORDERSTYLE_THIN);
        $headerFormat->verticalAlign(ExcelFormat::ALIGNV_CENTER);
        $headerFormat->borderColor(ExcelFormat::COLOR_GRAY50);
        //$headerFormat->patternBackgroundColor(ExcelFormat:COLOR_LIGHTBLUE);
        $headerFormat->patternForegroundColor(ExcelFormat::COLOR_LIGHTBLUE);
        $headerFormat->fillPattern(ExcelFormat::FILLPATTERN_SOLID);
        return $headerFormat;
    }

    /**
     *
     * @param ExcelBook $book
     * @param Options $options
     * @return ExcelBook
     */
    protected function generateExcel(ExcelBook $book, Options $options = null)
    {
        $sheet = $book->addSheet("Sheet");
        $headerFormat = $this->getHeaderFormat($book);

        // Step 1, print header
        $specs = $this->getMetadataSpecs($book);
        $column_max_widths = array_fill_keys(array_keys((array) $specs), 0);
        $col_idx = 0;
        foreach ($specs as $name => $spec) {
            $sheet->write($row = 0, $col_idx, $spec['header'], $headerFormat);
            $column_max_widths[$name] = max(strlen($spec['header']) * $this->column_width_multiplier, $column_max_widths[$name]);
            $col_idx++;
        }

        $sheet->setRowHeight(0, 30);

        // Fix the header
        $sheet->splitSheet(1, 0);

        // Fill document content
        
        
        
        $data = $this->store->getData($options);

        foreach ($data as $idx => $row) {
            $col_idx = 0;
            $row_idx = $idx + 1;
            $rowHeight = $sheet->rowHeight($row_idx);
            
            foreach ($specs as $name => $spec) {
                $value = $row[$name];

                if ($spec['format'] !== null) {
                    $format = $spec['format'];
                    if (is_callable($format)) {
                        // Dynamic column format
                        $sheet->write($row_idx, $col_idx, (string) $value, $format($book, $row[$spec['custom_column']], $spec['decimals']), ExcelFormat::AS_NUMERIC_STRING);
                    } else {
                        switch ($spec['type']) {
                            case 'number':
                                $sheet->write($row_idx, $col_idx, (string) $value, $spec['format'], ExcelFormat::AS_NUMERIC_STRING);
                                break;
                            case 'date':
                            case 'datetime':
                                if ($value != '') {
                                    $time = strtotime($value);
                                } else {
                                    $time = null;
                                }
                                $sheet->write($row_idx, $col_idx, $time, $spec['format'], ExcelFormat::AS_DATE);
                                break;
                            default:
                                if (preg_match('/(\n)/', $value)) {
                                    // More height when one cell contains multiple lines
                                    $sheet->setRowHeight($row_idx, ceil($rowHeight * 1.9));
                                }
                                
                                $sheet->write($row_idx, $col_idx, $value, $format);
                        }
                    }
                } else {
                    $sheet->write($row_idx, $col_idx, $value);
                }
                $column_max_widths[$name] = max(strlen((string) $value) * $this->column_width_multiplier, $column_max_widths[$name]);
                $col_idx++;
            }
        }

        foreach (array_values($column_max_widths) as $idx => $width) {
            $sheet->setColWidth($idx, ceil($idx), min($width, $this->column_max_width));
        }

        $sheet->setPrintGridlines(true);
        //$sheet->setPrintRepeatRows(1, 2);
        //$sheet->setPrintHeaders(true);
        //$sheet->setVerPageBreak($col_idx, true);

        return $book;
    }

    
    /**
     *
     * @param string $license_name
     * @param string $license_key
     */
    public static function setDefaultLicense($license_name, $license_key)
    {
        self::$default_license = array('name' => $license_name, 'key' => $license_key);
    }
    
    
    public function getFormatStyle($style)
    {
        $styles = $this->getFormatStyles();
        return $styles[$style];
    }
    
    public function getFormatStyles()
    {
        $styles = array(
                'horizontalAlign' => ExcelFormat::ALIGNH_LEFT,
                'verticalAlign' => ExcelFormat::ALIGNV_TOP
        );
        return $styles;
    }

    /**
     * Return default headers for sending store data via http
     * @return SimpleHeaders
     */
    public function getHttpHeaders()
    {
        if ($this->headers === null) {
            $this->headers = new SimpleHeaders();
            $this->headers->setContentType('application/excel', 'utf-8');
            $this->headers->setContentDispositionType(SimpleHeaders::DIPOSITION_ATTACHEMENT);
        }
        return $this->headers;
    }
}
