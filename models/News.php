<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "news".
 *
 * @property string $content
 * @property integer $id
 * @property string $title
 */
class News extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PUB.news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['content', 'title'], 'string', 'max' => 255],
        ];
    }
	
    public static function primaryKey()
    {
        return ['id'];
    }
	
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'content' => 'Content',
            'id' => 'ID',
            'title' => 'Title',
        ];
    }
}
