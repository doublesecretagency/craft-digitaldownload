<?php
/**
 * Digital Download plugin for Craft CMS
 *
 * Provide secure digital download links to your files.
 *
 * @author    Double Secret Agency
 * @link      https://www.doublesecretagency.com/
 * @copyright Copyright (c) 2016 Double Secret Agency
 */

namespace doublesecretagency\digitaldownload;

use yii\base\Event;

use Craft;
use craft\base\Plugin;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use doublesecretagency\digitaldownload\models\Settings;
use doublesecretagency\digitaldownload\services\DigitalDownloadService;
use doublesecretagency\digitaldownload\services\Download;
use doublesecretagency\digitaldownload\services\Token;
use doublesecretagency\digitaldownload\variables\DigitalDownloadVariable;
use doublesecretagency\digitaldownload\web\assets\SettingsAssets;

/**
 * Class DigitalDownload
 * @since 2.0.0
 */
class DigitalDownload extends Plugin
{

    /** @var Plugin  $plugin  Self-referential plugin property. */
    public static $plugin;

    /** @var bool  $hasCpSettings  The plugin has a settings page. */
    public $hasCpSettings = true;

    /** @var bool  $schemaVersion  Current schema version of the plugin. */
    public $schemaVersion = '2.0.0';

    /** @inheritDoc */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Load plugin components
        $this->setComponents([
            'digitalDownload'          => DigitalDownloadService::class,
            'digitalDownload_download' => Download::class,
            'digitalDownload_token'    => Token::class,
        ]);

        // Register variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                $variable = $event->sender;
                $variable->set('digitalDownload', DigitalDownloadVariable::class);
            }
        );

        // Register site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $shortPath = $this->getSettings()->shortPath;
                $shortPath = trim($shortPath, ' /');
                if ($shortPath) {
                    $downloadRoute = $shortPath.'/<token:[a-zA-Z0-9]+>';
                    $event->rules[$downloadRoute] = 'digital-download/download/short-path';
                }
            }
        );

    }

    /**
     * @return Settings  Plugin settings model.
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @return string  The fully rendered settings template.
     */
    protected function settingsHtml(): string
    {
        $view = Craft::$app->getView();
        $view->registerAssetBundle(SettingsAssets::class);
        $overrideKeys = array_keys(Craft::$app->getConfig()->getConfigFromFile('digital-download'));
        return $view->renderTemplate('digital-download/settings', [
            'settings' => $this->getSettings(),
            'overrideKeys' => $overrideKeys,
            'docsUrl' => $this->documentationUrl,
        ]);
    }

}
