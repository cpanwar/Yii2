<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use app\models\Customer;
use app\models\BillTo;
use yii\db\Query;

class CustomerController extends Controller
{
    public function actionIndex()
    {
        /*
        $customer = Customer::findOne(987654343);
        $customer->Name = 'chandradeep';
        //$customer->update();      
        if ($customer->update() !== false) {
            echo "update successful";
        } else {
            echo "update failed";
        }
        */

        
        $model = new Customer();
        //$model->CustNum = 987654347;
        $model->Name = 'CSP002';
        $model->EmailAddress = 'CSP002@example.com';
        $model->Phone = '87654321';
           
        $model->save();

        $billTo = new BillTo();
        //$billTo->BillToID = 46;
       // $billTo->CustNum = 987654342;
        $billTo->Name = 'Name : '.$billTo->CustNum.'--'.rand();
        $billTo->Contact = 'Contact :'. $billTo->CustNum.'--'.rand();
        //$billTo->save();
        $model->link('billTo', $billTo); //automatically saved into database

       // echo '<pre>';print_r($model);
       // echo '<pre>';print_r($billTo);
       // die;
                
        echo "Save Done";
        /*
        $customer = Customer::find()
        ->where(['CustNum' => 69])
        ->one();
        */
        //echo '<pre>';print_r($customer);die;
        /*    
        $query = Customer::find();
        
        $pagination = new Pagination([
            'defaultPageSize' => 5,
            'totalCount' => $query->count(),
        ]);
        

        $customer = $query->orderBy('Name')
//            ->offset($pagination->offset)
//            ->limit($pagination->limit)
            ->all();
        */
        
        //$connection = \Yii::$app->db;    
        
        //$model = $connection->createCommand('SELECT * FROM pub.BillTo');
       // $customer = $model->queryAll();    

/*
        return $this->render('index', [
            'customer' => $customer,
            //'pagination' => $pagination,
        ]);
*/        
    }
}