<?php

namespace api\modules\v1\models;

use Yii;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * This is the model class for table "upload".
 *
 * @property string $id
 * @property string $extension
 * @property string $created_at
 * @property UploadedFile $file
 */
class Upload extends \yii\db\ActiveRecord
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'upload';
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['url'] = function ($model) {
            return $model->getUrl();
        };

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
            [['file'], 'required', 'on' => 'create'],
            [['id', 'extension'], 'required', 'on' => 'update'],
            [['created_at'], 'safe'],
            [['id'], 'string', 'max' => 255],
            [['extension'], 'string', 'max' => 4],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'extension' => 'Extension',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->scenario == 'create')
            $this->created_at = date('Y-m-d H:i:s');

        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->refresh();

        if ($insert) {
            $dir = dirname($this->getPath());

            if (!is_dir($dir))
                mkdir($dir, 0755, true);

            // @TODO: Resize the image
            $this->file->saveAs($this->getPath());
        }
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        $file = $this->getPath();
        if (file_exists($file))
            unlink($file);
    }

    /**
     * Retrieve the uploaded file location on the filesystem
     * @return string Full path to the file
     */
    public function getPath()
    {
        $creation_date = strtotime($this->created_at);
        $year = date('Y', $creation_date);
        $month = date('m', $creation_date);
        return Yii::$app->params['uploadPath'] . $year . '/' . $month . '/' . $this->id . '.' . $this->extension;
    }

    /**
     * Retrieve the uploaded file URL
     * @return string Full URL to the file
     */
    public Function getUrl()
    {
        $creation_date = strtotime($this->created_at);
        $year = date('Y', $creation_date);
        $month = date('m', $creation_date);
        return Url::to(Yii::$app->params['uploadUrl'] . $year . '/' . $month . '/' . $this->id . '.' . $this->extension, true);
    }
}
