<?php

namespace Geocoder\Tests\Provider;

use Geocoder\Tests\TestCase;

use Geocoder\Provider\GeoipProvider;

class GeoipProviderTest extends TestCase
{
    public function setUp()
    {
        if (!function_exists('geoip_record_by_name')) {
            $this->markTestSkipped('You have to install GeoIP.');
        }
    }

    public function testGetGeocodedDataWithNull()
    {
        $this->provider = new GeoipProvider($this->getMockAdapter());
        $result = $this->provider->getGeocodedData(null);

        $this->assertNull($result['latitude']);
        $this->assertNull($result['longitude']);
        $this->assertNull($result['city']);
        $this->assertNull($result['zipcode']);
        $this->assertNull($result['region']);
        $this->assertNull($result['country']);
    }

    public function testGetGeocodedDataWithEmpty()
    {
        $this->provider = new GeoipProvider($this->getMockAdapter());
        $result = $this->provider->getGeocodedData('');

        $this->assertNull($result['latitude']);
        $this->assertNull($result['longitude']);
        $this->assertNull($result['city']);
        $this->assertNull($result['zipcode']);
        $this->assertNull($result['region']);
        $this->assertNull($result['country']);
    }

    public function testGetGeocodedDataWithAddress()
    {
        $this->provider = new GeoipProvider($this->getMockAdapter());
        $result = $this->provider->getGeocodedData('10 avenue Gambetta, Paris, France');

        $this->assertNull($result['latitude']);
        $this->assertNull($result['longitude']);
        $this->assertNull($result['city']);
        $this->assertNull($result['zipcode']);
        $this->assertNull($result['region']);
        $this->assertNull($result['country']);
    }

    public function testGetGeocodedDataWithLocalhost()
    {
        $this->provider = new GeoipProvider($this->getMockAdapter($this->never()));
        $result = $this->provider->getGeocodedData('127.0.0.1');

        $this->assertArrayNotHasKey('latitude', $result);
        $this->assertArrayNotHasKey('longitude', $result);
        $this->assertArrayNotHasKey('zipcode', $result);

        $this->assertEquals('localhost', $result['city']);
        $this->assertEquals('localhost', $result['region']);
        $this->assertEquals('localhost', $result['county']);
        $this->assertEquals('localhost', $result['country']);
    }

    public function testGetGeocodedDataWithRealIp()
    {
        $this->provider = new GeoipProvider(new \Geocoder\HttpAdapter\BuzzHttpAdapter());
        $result = $this->provider->getGeocodedData('74.200.247.59');

        $this->assertEquals(33.034698486328, $result['latitude']);
        $this->assertEquals(-96.813400268555, $result['longitude']);
        $this->assertEquals(75093, $result['zipcode']);
        $this->assertEquals('Plano', $result['city']);
        $this->assertEquals('TX', $result['region']);
        $this->assertEquals('United States', $result['country']);
    }

    /**
     * @expectedException \Geocoder\Exception\UnsupportedException
     */
    public function testGetReverseData()
    {
        $this->provider = new GeoipProvider($this->getMockAdapter($this->never()));
        $this->provider->getReversedData(array(1, 2));
    }

    /**
     * {@inheritdoc}
     */
    protected function getMockAdapter($expects = null)
    {
        if (null === $expects) {
            $expects = $this->once();
        }

        $mock = $this->getMock('\Geocoder\HttpAdapter\HttpAdapterInterface');

        return $mock;
    }
}
