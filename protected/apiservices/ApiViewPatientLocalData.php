<?php

class ApiViewPatientLocalData extends EApiViewService {
    public function __construct() {
        parent::__construct();
    }

    protected function loadData() {
        $this->loadSendCodeUrl();
        $this->loadLoginUrl();
        $this->loadBookingListUrl();
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
        $this->setSendCodeUrl(Yii::app()->createAbsoluteUrl('/api/smsverifycode'));
    }

    private function setSendCodeUrl($data){
        $this->results->sendCodeUrl = $data;
    }

    public function loadLoginUrl(){
        $this->setLoginUrl(Yii::app()->createAbsoluteUrl('/api/usermobilelogin'));
    }

    private function setLoginUrl($data){
        $this->results->loginUrl = $data;
    }
    public function loadBookingListUrl(){
        $this->setBookingListUrl(Yii::app()->createAbsoluteUrl('/api/userbooking'));
    }

    private function setBookingListUrl($data){
        $this->results->bookingListUrl = $data;
    }


}
