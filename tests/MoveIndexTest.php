<?php

include __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../algoliasearch.php';

class MoveIndexTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new \AlgoliaSearch\Client(getenv('ALGOLIA_APPLICATION_ID'), getenv('ALGOLIA_API_KEY'));
        $this->index = $this->client->initIndex(safe_name('àlgol?à-php'));
        try {
            $this->index->clearIndex();
        } catch (AlgoliaSearch\AlgoliaException $e) {
            // not fatal
        }
        try {
            $task = $this->client->deleteIndex(safe_name('àlgol?à2-php'));
            //$this->client->waitTask($task['taskID']);
        } catch (AlgoliaSearch\AlgoliaException $e) {
            // CopyIndex does not exist
        }
    }

    public function tearDown()
    {
        try {
            $this->client->deleteIndex(safe_name('àlgol?à-php'));
        } catch (AlgoliaSearch\AlgoliaException $e) {
            // not fatal
        }
        try {
            $this->client->deleteIndex(safe_name('àlgol?à2-php'));
        } catch (AlgoliaSearch\AlgoliaException $e) {
            // not fatal
        }

    }

    public function includeValue($tab, $attrName, $value)
    {
        foreach ($tab as $key => $elt) {
            if ($elt[$attrName] == $value) {
                return true;
            }
        }
        return false;
    }

    public function testMoveIndex()
    {
        $task = $this->index->addObject(array("firstname" => "Robin"));
        $this->index->waitTask($task['taskID']);

        $task = $this->client->moveIndex(safe_name('àlgol?à-php'), safe_name('àlgol?à2-php'));
        $this->index->waitTask($task['taskID']);

        $this->index = $this->client->initIndex(safe_name('àlgol?à2-php'));

        $res = $this->index->search('');
        $list = $this->client->listIndexes();
        $this->assertTrue($this->includeValue($list['items'], 'name', safe_name('àlgol?à2-php')));
        $this->assertFalse($this->includeValue($list['items'], 'name', safe_name('àlgol?à-php')));
        $this->assertEquals(1, $res['nbHits']);
        $this->assertEquals("Robin", $res['hits'][0]['firstname']);
    }

    public function testCopyIndex()
    {
        $this->index2 = $this->client->initIndex(safe_name('àlgol?à2-php'));
        $task = $this->index2->addObject(array("firstname" => "Robin"));
        $this->index2->waitTask($task['taskID']);

        $this->expectOutputString('');
        $task = $this->client->copyIndex(safe_name('àlgol?à2-php'), safe_name('àlgol?à-php'));
        //$this->client->waitTask($task['taskID']);

        $this->index = $this->client->initIndex(safe_name('àlgol?à-php'));
        $this->index2 = $this->client->initIndex(safe_name('àlgol?à2-php'));

        $res = $this->index->search('');
        $this->assertEquals(1, $res['nbHits']);
        $del = $this->index->deleteObject($res['hits'][0]['objectID']);
        $this->index->waitTask($del['taskID']);

        $res = $this->index2->search('');

        $this->assertEquals(1, $res['nbHits']);
        $this->assertEquals("Robin", $res['hits'][0]['firstname']);
    }

    private $client;
    private $index;
}
