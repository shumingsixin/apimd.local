<?php

class ApiViewPatientLocalDataV7 extends EApiViewService {
    public function __construct() {
        parent::__construct();
    }

    protected function loadData() {
        $this->loadSendCodeUrl();
        $this->loadLoginUrl();
        $this->loadBookingListUrl();
        $this->loadPaymentUrl();
    }

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

    public function loadSendCodeUrl(){
        $this->setSendCodeUrl(Yii::app()->createAbsoluteUrl('/api2/smsverifycode'));
    }

    private function setSendCodeUrl($data){
        $this->results->sendCodeUrl = $data;
    }

    public function loadLoginUrl(){
        $this->setLoginUrl(Yii::app()->createAbsoluteUrl('/api2/usermobilelogin'));
    }

    private function setLoginUrl($data){
        $this->results->loginUrl = $data;
    }
    public function loadBookingListUrl(){
        $this->setBookingListUrl(Yii::app()->createAbsoluteUrl('/api2/userbooking'));
    }

    private function setBookingListUrl($data){
        $this->results->bookingListUrl = $data;
    }
    public function loadPaymentUrl(){
        $this->setPaymentUrl('http://m.mingyizhudao.com/mobile/order/view?refNo=#&os=#&header=0&footer=0&addBackBtn=0');
    }

    private function setPaymentUrl($data){
        $this->results->paymentUrl = $data;
    }



}
