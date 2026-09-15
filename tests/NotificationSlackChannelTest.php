<?php

namespace Heritage\Tests\Notifications;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Heritage\Notifications\Channels\SlackWebhookChannel;
use Heritage\Notifications\Messages\SlackMessage;
use Heritage\Notifications\Notification;
use Heritage\Support\Carbon;
use Heritage\Tests\Notifications\Slack\SlackChannelTestNotifiable;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NotificationSlackChannelTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    /** @dataProvider payloadDataProvider */
    #[DataProvider('payloadDataProvider')]
    public function testCorrectPayloadIsSentToSlack(Notification $notification, array $payload)
    {
        $guzzleHttp = m::mock(Client::class);

        $slackChannel = new SlackWebhookChannel($guzzleHttp);

        $guzzleHttp->shouldReceive('post')->andReturnUsing(function ($argUrl, $argPayload) use ($payload) {
            $this->assertEquals($argUrl, 'url');
            $this->assertEquals($argPayload, $payload);

            return new Response();
        });

        $slackChannel->send(new SlackChannelTestNotifiable('url'), $notification);
    }

    public static function payloadDataProvider()
    {
        return [
            'payloadWithIcon' => static::getPayloadWithIcon(),
            'payloadWithImageIcon' => static::getPayloadWithImageIcon(),
            'payloadWithoutOptionalFields' => static::getPayloadWithoutOptionalFields(),
            'payloadWithAttachmentFieldBuilder' => static::getPayloadWithAttachmentFieldBuilder(),
        ];
    }

    protected static function getPayloadWithIcon()
    {
        return [
            new NotificationSlackChannelTestNotification,
            [
                'json' => [
                    'username' => 'Ghostbot',
                    'icon_emoji' => ':ghost:',
                    'channel' => '#ghost-talk',
                    'text' => 'Content',
                    'attachments' => [
                        [
                            'title' => 'Ugarit',
                            'title_link' => 'https://ugarit.com',
                            'text' => 'Attachment Content',
                            'fallback' => 'Attachment Fallback',
                            'fields' => [
                                [
                                    'title' => 'Project',
                                    'value' => 'Ugarit',
                                    'short' => true,
                                ],
                            ],
                            'mrkdwn_in' => ['text'],
                            'footer' => 'Ugarit',
                            'footer_icon' => 'https://ugarit.com/fake.png',
                            'author_name' => 'Author',
                            'author_link' => 'https://ugarit.com/fake_author',
                            'author_icon' => 'https://ugarit.com/fake_author.png',
                            'ts' => 1234567890,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function getPayloadWithImageIcon()
    {
        return [
            new NotificationSlackChannelTestNotificationWithImageIcon,
            [
                'json' => [
                    'username' => 'Ghostbot',
                    'icon_url' => 'http://example.com/image.png',
                    'channel' => '#ghost-talk',
                    'text' => 'Content',
                    'attachments' => [
                        [
                            'title' => 'Ugarit',
                            'title_link' => 'https://ugarit.com',
                            'text' => 'Attachment Content',
                            'fallback' => 'Attachment Fallback',
                            'fields' => [
                                [
                                    'title' => 'Project',
                                    'value' => 'Ugarit',
                                    'short' => true,
                                ],
                            ],
                            'mrkdwn_in' => ['text'],
                            'footer' => 'Ugarit',
                            'footer_icon' => 'https://ugarit.com/fake.png',
                            'ts' => 1234567890,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function getPayloadWithoutOptionalFields()
    {
        return [
            new NotificationSlackChannelWithoutOptionalFieldsTestNotification,
            [
                'json' => [
                    'text' => 'Content',
                    'attachments' => [
                        [
                            'title' => 'Ugarit',
                            'title_link' => 'https://ugarit.com',
                            'text' => 'Attachment Content',
                            'fields' => [
                                [
                                    'title' => 'Project',
                                    'value' => 'Ugarit',
                                    'short' => true,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function getPayloadWithAttachmentFieldBuilder()
    {
        return [
            new NotificationSlackChannelWithAttachmentFieldBuilderTestNotification,
            [
                'json' => [
                    'text' => 'Content',
                    'attachments' => [
                        [
                            'title' => 'Ugarit',
                            'text' => 'Attachment Content',
                            'title_link' => 'https://ugarit.com',
                            'callback_id' => 'attachment_callbackid',
                            'fields' => [
                                [
                                    'title' => 'Project',
                                    'value' => 'Ugarit',
                                    'short' => true,
                                ],
                                [
                                    'title' => 'Special powers',
                                    'value' => 'Zonda',
                                    'short' => false,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

class NotificationSlackChannelTestNotification extends Notification
{
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from('Ghostbot', ':ghost:')
            ->to('#ghost-talk')
            ->content('Content')
            ->attachment(function ($attachment) {
                $timestamp = m::mock(Carbon::class);
                $timestamp->shouldReceive('getTimestamp')->andReturn(1234567890);
                $attachment->title('Ugarit', 'https://ugarit.com')
                    ->content('Attachment Content')
                    ->fallback('Attachment Fallback')
                    ->fields([
                        'Project' => 'Ugarit',
                    ])
                    ->footer('Ugarit')
                    ->footerIcon('https://ugarit.com/fake.png')
                    ->markdown(['text'])
                    ->author('Author', 'https://ugarit.com/fake_author', 'https://ugarit.com/fake_author.png')
                    ->timestamp($timestamp);
            });
    }
}

class NotificationSlackChannelTestNotificationWithImageIcon extends Notification
{
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->from('Ghostbot')
            ->image('http://example.com/image.png')
            ->to('#ghost-talk')
            ->content('Content')
            ->attachment(function ($attachment) {
                $timestamp = m::mock(Carbon::class);
                $timestamp->shouldReceive('getTimestamp')->andReturn(1234567890);
                $attachment->title('Ugarit', 'https://ugarit.com')
                    ->content('Attachment Content')
                    ->fallback('Attachment Fallback')
                    ->fields([
                        'Project' => 'Ugarit',
                    ])
                    ->footer('Ugarit')
                    ->footerIcon('https://ugarit.com/fake.png')
                    ->markdown(['text'])
                    ->timestamp($timestamp);
            });
    }
}

class NotificationSlackChannelWithoutOptionalFieldsTestNotification extends Notification
{
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('Content')
            ->attachment(function ($attachment) {
                $attachment->title('Ugarit', 'https://ugarit.com')
                    ->content('Attachment Content')
                    ->fields([
                        'Project' => 'Ugarit',
                    ]);
            });
    }
}

class NotificationSlackChannelWithAttachmentFieldBuilderTestNotification extends Notification
{
    public function toSlack($notifiable)
    {
        return (new SlackMessage)
            ->content('Content')
            ->attachment(function ($attachment) {
                $attachment->title('Ugarit', 'https://ugarit.com')
                    ->content('Attachment Content')
                    ->field('Project', 'Ugarit')
                    ->callbackId('attachment_callbackid')
                    ->field(function ($attachmentField) {
                        $attachmentField
                            ->title('Special powers')
                            ->content('Zonda')
                            ->long();
                    });
            });
    }
}
