<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of apiViewDoctorHz
 *
 * @author shuming
 */
class apiViewDoctorHz extends EApiViewService {

    private $userId;
    private $userDoctorHz;
    private $userMgr;

    public function __construct($userId) {
        parent::__construct();
        $this->userId = $userId;
        $this->userMgr = new UserManager();
        $this->userDoctorHz = null;
    }

    protected function createOutput() {
        $this->output = array(
            'status' => self::RESPONSE_OK,
            'errorCode' => 0,
            'errorMsg' => 'success',
            'results' => $this->results,
        );
    }

    protected function loadData() {
        $this->loadDoctorHz();
    }

    private function loadDoctorHz() {
        $model = $this->userMgr->loadUserDoctorHuizhenByUserId($this->userId);
        if (isset($model)) {
            $this->setUserDoctorHz($model);
        }
        $this->results->userDoctorHz = $this->userDoctorHz;
    }

    private function setUserDoctorHz(UserDoctorHuizhen $model) {
        $data = new stdClass();
        $data->id = $model->id;
        $data->user_id = $model->user_id;
        $data->is_join = $model->getIsJoin(false);
        $data->travel_duration = $model->travel_duration;
        $data->fee_min = $model->fee_min;
        $data->fee_max = $model->fee_max;
        $data->week_days = $model->getWeekDays();
        $data->patients_prefer = $model->patients_prefer;
        $data->freq_destination = $model->freq_destination;
        $data->destination_req = $model->destination_req;
        $this->userDoctorHz = $data;
    }

}
