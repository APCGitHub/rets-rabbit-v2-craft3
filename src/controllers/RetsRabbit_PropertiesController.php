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
class RetsRabbit_PropertiesController extends Controller
{
	/**
	 * Allow these endpoints to be hit by anonymous users
	 * @var array
	 */
	protected $allowAnonymous = ['actionSearch'];

	/**
	 * Handle a POST search by saving params into the DB and redirecting
	 * to the search results page.
	 * 
	 * @return mixed
	 */
	public function actionSearch()
	{
		$this->requirePostRequest();

		$data = craft()->request->getPost();
		$resoParams = craft()->retsRabbit_forms->toReso($data);
		$search = craft()->retsRabbit_searches->newPropertySearch(array(
			'params' => $resoParams
		));

		if(craft()->retsRabbit_searches->saveSearch($search)) {
			craft()->userSession->setNotice(Craft::t('Search saved'));

			$this->redirectToPostedUrl(array('searchId' => $search->id));
		} else {
			craft()->userSession->setError(Craft::t("Couldn't save search."));
			craft()->urlManager->setRouteVariables(array('search' => $search));
		}
	}
}