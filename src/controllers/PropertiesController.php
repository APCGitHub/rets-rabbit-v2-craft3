<?php
/**
 * Rets Rabbit plugin for Craft CMS 3.x
 *
 * Display real estate listings in your craft site in a simple and intuitive way.
 *
 * @link      http://anecka.com
 * @copyright Copyright (c) 2017 Anecka, LLC
 */

namespace anecka\retsrabbit\controllers;

use Craft;

use anecka\retsrabbit\RetsRabbit;
use craft\web\Controller;

/**
 * Properties Controller
 *
 * https://craftcms.com/docs/plugins/controllers
 *
 * @author    Anecka, LLC
 * @package   RetsRabbit
 * @since     1.0.0
 */
class PropertiesController extends Controller
{
	/**
	 * Allow these endpoints to be hit by anonymous users
	 * @var array
	 */
	protected $allowAnonymous = ['search'];

	/**
	 * Handle a POST search by saving params into the DB and redirecting
	 * to the search results page.
	 * 
	 * @return mixed
	 */
	public function actionSearch()
	{
		$this->requirePostRequest();

		$data = Craft::$app->getRequest();
		$resoParams = RetsRabbit::$plugin->forms->toReso($data);
		$search = RetsRabbit::$plugin->newPropertySearch(array(
			'params' => $resoParams
		));

		if(RetsRabbit::$plugin->saveSearch($search)) {
			Craft::$app->user->setNotice(Craft::t('rets-rabbit', 'Search saved'));

			return $this->redirectToPostedUrl(array('searchId' => $search->id));
		} else {
			Craft::$app->user->setError(Craft::t('rets-rabbit', "Couldn't save search."));

			Craft::$app->urlManager->setRouteVariables(array(
				'search' => $search
			));
		}
	}
}