<?php

class ApiViewPatientLocalDataV9 extends EApiViewService {
    public function __construct() {
        parent::__construct();
    }

    protected function loadData() {
//        $this->loadSendCodeUrl();
//        $this->loadLoginUrl();
//        $this->loadBookingListUrl();
//        $this->loadPaymentUrl();
    }

    protected function createOutput() {

        if (is_null($this->output)) {
            $this->results->url = array(
                'sendCodeUrl' => Yii::app()->createAbsoluteUrl('/api2/smsverifycode'),
                'loginUrl' => Yii::app()->createAbsoluteUrl('/api2/usermobilelogin'),
                'bookingListUrl' => Yii::app()->createAbsoluteUrl('/api2/userbooking'),
                'hospitaldeptUrl' => Yii::app()->createAbsoluteUrl('/api2/view', array('model'=>'hospitaldept', 'id'=>'')),
                'doctorUrl' => Yii::app()->createAbsoluteUrl('/api2/view', array('model'=>'doctor', 'id'=>'')),
                'hospitalUrl' => Yii::app()->createAbsoluteUrl('/api2/view', array('model'=>'hospital', 'id'=>'')),
                'bookingUrl' => Yii::app()->createAbsoluteUrl('/api2/booking'),
                'bookingfileUrl' => Yii::app()->createAbsoluteUrl('/api2/bookingfile'),

                'paymentUrl' => 'http://m.mingyizhudao.com/mobile/order/view?refNo=#&os=#&header=0&footer=0&addBackBtn=0&app=0',
            );
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
