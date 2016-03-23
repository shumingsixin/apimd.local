<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ApiViewDoctorInfo
 *
 * @author Administrator
 */
class ApiViewTest extends EApiViewService {


    //初始化类的时候将参数注入
    public function __construct() {
        parent::__construct();

    }

    protected function loadData() {
        // load PatientBooking by creatorId.
//        $this->loadDoctorInfoById();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'doctor' => array(1,2),
            );
        }
    }

    private function loadDoctorInfoById() {
        if (is_null($this->doctorInfo)) {
            $attributes = null;
            $with = null;
            $model = $this->userMgr->loadUserDoctorProflieByUserId($this->userId, $attributes, $with);
            if (isset($model)) {
                $this->setDoctorInfo($model);
            }
        }
    }

    private function setDoctorInfo(UserDoctorProfile $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        if ($model->isVerified()) {
            $data->isVerified = '已认证';
        } else {
            $data->isVerified = '未认证';
        }
        $data->stateName = $model->getStateName();    //省会
        $data->cityName = $model->getCityName();
        $data->hospitalName = $model->getHospitalName();
        $data->hpDeptName = $model->getHpDeptName();    //科室
        $data->cTitle = $model->getClinicalTitle();
        $data->aTitle = $model->getAcademictitle();
        $this->doctorInfo = $data;
    }

}
