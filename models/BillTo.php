<?php

namespace app\models;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\Log;
use Yii;

class BillTo extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PUB.BillTo';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'BillToID' => 'BillToID',
            'CustNum' => 'CustNum',
            'Address' => 'Address',
            'Address2' => 'Address2',
            'City' => 'City',
            'Contact' => 'Contact',
            'Name' => 'Name',
            'Phone' => 'Phone',
            'PostalCode' => 'Postal Code',
            'State' => 'State',
        ];
    } 

    public static function primaryKey()
    {
        return ['BillToID'];
    } 

    public function getCustomer()
    {
        return $this->belongsTo(\app\models\Customer::className(), ['BillTo' => 'BillTo']);
    }    

    public static function maxBillToId() {
        $maxId = BillTo::find()
            ->max('BillToID');
        return $maxId;
    }           	
}