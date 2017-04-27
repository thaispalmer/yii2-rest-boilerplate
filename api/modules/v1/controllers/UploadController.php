<?php

namespace api\modules\v1\controllers;

use api\modules\v1\models\Upload;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

/**
 * Upload Controller API
 */
class UploadController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Upload';
    public $createScenario = 'create';
    public $updateScenario = 'update';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // Adding Http Bearer Authentication
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'except' => ['options']
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        $actions = parent::actions();
        unset($actions['create']);
        return $actions;
    }

    /**
     * Custom action for creating new uploads
     * @return Upload
     * @throws ServerErrorHttpException
     */
    public function actionCreate()
    {
        $model = new Upload;
        $model->scenario = $this->createScenario;

        $model->id = uniqid();
        $model->file = UploadedFile::getInstanceByName('file');

        if ($model->validate()) {
            $model->extension = $model->file->extension;
            if ($model->save()) {
                $response = Yii::$app->getResponse();
                $response->setStatusCode(201);
            } elseif (!$model->hasErrors()) {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        }
        return $model;
    }
}