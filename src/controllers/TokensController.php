<?php


namespace apc\retsrabbit\controllers;

use Craft;

use apc\retsrabbit\RetsRabbit;
use craft\web\Controller;

class TokensController extends Controller
{
    /**
     * @throws \yii\web\BadRequestHttpException
     */
    public function actionSearch(): \yii\web\Response
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