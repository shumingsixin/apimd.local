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
class ApiViewDoctorVerified extends EApiViewService {
    private $userId;    // User.id.
    private $userMgr;   // UserManager.
    private $doctorVerified;

    //初始化类的时候将参数注入
    public function __construct($userId) {
        parent::__construct();
        $this->userId = $userId;
        $this->userMgr = new UserManager();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadDoctorVerifiedById();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->doctorVerified,
            );
        }
    }

    private function loadDoctorVerifiedById() {
            $attributes = null;
            $with = null;
            $doctorProflie = $this->userMgr->loadUserDoctorProflieByUserId($this->userId, $attributes, $with);
            $doctorFiles = $this->userMgr->loadUserDoctorFilesByUserId($this->userId, $attributes, $with);
            if($doctorProflie->date_verified !== null){
                $this->doctorVerified = "已认证";
            }
            elseif($doctorProflie->date_verified == null && $doctorFiles[0]->file_url == null){
                $this->doctorVerified = "未认证";
            }
            else{
                $this->doctorVerified = "认证中";
            }
            //echo $doctorVerified;exit;
    }


}
