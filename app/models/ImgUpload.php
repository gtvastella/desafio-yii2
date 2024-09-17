<?php

namespace app\models;

use yii\base\Model;
use app\components\AwsBucket;

class ImgUpload extends Model
{

    public $image;

    public function rules()
    {
        return [
            [['image'], 'file', 
                'skipOnEmpty' => false, 
                'extensions' => 'png, jpg',
                'maxFiles' => 1, 
                'maxSize' => 1024 * 1024 * 2,
                'tooBig' => 'O arquivo deve ter no máximo 2MB.',
                'checkExtensionByMimeType' => false,
                'wrongExtension' => 'Por favor, envie um arquivo de imagem válido (png, jpg).',
            ],
        ];
    }

    /**
     * Attempts to upload the image to AWS S3. If the image passes validation, it sends it to the S3 bucket.
     * If the upload is successful, it returns path to the image in the S3 bucket. Otherwise, it returns false.
     * @return bool|string
     */
    public function upload()
    {
        if (!$this->validate()) {
            return false;
        }
        
        $file = $this->image;
        $key = $file->baseName . '-' . time() . '.' . $file->extension;
        return AwsBucket::upload($file->tempName, $key);

    }
}