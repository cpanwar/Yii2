<?php

namespace app\models;
use app\models\InvoiceTestRef;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\Log;
use Yii;

class InvoiceTest extends ActiveRecord
{
    private $idCache;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PUB.invoicedemo';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'inv_no' => 'Invoice Numer',
            'qty' => 'Quantity'
        ];
    }

    public static function primaryKey()
    {
        return ['inv_no'];
    }

    public function getInvoiceTest()
    {
        return $this->hasMany(\app\models\InvoiceTestRef::className(), ['item_no' => 'inv_no']);
    } 

    public static function getMaxInvoiceNum() {
        $maxId = InvoiceTest::find()
            ->max('inv_no');
        return $maxId;
    }

    public function beforeDelete()
    {
        $this->idCache = $this->inv_no;
        return parent::beforeDelete();
    }   

    public function afterDelete()
    {
        $inv_no = $this->idCache;
        $children = invoicetestref::findAll(array('item_no'=>$inv_no));
        foreach ($children as $child)
        {
            $child->delete();
        }
        parent::afterDelete();
    }        
}