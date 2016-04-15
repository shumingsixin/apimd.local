<?php

class ApiViewOrderPayment extends EApiViewService {

    private $order_id;
    private $payment;
    private $totalAmount;
    private $havePay;
    private $notPay;

    public function __construct($id) {
        parent::__construct();
        $this->order_id = $id;
        $this->orderPayment =null;
        $this->havePay =null;
        $this->notPay =null;
    }

    protected function loadData() {
        $this->loadOrderPayment();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                "errorMsg" => "success",
                'results' => $this->results,
            );
        }
    }

    private function loadOrderPayment() {
        $criteria = new CDbCriteria();
        $criteria->compare('t.order_id', $this->order_id);
        $models = SalesPayment::model()->findAll($criteria);
        if (arrayNotEmpty($models)) {
            $this->setOrderPayment($models);
        }
        $this->results->salesPayment = $this->orderPayment;
        $this->results->totalAmount = $this->totalAmount;
        $this->results->havePay = $this->havePay;
        $this->results->notPay = $this->notPay;
    }

    private function setOrderPayment($models) {
        foreach ($models as $model) {
            $array= $model->attributes;
            $data = new stdClass();
            $data->id = $array['id'];
            $data->orderId = $array['order_id'];
            $data->userId = $array['user_id'];
            $data->paymentStatus = $array['payment_status'];
            $data->billAmount = $array['bill_amount'];
            $this->orderPayment[] = $data;
            $this->totalAmount = $this->totalAmount+$array['bill_amount'];
            if($array['payment_status']=='0'){
                $this->havePay = $this->havePay+$array['bill_amount'];
            }
            else{
                $this->notPay = $this->notPay+$array['bill_amount'];
            }
        }
    }
}