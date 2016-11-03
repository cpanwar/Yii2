<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use app\models\Customer;
use app\models\BillTo;
use yii\db\Query;

class CustomerController extends Controller
{
    public function actionCreate()
    {
            $model = new Customer();
            $maxCustNum = Customer::maxCustNum();
            $model->CustNum = $maxCustNum + 1;
            $model->Name = 'Riya';
            $model->EmailAddress = 'CSP0021@example.com';
            $model->Address = 'Address123';
            $model->Address2 = 'Address231';
            $model->City = 'New York';
            $model->State = 'New York';
            $model->Phone = '4564567658678';
            $model->Contact = 'q24234234';            
            $model->save();        
        /*
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $model = new Customer();
            $maxCustNum = Customer::maxCustNum();
            $model->CustNum = $maxCustNum + 1;
            $model->Name = 'CSP0021';
            $model->EmailAddress = 'CSP0021@example.com';
            $model->Phone = '87654321';  
            $model->save();
            //echo "<pre>";print_r($model);die;
            if( !$model->save()){
                throw new Exception('Can\'t be saved customer model');
            }            

            $billTo = new BillTo();
            $maxBillId = BillTo::maxBillToId();
            //$billTo->BillToID = $maxBillId + 1;
            $billTo->CustNum = $model->CustNum;
            $billTo->Name = 'Name : '.$billTo->CustNum.'--'.rand();
            $billTo->Contact = 'Contact :'. $billTo->CustNum.'--'.rand();
           // $model->link('billTo', $billTo); //automatically saved into database
            if( !$billTo->save()){
                throw new Exception('Can\'t be saved bill to detail for customer');
            } 
            $transaction->commit();           
        } catch (Exception $e) {
            $transaction->rollBack();
        } 
        */            
        echo "Customer created successfully";
        die;
    }

    public function actionUpdate(){

        $customerArr = Customer::findOne(987654354);
        $custNum = $customerArr['CustNum'];
        //$billToArr = BillTo::findOne($custNum);
        $billToArr = BillTo::findAll(array('CustNum'=>$custNum));
        echo "<br/>custNum==".$custNum;
        echo "<pre>";print_r($billToArr);die;
        $customer->Name = 'chandradeep';

        $customer->update();

        //$customer->update();      
        if ($customer->update() !== false) {
            echo "update successful";
        } else {
            echo "update failed";
        }
    }

    public function actionList(){
        $customer = Customer::find()
                    //->select("'Customer.CustNum', 'BillTo.BillToID'")
                    ->leftJoin('PUB.Billto', "'Billto.CustNum' = 'Customer.CustNum'")
                    ->all();
                    //->LIMIT(5);
        echo "<pre>";print_r($customer);die;

    }  

    public function actionDelete(){
        $transaction = \Yii::$app->db->beginTransaction();
        try {        
            $customer = Customer::findOne(987654355);
            $customer->delete();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        echo "Delete Done successfully";
    }       
}