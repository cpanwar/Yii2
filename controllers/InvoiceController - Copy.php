<?php

namespace app\controllers;

use yii\web\Controller;
use yii\data\Pagination;
use app\models\InvoiceTest;
use app\models\InvoiceTestRef;
use yii\db\Query;

class InvoiceController extends Controller
{
    public function actionCreate()
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $invoiceModel = new InvoiceTest();
            $maxInvoiceNum = InvoiceTest::getMaxInvoiceNum();

            $invoiceModel->inv_no = $maxInvoiceNum + 1;
            $invoiceModel->qty = rand();
            $invoiceModel->save();

            $invoiceModelRef = new InvoiceTestRef();
            $maxId = InvoiceTestRef::getMaxId(); 
            $invoiceModelRef->id = $maxId + 1;
            $invoiceModelRef->item_no = $invoiceModel->inv_no;  

            $invoiceModel->link('invoiceTest', $invoiceModelRef); //automatically saved into database 
            $transaction->commit();       
            echo "Customer created successfully";
            die;
        } catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public function actionUpdate(){
            $invoice = InvoiceTest::findOne(1);
            $findAllInvoiceRef = InvoiceTestRef::findAll(array('item_no' => $invoice['inv_no']));
            foreach($findAllInvoiceRef as $key=>$value)  { 
                    echo "Inside Update" . PHP_EOL;
                    $invoiceModelRef = InvoiceTestRef::findOne($value['id']);
                    $invoiceModelRef->NAME = "chandradeep";                     
                    $invoiceModelRef->update(); 
            }                   
            echo "\nupdate" . PHP_EOL;
    }

    public function actionList(){

        // Code for find all
        $invoice = InvoiceTest::find(1)
                                ->all();
        echo "<pre>";print_r($invoice);
        /*
        // Code for fine one                                
        $invoice = InvoiceTest::find(1)
                                ->one();                             
        */  
        // Code for Join Query
        /*
        $query = InvoiceTest::find(1)
         ->leftJoin('nvoiceTest', "'invoicedemoref.item_no' = 'InvoiceTest.inv_no'")
         ->all();
        */ 
    }  

    public function actionDelete(){
        // Code for when there is foreign key concept
        $transaction = \Yii::$app->db->beginTransaction();
        try {        
            $invoiceRef = InvoiceTestRef::findAll($arrayName = array('item_no' => 1));
            foreach ($invoiceRef as $childInvocie)
            { 
                $childInvocie->delete();
            }
            $invoice = InvoiceTest::findOne(1);
            $invoice->delete();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }

        /* 
        //Code for when there is no foreign key concept, but parent child relation is there
        $transaction = \Yii::$app->db->beginTransaction();
        try {        
            $invoice = InvoiceTest::findOne(1);
            $invoice->delete();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        */        
        echo "Delete Done successfully";
    }       
}