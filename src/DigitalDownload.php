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

use Craft;
use craft\base\Model;
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
use Exception;
use yii\base\Event;

/**
 * Class DigitalDownload
 * @since 2.0.0
 *
 * @property DigitalDownloadService $digitalDownload
 * @property Download $digitalDownload_download
 * @property Token $digitalDownload_token
 */
class DigitalDownload extends Plugin
{

    /**
     * @var DigitalDownload Self-referential plugin property.
     */
    public static DigitalDownload $plugin;

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = true;

    /**
     * @inheritdoc
     */
    public string $schemaVersion = '2.1.0';

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Load plugin components
        $this->setComponents([
            'digitalDownload' => DigitalDownloadService::class,
            'digitalDownload_download' => Download::class,
            'digitalDownload_token' => Token::class,
        ]);

        // Register variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            static function (Event $event) {
                $variable = $event->sender;
                $variable->set('digitalDownload', DigitalDownloadVariable::class);
            }
        );

        // Register site routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $shortPath = ($this->getSettings()->shortPath ?? 'download');
                $shortPath = trim($shortPath, ' /');
                if ($shortPath) {
                    $downloadRoute = $shortPath.'/<token:[a-zA-Z0-9]+>';
                    $event->rules[$downloadRoute] = 'digital-download/download/short-path';
                    $event->rules[$shortPath] = 'digital-download/download/short-path';
                }
            }
        );

    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        try {
            $view = Craft::$app->getView();
            $view->registerAssetBundle(SettingsAssets::class);
            $overrideKeys = array_keys(Craft::$app->getConfig()->getConfigFromFile('digital-download'));
            return $view->renderTemplate('digital-download/settings', [
                'settings' => $this->getSettings(),
                'overrideKeys' => $overrideKeys,
                'docsUrl' => $this->documentationUrl,
            ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

}
