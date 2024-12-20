<?php

declare(strict_types=1);

namespace Domnikl\Test\Statsd;

use Domnikl\Statsd\Client as Client;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var ConnectionMock
     */
    private $connection;

    protected function setUp(): void
    {
        $this->connection = new ConnectionMock();
        $this->client = new Client($this->connection, 'test');
    }

    public function testInit()
    {
        $client = new Client(new ConnectionMock());
        $this->assertEquals('', $client->getNamespace());
    }

    public function testNamespace()
    {
        $client = new Client(new ConnectionMock(), 'test.foo');
        $this->assertEquals('test.foo', $client->getNamespace());

        $client->setNamespace('bar.baz');
        $this->assertEquals('bar.baz', $client->getNamespace());
    }

    public function testCount()
    {
        $this->client->count('foo.bar', 100);
        $this->assertEquals(
            'test.foo.bar:100|c',
            $this->connection->getLastMessage()
        );
    }

    public function testCountWithFloatValue()
    {
        $this->client->count('foo.bar', 100.45);
        $this->assertEquals(
            'test.foo.bar:100.45|c',
            $this->connection->getLastMessage()
        );
    }

    public function sampleRateData()
    {
        return [
            [0.9, 1, '0.9'],
            [0.9, 0.5, '0.5'],
        ];
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testCountWithSamplingRate(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->count('foo.baz', 100, $sampleRate);
        }
        $this->assertEquals(
            "test.foo.baz:100|c|@{$expectedSampleRate}",
            $this->connection->getLastMessage()
        );
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testCountWithSamplingRateAndTags(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->count('foo.baz', 100, $sampleRate, ['tag' => 'value']);
        }
        $this->assertEquals(
            "test.foo.baz:100|c|@{$expectedSampleRate}|#tag:value",
            $this->connection->getLastMessage()
        );
    }

    public function testIncrement()
    {
        $this->client->increment('foo.baz');
        $this->assertEquals(
            'test.foo.baz:1|c',
            $this->connection->getLastMessage()
        );
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testIncrementWithSamplingRate(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->increment('foo.baz', $sampleRate);
        }
        $this->assertEquals(
            "test.foo.baz:1|c|@{$expectedSampleRate}",
            $this->connection->getLastMessage()
        );
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testIncrementWithSamplingRateAndTags(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->increment('foo.baz', $sampleRate, ['tag' => 'value']);
        }
        $this->assertEquals(
            "test.foo.baz:1|c|@{$expectedSampleRate}|#tag:value",
            $this->connection->getLastMessage()
        );
    }

    public function testDecrement()
    {
        $this->client->decrement('foo.baz');
        $this->assertEquals(
            'test.foo.baz:-1|c',
            $this->connection->getLastMessage()
        );
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testDecrementWithSamplingRate(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->decrement('foo.baz', $sampleRate);
        }
        $this->assertEquals(
            "test.foo.baz:-1|c|@{$expectedSampleRate}",
            $this->connection->getLastMessage()
        );
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testDecrementWithSamplingRateAndTags(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->decrement('foo.baz', $sampleRate, ['tag' => 'value']);
        }
        $this->assertEquals(
            "test.foo.baz:-1|c|@{$expectedSampleRate}|#tag:value",
            $this->connection->getLastMessage()
        );
    }

    public function testCanMeasureTimingWithClosure()
    {
        $this->client->timing('foo.baz', 2000);
        $this->assertEquals(
            'test.foo.baz:2000|ms',
            $this->connection->getLastMessage()
        );
    }


    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testTimingWithSamplingRate(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->timing('foo.baz', 2000, $sampleRate);
        }
        $this->assertEquals(
            "test.foo.baz:2000|ms|@{$expectedSampleRate}",
            $this->connection->getLastMessage()
        );
    }

    public function testCanMeasureTimingByStartingAndEndingTiming()
    {
        $key = 'foo.bar';
        $this->client->startTiming($key);
        usleep(10000);
        $this->client->endTiming($key);

        // ranges between 1000 and 1001ms
        $this->assertMatchesRegularExpression(
            '/^test\.foo\.bar:[0-9]+(.[0-9]+)?\|ms$/',
            $this->connection->getLastMessage()
        );
    }

    public function testEndTimingReturnsTiming()
    {
        $key = 'foo.bar';
        $this->assertNull($this->client->endTiming($key));

        $sleep = 10000;
        $this->client->startTiming($key);
        usleep($sleep);

        $this->assertGreaterThanOrEqual($sleep / 1000, $this->client->endTiming($key));
    }

    /**
     * @dataProvider sampleRateData
     * @group sampling
     */
    public function testStartEndTimingWithSamplingRate(float $globalSampleRate, float $sampleRate, string $expectedSampleRate)
    {
        $client = new Client($this->connection, 'test', $globalSampleRate);
        for ($i = 0; $i < 10; $i++) {
            $client->startTiming('foo.baz');
            usleep(10000);
            $client->endTiming('foo.baz', $sampleRate);
        }

        // ranges between 1000 and 1001ms
        $this->assertMatchesRegularExpression(
            "/^test\.foo\.baz:1[0-9](.[0-9]+)?\|ms\|@{$expectedSampleRate}$/",
            $this->connection->getLastMessage()
        );
    }

    public function testTimeClosure()
    {
        $evald = $this->client->time('foo', function () {
            return "foobar";
        });

        $this->assertEquals('foobar', $evald);
        $this->assertMatchesRegularExpression(
            '/test\.foo\.baz:100[0|1]{1}|ms|@0.1/',
            $this->connection->getLastMessage()
        );
    }

    /**
     * @group memory
     */
    public function testMemory()
    {
        $this->client->memory('foo.bar');
        $this->assertMatchesRegularExpression(
            '/test\.foo\.bar:[0-9]{4,}|c/',
            $this->connection->getLastMessage()
        );
    }

    /**
     * @group memory
     */
    public function testMemoryProfile()
    {
        $this->client->startMemoryProfile('foo.bar');
        /** @noinspection PhpUnusedLocalVariableInspection */
        $memoryUsage = memory_get_usage();
        /** @noinspection PhpUnusedLocalVariableInspection */
        $foobar = "fooooooooooooooooooooooooooooooooooooooooooooooooooooooobar";
        $this->client->endMemoryProfile('foo.bar');

        $message = $this->connection->getLastMessage();
        $this->assertMatchesRegularExpression('/test\.foo\.bar:[0-9]{4,}|c/', $message);

        preg_match('/test\.foo\.bar\:([0-9]*)|c/', $message, $matches);
        $this->assertGreaterThan(0, $matches[1]);
    }

    public function testGauge()
    {
        $this->client->gauge("foobar", 333);

        $message = $this->connection->getLastMessage();
        $this->assertEquals('test.foobar:333|g', $message);
    }

    public function testGaugeWithTags()
    {
        $this->client->gauge("foobar", 333, ['tag' => 'value']);
        $message = $this->connection->getLastMessage();
        $this->assertEquals('test.foobar:333|g|#tag:value', $message);
    }

    public function testGaugeCanReceiveFormattedNumber()
    {
        $this->client->gauge('foobar', '+11');

        $message = $this->connection->getLastMessage();
        $this->assertEquals('test.foobar:+11|g', $message);
    }

    public function testSet()
    {
        $this->client->set("barfoo", 666);

        $message = $this->connection->getLastMessage();
        $this->assertEquals('test.barfoo:666|s', $message);
    }

    public function testSetWithTags()
    {
        $this->client->set("barfoo", 666, ['tag' => 'value', 'tag2' => 'value2']);
        $message = $this->connection->getLastMessage();
        $this->assertEquals('test.barfoo:666|s|#tag:value,tag2:value2', $message);
    }

    public function testTimeClosureRecursive()
    {
        $evald = $this->client->time(
            'foo',
            function () {
                return $this->client->time(
                    'foo',
                    function () {
                        return 'foobar';
                    },
                    1.0,
                    ['run' => 2]
                );
            },
            1.0,
            ['run' => 1]
        );

        $this->assertEquals('foobar', $evald);

        $messages = $this->connection->getMessages();
        $this->assertEquals(2, count($messages));
        $this->assertMatchesRegularExpression('/test\.foo\:[\d\.]*\|ms\|#run:2/', $messages[0]);
        $this->assertMatchesRegularExpression('/test\.foo\:[\d\.]*\|ms\|#run:1/', $messages[1]);
    }
}
