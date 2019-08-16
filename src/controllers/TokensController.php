<?php


namespace apc\retsrabbit\controllers;

use Craft;

use apc\retsrabbit\RetsRabbit;
use craft\web\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class TokensController extends Controller
{
    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSearch(): Response
    {
        $this->requirePostRequest();

        $tokens       = RetsRabbit::$plugin->getTokens();
        $access_token = $tokens->refresh();

        if($access_token !== null) {
            Craft::$app->session->setNotice(Craft::t('rets-rabbit', 'Token refreshed'));
        } else {
            Craft::$app->session->setNotice(Craft::t('rets-rabbit', 'Could not refresh token'));
        }

        return $this->redirectToPostedUrl();
    }
}