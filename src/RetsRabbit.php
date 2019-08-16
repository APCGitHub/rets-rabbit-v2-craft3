<?php /** @noinspection ALL */

/**
 * Rets Rabbit plugin for Craft CMS 3.x
 *
 * Display real estate listings in your craft site in a simple and intuitive way.
 *
 * @link      http://anecka.com
 * @copyright Copyright (c) 2017 Anecka, LLC
 */

namespace apc\retsrabbit;

use Apc\RetsRabbit\Core\ApiConfig;
use Apc\RetsRabbit\Core\Responses\MultipleListingResponse;
use Apc\RetsRabbit\Core\RetsRabbitApi;
use apc\retsrabbit\models\Settings;
use apc\retsrabbit\traits\PluginTrait;
use apc\retsrabbit\variables\RetsRabbitVariable;
use apc\retsrabbit\twigextensions\RetsRabbitTwigExtension;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;
use yii\di\Container;

/**
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author APC, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 */
class RetsRabbit extends Plugin
{
    use PluginTrait;

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * RetsRabbit::$plugin
     *
     * @var RetsRabbit
     */
    public static $plugin;

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * RetsRabbit::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        \Yii::$container->setSingleton('retsRabbitApi', function ($container, $params, $config) {
            $settings = $this->getSettings();
            $config   = new ApiConfig($settings->apiEndpoint);
            $api      = new RetsRabbitApi($config);

            return $api;
        });

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new RetsRabbitTwigExtension());

        // Register our variables
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('retsRabbit', RetsRabbitVariable::class);
        });

        // Do something after we're installed
        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

        /**
         * Set Plugin Components
         */
        $this->setComponents([
            'apiResponses' => \apc\retsrabbit\services\ApiResponseService::class,
            'cache'        => \apc\retsrabbit\services\CacheService::class,
            'forms'        => \apc\retsrabbit\services\FormsService::class,
            'properties'   => \apc\retsrabbit\services\PropertiesService::class,
            'openHouses'   => \apc\retsrabbit\services\OpenHousesService::class,
            'searches'     => \apc\retsrabbit\services\SearchesService::class,
            'tokens'       => \apc\retsrabbit\services\TokensService::class,
        ]);

        /**
         * Logging in Craft involves using one of the following methods:
         *
         * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
         * Craft::info(): record a message that conveys some useful information.
         * Craft::warning(): record a warning message that indicates something unexpected has happened.
         * Craft::error(): record a fatal error that should be investigated as soon as possible.
         *
         * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
         *
         * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
         * the category to the method (prefixed with the fully qualified class name) where the constant appears.
         *
         * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
         * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
         *
         * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
         */
        Craft::info(
            Craft::t(
                'rets-rabbit',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    /**
     * Create the settings model
     *
     * @return Settings
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @return null|string
     * @throws \Twig_Error_Loader
     * @throws \yii\base\Exception
     */
    protected function settingsHtml(): string
    {
        $valid = RetsRabbit::getInstance()->getTokens()->isValid();

        try {
            /** @var MultipleListingResponse $canHitApi */
            $canHitApi = RetsRabbit::getInstance()->getProperties()->search([
                '$top' => 1
            ])->wasSuccessful();
        } catch (\Exception $e) {
            $canHitApi = false;
        }

        return Craft::$app->getView()->renderTemplate(
            'rets-rabbit/settings',
            [
                'canHitApi'   => $canHitApi,
                'settings'    => $this->getSettings(),
                'tokenExists' => $valid,
            ]
        );
    }
}
