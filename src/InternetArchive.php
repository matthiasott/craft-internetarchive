<?php

namespace matthiasott\internetarchive;

use Craft;
use craft\base\Element;
use craft\base\Event;
use craft\base\Model;
use craft\base\Plugin;
use craft\log\MonologTarget;
use Psr\Log\LogLevel;
use Monolog\Formatter\LineFormatter;

use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\helpers\ElementHelper;

use matthiasott\internetarchive\models\Settings;
use matthiasott\internetarchive\services\InternetArchiveService as InternetArchiveAlias;

/**
 * Internet Archive plugin
 *
 * @method static InternetArchive getInstance()
 * @method Settings getSettings()
 * @author Matthias Ott <mail@matthiasott.com>
 * @copyright Matthias Ott
 * @license MIT
 * @property-read InternetArchiveAlias $internetArchive
 */
class InternetArchive extends Plugin
{
    public string $schemaVersion = '1.0.0';
    public bool $hasCpSection = true;
    public bool $hasCpSettings = true;

    public static function config(): array
    {
        return [
            'components' => ['internetArchive' => InternetArchiveAlias::class],
        ];
    }

    public function init(): void
    {
        parent::init();

        $this->attachEventHandlers();

        Craft::getLogger()->dispatcher->targets[] = new MonologTarget([
            'name' => 'internetarchive',
            'categories' => ['internetarchive'],
            'level' => LogLevel::INFO,
            'logContext' => false,
            'allowLineBreaks' => false,
            'formatter' => new LineFormatter(
                format: "%datetime% %message%\n",
                dateFormat: 'Y-m-d H:i:s',
            ),
        ]);

        // Any code that creates an element query or loads Twig should be deferred until
        // after Craft is fully initialized, to avoid conflicts with other plugins/modules
        Craft::$app->onInit(function() {
            // ...
        });

    }

    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('craft-internetarchive/settings', [
            'plugin' => $this,
            'settings' => $this->getSettings()
        ]);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/5.x/extend/events.html to get started)
        Event::on(Entry::class, Element::EVENT_AFTER_SAVE, function(ModelEvent $event) {
            // @var Entry $entry
            $entry = $event->sender;

            Craft::info("EVENT_AFTER_SAVE", 'internetarchive');
        
            if (ElementHelper::isDraftOrRevision($entry)) {
                return;
            }

            $this->internetArchive->onSaveEntry($event);


            
            // ...
        });
    }
}
