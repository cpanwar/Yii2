<?php

namespace app\controllers;
use yii\rest\ActiveController;
use yii\data\Pagination;
use app\models\InvoiceTest;
use app\models\InvoiceTestRef;
use yii\behaviors\AttributeBehavior;
use yii\filters\VerbFilter;
use yii\db\Query;

class InvoiceController extends ActiveController
{
    public $modelClass = 'app\models\InvoiceTest';

/*
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'invoice/create' => ['post'],
                    'invoice/list' => ['get'],
                    'invoice/update' => ['post'],
                ],
            ],
        ];
    } 
  */  
    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset( $actions['create'], $actions['view'], $actions['update'], $actions['delete']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }
   
    public function actionCreate()
    {
        //echo "create"   ;die;
        $postArr = \Yii::$app->request->post();
        echo "<pre>";print_r($postArr);die;
        /*
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
        }*/
    }

    /*
     * Update Action   
    */
    public function actionUpdate(){
            echo "update an existing resource"   ;die;
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
        //echo "list resources page by page";die;
       
        // Code for find all
        return $invoice = InvoiceTest::find(1)
                                ->all();
       // $this->setHeader(200);
        //echo json_encode(array('status'=>1,'data'=>$invoice));                                
        //echo "<pre>";print_r($invoice);
        
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
        echo "delete the specified resource";die;
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

    public function actionView(){
        echo "return the details of a specified resource";die;
    }           

    private function setHeader($status) {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        $content_type="application/json; charset=utf-8";
        header($status_header);
        header('Content-type: ' . $content_type);
    }

    private function _getStatusCodeMessage($status) {
        $codes = array(
                        200 => 'OK',
                        400 => 'Bad Request',
                        401 => 'Unauthorized',
                        402 => 'Payment Required',
                        403 => 'Forbidden',
                        404 => 'Not Found',
                        500 => 'Internal Server Error',
                        501 => 'Not Implemented',
                    );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }        
}