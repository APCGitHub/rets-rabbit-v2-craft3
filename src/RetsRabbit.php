<?php
/**
 * Rets Rabbit plugin for Craft CMS 3.x
 *
 * Display real estate listings in your craft site in a simple and intuitive way.
 *
 * @link      http://anecka.com
 * @copyright Copyright (c) 2017 Anecka, LLC
 */

namespace anecka\retsrabbit;

use Craft;

use anecka\retsrabbit\services\RetsRabbitService as RetsRabbitServiceService;
use anecka\retsrabbit\variables\RetsRabbitVariable;
use anecka\retsrabbit\twigextensions\RetsRabbitTwigExtension;

use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\web\twig\variables\CraftVariable;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Anecka, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 *
 * @property  RetsRabbitServiceService $retsRabbitService
 */
class RetsRabbit extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * RetsRabbit::$plugin
     *
     * @var RetsRabbit
     */
    public static $plugin;

    /**
     * Has a control panel
     * @var boolean
     */
    public $hasCpSettings = true;

    // Public Methods
    // =========================================================================

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

        // Add in our Twig extensions
        Craft::$app->view->registerTwigExtension(new RetsRabbitTwigExtension());

        // Register our variables
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('retsRabbit', RetsRabbitVariable::class);
            }
        );

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
            "cache"      => anecka\retsrabbit\services\CacheService::class,
            "forms"      => anecka\retsrabbit\services\FormsService::class,
            "properties" => anecka\retsrabbit\services\PropertiesService::class,
            "searches"   => anecka\retsrabbit\services\SearchesService::class,
            "tokens"     => anecka\retsrabbit\services\TokensService::class,
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
     * @return \anecka\retsrabbit\Settings
     */
    public function createSettingsModel()
    {
        return new \anecka\retsrabbit\Settings();
    }

    // Protected Methods
    // =========================================================================
    protected function settingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('rets-rabbit/settings', [
                'settings' => $this->getSettings()
            ]
        )
    }
}
