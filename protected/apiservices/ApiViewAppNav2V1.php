<?php

class ApiViewAppNav2V1 extends EApiViewService{
    
    protected function loadData() {
        $this->results = new stdClass();

        // load slideshow banners.
        $this->loadBanners();
        // load Disease Categories.
        $this->loadDiseaseCategoryList();
        // load Doctors.
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {

            $this->results->actionUrl = Yii::app()->createAbsoluteUrl('/api/view', array('model' => 'disease', 'id' => ''));
            $this->results->doctorUrl = Yii::app()->createAbsoluteUrl('/api/list', array('model' => 'doctor', 'getcount' => 1, 'disease' => ''));

            $this->output = array(
                'status' => self::RESPONSE_OK,
                'score' => 0,
                'balance' => 0,
                'errorCode' => 0,
                "errorMsg" => "success",
                "resultCount" => 3,
                'results' => $this->results
            );
        }
    }
}
