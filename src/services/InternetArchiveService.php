<?php

namespace matthiasott\internetarchive\services;

use Craft;
use craft\elements\Entry;
use craft\helpers\Queue;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use yii\base\Component;

use matthiasott\internetarchive\InternetArchive as Plugin;
use matthiasott\internetarchive\jobs\NotifyInternetArchive;

/**
 * Internet Archive service
 */
class InternetArchiveService extends Component
{

    public Client $client;

    public function init()
    {
        parent::init();

        if (!isset($this->client)) {
            $this->client = Craft::createGuzzleClient();
        }
    }

    /**
     * Get the url for a saved entry and notify the Internet Archive
     *
     * @param Event $event Craft's onSaveEntry event
     *
     */
    public function onSaveEntry($event): void
    {

        $entry = $event->sender;
        $url = $entry->url;

        $settings = Plugin::getInstance()->settings;
        $sendUrls = $settings->sendUrls;

        if (!$sendUrls) {
            Craft::info("Sending turned off", 'internetarchive');
            return;
        }

        if (preg_match("/(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:\/[^\"\'\s]*)?/uix", $url)) {

            Queue::push(new NotifyInternetArchive([
                'url' => $url
            ]));

        }
    }

    /**
     * Sends a URL to the Internet Archive via Guzzle.
     *
     */
    public function archiveUrl($url): bool
    {
        $settings = Plugin::getInstance()->settings;
        $sendUrls = $settings->sendUrls;

        if (!$sendUrls) {
            Craft::info("Sending turned off", 'internetarchive');
            return false;
        }

        $saveUrl = 'https://web.archive.org/save/' . $url;
        
        // Craft::info($url, 'internetarchive');

        try {
            $response = $this->client->get($saveUrl);
        } catch (GuzzleException $e) {
            Craft::info($e->getResponse(), 'internetarchive');
            return false;
        }

        Craft::info($response->getStatusCode(), 'internetarchive');

        return true;

    }

    /**
     * Returns all URLs for all published entries.
     *
     */
    public function getLiveEntryUrls(): array
    {
        $liveEntries = Entry::find()
            ->status('live')
            ->all();

        $urls = [];
        foreach ($liveEntries as $entry) {
            if ($entry->getUrl()) {
                $urls[] = $entry->getUrl();
            }
        }

        return $urls;
    }

    /**
     * Gets all active URLs, fills queue, and creates queue jobs to save the URLs in the Internet Archive
     *
     */
    public function saveAllUrls(): bool
    {

        $urls = $this->getLiveEntryUrls();

        foreach ($urls as $url) {

            if (preg_match("/(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]+-?)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:\/[^\"\'\s]*)?/uix", $url)) {

                // Craft::info($url . "\n", 'internetarchive');
                Queue::push(new NotifyInternetArchive([
                    'url' => $url
                ]));

            }

        }

        return true;

    }
}
