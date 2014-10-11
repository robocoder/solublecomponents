<?php

namespace Soluble\FlexStore\Writer;

use Soluble\FlexStore\Source\QueryableSourceInterface;
use Soluble\FlexStore\Writer\Http\SimpleHeaders;
use DateTime;
use Soluble\FlexStore\Options;

class JsonWriter extends AbstractSendableWriter
{

    /**
     *
     * @var SimpleHeaders
     */
    protected $headers;

    /**
     * @param Options $options
     * @return \Zend\View\Model\JsonModel
     */
    public function getData(Options $options = null)
    {
        if ($options === null) {
            $options = new Options();
        }

        // Get unformatted data when using json
        $options->getHydrationOptions()->disableFormatters();

        $data = $this->store->getData($options);
        $now = new DateTime();

        $d = array(
            'success' => true,
            'timestamp' => $now->format(DateTime::W3C),
            'total' => $data->getTotalRows(),
            'start' => $data->getSource()->getOptions()->getOffset(),
            'limit' => $data->getSource()->getOptions()->getLimit(),
            'data' => $data->toArray()
        );

        if ($this->options['debug']) {
            $source = $data->getSource();
            if ($source instanceof QueryableSourceInterface) {
                $d['query'] = $source->getQueryString();
            }
        }

        return json_encode($d);
    }

    /**
     * Return default headers for sending store data via http
     * @return SimpleHeaders
     */
    public function getHttpHeaders()
    {
        if ($this->headers === null) {
            $this->headers = new SimpleHeaders();
            $this->headers->setContentType('application/json', 'utf-8');
            //$this->headers->setContentDispositionType(SimpleHeaders::DIPOSITION_ATTACHEMENT);
        }
        return $this->headers;
    }

}