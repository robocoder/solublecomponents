<?php

namespace Soluble\FlexStore\Formatter;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-05 at 13:20:15.
 */
class CurrencyFormatTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurrencyFormat
     */
    protected $currencyFormatter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->currencyFormatter = new CurrencyFormatter;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testConstruct()
    {
        $params = array(
            'locale' => 'zh_CN',
            'pattern' => '#,##0.###',
            'decimals' => 3
        );
        $f = new CurrencyFormatter($params);
        $this->assertEquals('zh_CN', $f->getLocale());
        $this->assertEquals('#,##0.###', $f->getPattern());
        $this->assertEquals(3, $f->getDecimals());
    }

    public function testGetSet()
    {
        $f = $this->currencyFormatter;
        $this->assertInternalType('string', $f->getLocale());
        $this->assertEquals($f->getLocale(), substr(\Locale::getDefault(), 0, 5));
        $this->assertNull($f->getPattern());
        $this->assertEquals(2, $f->getDecimals());

        $f->setDecimals(3);
        $f->setPattern('#,##0.###');
        $f->setLocale('zh_CN');

        $this->assertEquals('zh_CN', $f->getLocale());
        $this->assertEquals('#,##0.###', $f->getPattern());
        $this->assertEquals(3, $f->getDecimals());
    }

    public function testConstructThrowsInvalidArgumentException()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\InvalidArgumentException');
        $params = array(
            'cool' => 0
        );
        $f = new CurrencyFormatter($params);
    }

    public function testFormat()
    {
        $moneyFormatter = new CurrencyFormatter();
        $moneyFormatter->setCurrencyCode('EUR');
        $moneyFormatter->setLocale('fr_FR');
        $this->assertEquals('10,24 €', $moneyFormatter->format(10.239));
        $moneyFormatter->setLocale('en_US');
        $this->assertEquals('€10.24', $moneyFormatter->format(10.239));
        $moneyFormatter->setLocale('en_GB');
        $this->assertEquals('€10.24', $moneyFormatter->format(10.239));
        $moneyFormatter->setCurrencyCode('CAD');
        $this->assertEquals('CA$10.24', $moneyFormatter->format(10.239));
        $moneyFormatter->setCurrencyCode('CNY');
        $this->assertEquals('CN¥10.24', $moneyFormatter->format(10.239));
        $moneyFormatter->setCurrencyCode('GBP');
        $this->assertEquals('£10.24', $moneyFormatter->format(10.239));
        $this->assertEquals('-£10.24', $moneyFormatter->format(-10.239));
        $moneyFormatter->setLocale('fr_FR');
        $moneyFormatter->setCurrencyCode('EUR');
        $this->assertEquals('-10,24 €', $moneyFormatter->format(-10.239));
        $moneyFormatter->setLocale('en_GB');
        $moneyFormatter->setCurrencyCode('GBP');
        $this->assertEquals('-£10.24', $moneyFormatter->format(-10.239));

        $parsed = $moneyFormatter->parse('-£10.24');
        $this->assertInternalType('array', $parsed);
        $this->assertEquals('GBP', $parsed['currency']);
        $this->assertEquals(-10.24, $parsed['value']);


        $params = array(
            'locale' => 'fr_FR',
            'decimals' => 3
        );
        $f = new CurrencyFormatter($params);
        $f->setCurrencyCode('EUR');
        $this->assertEquals('1 123,457 €', $f->format(1123.4567));
        
        $this->assertEquals('0,000 €', $f->format(null));
    }

    public function testFormatThrowsRuntimeException()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\RuntimeException');
        $f = new CurrencyFormatter();
        $f->format(1123.4567);
    }

    public function testFormatThrowsRuntimeException2()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\RuntimeException');
        $params = array(
            'locale' => 'fr_FR',
            'decimals' => 3
        );
        $f = new CurrencyFormatter($params);
        $f->format(array('cool'));
    }

    public function testFormatThrowsRuntimeException3()
    {
        $this->setExpectedException('Soluble\FlexStore\Exception\RuntimeException');
        $params = array(
            'locale' => 'fr_FR',
            'decimals' => 3
        );
        $f = new CurrencyFormatter($params);
        $f->format('not a number');
    }
}
