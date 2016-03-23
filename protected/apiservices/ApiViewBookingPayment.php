<?php

class ApiViewBookingPayment extends EApiViewService {
    private $refno;

    //初始化类的时候将参数注入

    public function __construct($values) {
        parent::__construct();
        $this->bookingNo = $values['bookingNo'];
    }

    protected function loadData() {
        // load Booking by id.
        $this->loadBooking();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    private function loadBooking() {
        $model = Booking::model()->getByRefNo($this->bookingNo);
        if (isset($model)){
            $this->setBookingPayment($model);
        }
    }

    private function setBookingPayment(Booking $model) {
        $orderMgr = new OrderManager();
        $orders = $orderMgr->loadSalesOrderByBkIdAndBkType($model->getId(), StatCode::TRANS_TYPE_BK);
        foreach($orders as $order){
            $data = new stdClass();
            $data->tradeNo = $model->vendor_trade_no;
            $data->orderNo = $order->getRefNo();
            $data->subject = $order->getSubject();
            $data->dateBilled = $order->date_created;
            $data->amountBilled = $order->getFinalAmount() * 100;


            if($order->getIsPaid(false)){
                $data->amountPaid = $order->getFinalAmount() * 100;
                $data->datePaid = $order->getDateClosed();
                $this->results->closed[] = $data;
            }else{
                $this->results->active[] = $data;
            }
        }
    }
}
