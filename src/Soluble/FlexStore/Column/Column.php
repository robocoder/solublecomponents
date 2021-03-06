<?php

namespace Soluble\FlexStore\Column;

use Soluble\FlexStore\Column\Type\AbstractType;
use Soluble\FlexStore\Formatter\FormatterInterface;

class Column implements ColumnSettableInterface
{
    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var array
     */
    protected $properties = array(
        'type' => null, // will be defaulted to string
        'formatter' => null,
        'excluded' => false,
        'hidden' => false,
        'width' => null,
        'header' => null, // will be defaulted to name
        'filterable' => true,
        'groupable' => true,
        'sortable' => true,
        'editable' => false,
        'virtual' => true
    );
    

    /**
     * Constructor
     * @param string $name unique identifier name for the column
     * @param array $properties associative array with (header,width,filterable,groupable,sortable,hidden,excluded,editable...)
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($name, array $properties = null)
    {
        if (!is_string($name)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . " Column name must be a string");
        }
        if (trim($name) == '') {
            throw new Exception\InvalidArgumentException(__METHOD__ . " Column name cannot be empty");
        }
        $this->name = $name;
        if ($properties !== null) {
            $this->setProperties($properties);
        }
        $this->initDefaults();
    }

    /**
     * This method ensure some properties are defaulted.
     * For example header with name and type is string
     */
    protected function initDefaults()
    {
        if ($this->properties['header'] == '') {
            $this->setHeader($this->name);
        }
        if ($this->properties['type'] == '') {
            $this->setType(ColumnType::createType(ColumnType::TYPE_STRING));
        }
    }

    /**
     * Get the name of the column
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set column datatype
     * @param string|AbstractType $type
     * @throws Exception\InvalidArgumentException when the type is not supported.
     * @return Column
     */
    public function setType($type)
    {
        if (is_string($type)) {
            $type = ColumnType::createType($type);
        } elseif (!$type instanceof AbstractType) {
            throw new Exception\InvalidArgumentException(__METHOD__ . " setType() accepts only AbstractType or string.");
        }
        $this->properties['type'] = $type;
        return $this;
    }
    

    /**
     *
     * @return AbstractType
     */
    public function getType()
    {
        return $this->properties['type'];
    }

    /**
     *
     * @param FormatterInterface $formatter
     * @return Column
     */
    public function setFormatter(FormatterInterface $formatter)
    {
        $this->properties['formatter'] = $formatter;
        return $this;
    }

    /**
     *
     * @return FormatterInterface|null
     */
    public function getFormatter()
    {
        return $this->properties['formatter'];
    }

    /**
     *
     * @param boolean $virtual
     * @return Column
     */
    public function setVirtual($virtual = true)
    {
        $this->properties['virtual'] = (bool) $virtual;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->properties['virtual'];
    }
    
    
    /**
     *
     * @param boolean $excluded
     * @return Column
     */
    public function setExcluded($excluded = true)
    {
        $this->properties['excluded'] = (bool) $excluded;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isExcluded()
    {
        return $this->properties['excluded'];
    }

    /**
     *
     * @param boolean $editable
     * @return Column
     */
    public function setEditable($editable = true)
    {
        $this->properties['editable'] = (bool) $editable;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isEditable()
    {
        return $this->properties['editable'];
    }

    /**
     *
     * @param boolean $hidden
     * @return Column
     */
    public function setHidden($hidden = true)
    {
        $this->properties['hidden'] = (bool) $hidden;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isHidden()
    {
        return (bool) $this->properties['hidden'];
    }

    /**
     *
     * @param boolean $sortable
     * @return Column
     */
    public function setSortable($sortable = true)
    {
        $this->properties['sortable'] = (bool) $sortable;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isSortable()
    {
        return (bool) $this->properties['sortable'];
    }

    /**
     *
     * @param boolean $groupable
     * @return Column
     */
    public function setGroupable($groupable = true)
    {
        $this->properties['groupable'] = (bool) $groupable;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isGroupable()
    {
        return (bool) $this->properties['groupable'];
    }

    /**
     *
     * @param boolean $filterable
     * @return Column
     */
    public function setFilterable($filterable = true)
    {
        $this->properties['filterable'] = (bool) $filterable;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function isFilterable()
    {
        return (bool) $this->properties['filterable'];
    }

    /**
     * Set recommended width for the column
     *
     * @throws Exception\InvalidArgumentException
     * @param float|int|string $width
     * @return Column
     */
    public function setWidth($width)
    {
        if (!is_scalar($width)) {
            throw new Exception\InvalidArgumentException(__METHOD__ . " Width parameter must be scalar.");
        }
        $this->properties['width'] = $width;
        return $this;
    }

    /**
     *
     * @return float|int|string
     */
    public function getWidth()
    {
        return $this->properties['width'];
    }

    /**
     * Set table header for this column
     *
     * @throws Exception\InvalidArgumentException
     * @param string|null $header
     * @return Column
     */
    public function setHeader($header)
    {
        $this->properties['header'] = $header;
        return $this;
    }

    /**
     *
     * @return string|null
     */
    public function getHeader()
    {
        return $this->properties['header'];
    }

    /**
     *
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set properties for the column
     *
     * @throws Exception\InvalidArgumentException
     * @param array $properties associative array with (header,width,filterable,groupable,sortable,hidden,excluded,editable...)
     * @return Column
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $key => $value) {
            if (array_key_exists($key, $this->properties)) {
                $method = 'set' . ucfirst($key);
                $this->$method($value);
            } else {
                throw new Exception\InvalidArgumentException(__METHOD__ . " property '$key' is not supported in column.");
            }
        }
        return $this;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }
}
