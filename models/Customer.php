<?php

namespace app\models;
use app\models\BillTo;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\helpers\Security;
use yii\Log;
use Yii;

class Customer extends ActiveRecord
{
    private $idCache;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'PUB.Customer';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'CustNum' => 'CustNum',
            'Address' => 'Address',
            'Address2' => 'Address2',
            'Balance' => 'Balance',
            'City' => 'City',
            'Comments' => 'Comments',
            'Contact' => 'Contact',
            'Country' => 'Country',
            'CreditLimit' => 'CreditLimit',
            'Discount' => 'Discount',
            'EmailAddress' => 'EmailAddress',
            'Fax' => 'Fax',
            'Name' => 'Name',
            'Phone' => 'Phone',
            'PostalCode' => 'PostalCode',
            'SalesRep' => 'SalesRep',
            'State' => 'State',
            'Terms' => 'Terms',
        ];
    }

    public static function primaryKey()
    {
        return ['CustNum'];
    }

    public function getbillTo()
    {
        return $this->hasMany(\app\models\BillTo::className(), ['CustNum' => 'CustNum']);
    } 

    public static function maxCustNum() {
        $maxId = Customer::find()
            ->max('CustNum');
        return $maxId;
    }

    public function beforeDelete()
    {
        $this->idCache = $this->CustNum;
        return parent::beforeDelete();
    }   

    public function afterDelete()
    {
        $custNum = $this->idCache;
        $children = BillTo::findAll(array('CustNum'=>$custNum));
        foreach ($children as $child)
        {
            $child->delete();
        }
        parent::afterDelete();
    }        
}