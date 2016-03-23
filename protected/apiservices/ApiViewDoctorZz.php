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
class apiViewDoctorZz extends EApiViewService {

    private $userId;
    private $userDoctorZz;
    private $userMgr;

    public function __construct($userId) {
        parent::__construct();
        $this->userId = $userId;
        $this->userMgr = new UserManager();
        $this->userDoctorZz = null;
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
        $this->loadDoctorZz();
    }

    private function loadDoctorZz() {
        $model = $this->userMgr->loadUserDoctorZhuanzhenByUserId($this->userId);
        if (isset($model)) {
            $this->setUserDoctorZz($model);
        }
        $this->results->userDoctorZz = $this->userDoctorZz;
    }

    private function setUserDoctorZz(UserDoctorZhuanzhen $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->user_id = $model->user_id;
        $data->is_join = $model->getIsJoin(false);
        $data->fee = $model->fee;
        //$data->week_days = $model->getWeekDays();
        $data->preferredPatient = $model->preferred_patient;
        $data->prep_days = $model->getPrepDays();
        $this->userDoctorZz = $data;
    }

}
