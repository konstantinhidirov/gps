<?php

namespace app\lib\rest;

use app\models\Device;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\Controller;
use yii\web\IdentityInterface;

class RestController extends Controller
{
    protected $request;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);
        unset($behaviors['verbFilter']);
        unset($behaviors['rateLimiter']);

        $request = Yii::$app->getRequest();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [$request->origin],
                'Access-Control-Request-Headers' => ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization'],
                'Access-Control-Allow-Credentials' => true
            ]
        ];
        $behaviors['authenticator'] = [
            'class' => \yii\filters\auth\CompositeAuth::class,
            'authMethods' => [
                [
                    'class' => \yii\filters\auth\HttpBearerAuth::class,
                ],
                [
                    'class' => \yii\filters\auth\QueryParamAuth::class,
                    'tokenParam' => 'token'
                ]
            ]
        ];
        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (!parent::beforeAction($action))
            return false;
        if (Yii::$app->getRequest()->getMethod() == 'OPTIONS') {
            Yii::$app->response->setStatusCode(200);
            return true;
        }
        return true;
    }

    public function init()
    {
        $this->request = ArrayHelper::merge(
            Yii::$app->request->get(),
            Yii::$app->request->post()
        );
    }

    protected function setMessage($message)
    {
        /** @var $response Response */
        $response = Yii::$app->response;
        $response->setMessage($message);
    }

    /**
     * @return null|Device|IdentityInterface
     */
    protected function getDevice()
    {
        return Yii::$app->user->identity;
    }
}