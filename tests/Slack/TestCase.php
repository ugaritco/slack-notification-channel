<?php

namespace Heritage\Tests\Notifications\Slack;

use Heritage\Notifications\Slack\SlackChannel;
use Heritage\Notifications\SlackChannelServiceProvider;
use Heritage\Support\Facades\Http;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @var \Heritage\Http\Client\Factory
     */
    protected $http;

    /**
     * @var \Heritage\Notifications\Slack\SlackChannel
     */
    protected $slackChannel;

    /**
     * @param  \Heritage\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            SlackChannelServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = Http::fake();
        $this->slackChannel = new SlackChannel($this->http);
    }
}
