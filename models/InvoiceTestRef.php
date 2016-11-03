<?php

namespace app\models;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\Log;
use Yii;

class InvoiceTestRef extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PUB.invoicedemoref';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'item_no' => 'Item Number',
            'NAME' => 'Item Name'
        ];
    } 

    public static function primaryKey()
    {
        return ['id'];
    } 

    public function getInvoice()
    {
        return $this->belongsTo(\app\models\InvoiceTest::className(), ['inv_no' => 'item_no']);
    }    

    public static function getMaxId() {
        $maxId = InvoiceTestRef::find()
            ->max('id');
        return $maxId;
    }           
}