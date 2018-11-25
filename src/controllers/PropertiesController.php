<?php
/**
 * Rets Rabbit plugin for Craft CMS 3.x
 *
 * Display real estate listings in your craft site in a simple and intuitive way.
 *
 * @link      http://anecka.com
 * @copyright Copyright (c) 2017 Anecka, LLC
 */

namespace apc\retsrabbit\controllers;

use Craft;

use apc\retsrabbit\RetsRabbit;
use craft\web\Controller;

/**
 * Properties Controller
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author APC, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 */
class PropertiesController extends Controller
{
    /**
     * Allow these endpoints to be hit by anonymous users
     *
     * @var array
     */
    protected $allowAnonymous = ['search'];

    /**
     * Handle a POST search by saving params into the DB and redirecting
     * to the search results page.
     *
     * @return mixed
     * @throws \apc\retsrabbit\exceptions\InvalidSearchException
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSearch()
    {
        $this->requirePostRequest();

        $data       = Craft::$app->getRequest()->getBodyParams();
        $resoParams = RetsRabbit::$plugin->getForms()->toReso($data);
        $search     = RetsRabbit::$plugin->getSearches()->newPropertySearch([
            'params' => $resoParams
        ]);

        if (RetsRabbit::$plugin->getSearches()->saveSearch($search)) {
            Craft::$app->session->setNotice(Craft::t('rets-rabbit', 'Search saved'));

            return $this->redirectToPostedUrl(['searchId' => $search->id]);
        }

        Craft::$app->session->setError(Craft::t('rets-rabbit', "Couldn't save search."));
        Craft::$app->urlManager->setRouteParams([
            'search' => $search
        ]);

        return $this->redirectToPostedUrl();
    }
}