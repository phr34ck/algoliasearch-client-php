<?php

include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../algoliasearch.php';

class FunctionTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new \AlgoliaSearch\Client(getenv('ALGOLIA_APPLICATION_ID'), getenv('ALGOLIA_API_KEY'));  
        $this->index = $this->client->initIndex(safe_name('àlgol?à-php'));
        $res = $this->index->addObject(array("firstname" => "Robin"));
        $this->index->waitTask($res['taskID']);
    }

    public function tearDown()
    {
        try {
            $this->client->deleteIndex(safe_name('àlgol?à-php'));
        } catch (AlgoliaSearch\AlgoliaException $e) {
            // not fatal
        }

    }

    public function testConstructAPIKey()
    {
        $this->setExpectedException('Exception');
        new \AlgoliaSearch\Client(getenv('ALGOLIA_APPLICATION_ID'), null);
    }

    public function testConstructAPPID()
    {
        $this->setExpectedException('Exception');
        new \AlgoliaSearch\Client(null, getenv('ALGOLIA_API_KEY'));
    }

    public function testConstructHost()
    {
        $this->setExpectedException('Exception');
        $host = array("toto");
        $this->badClient = new \AlgoliaSearch\Client(getenv('ALGOLIA_APPLICATION_ID'), getenv('ALGOLIA_API_KEY'), $host);
        $this->badIndex = $this->badClient->initIndex(safe_name('àlgol?à-php'));
        $res = $this->badIndex->addObject(array("firstname" => "Robin"));
        $this->badIndex->waitTask($res['taskID']);
    }

    public function testBadAPPIP()
    {
        $this->setExpectedException('Exception');
        $this->badClient = new \AlgoliaSearch\Client(getenv('ALGOLIA_APPLICATION_ID'), "toto");
        $this->index = $this->badClient->listIndexes();
    }


    public function testFunction()
    {
        $this->client->disableRateLimitForward(); 
        $this->client->listIndexes(); 
    }

    private $client;
    private $index;
}
