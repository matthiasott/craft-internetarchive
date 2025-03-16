<?php

namespace matthiasott\internetarchive\jobs;

use Craft;
use craft\queue\BaseJob;
use matthiasott\internetarchive\InternetArchive;

class NotifyInternetArchive extends BaseJob
{
    public string $url;

    protected function defineSettings(): array 
    {
        return array(
            'queue' => AttributeType::Mixed,
            'rows'  => AttributeType::Number
        );
    }

    protected function defaultDescription(): ?string
    {
        return Craft::t('craft-internetarchive', 'Sending URL to the Internet Archive …');
    }

    public function getTotalSteps(): number 
    {
        return $this->getSettings()->rows;
    }

    public function execute($queue): void
    {
        InternetArchive::getInstance()->internetArchive->archiveUrl($this->url);
    }
}
