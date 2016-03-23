<?php

class UserSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        parent::__construct($searchInputs, $with);
        $this->setSelectFields(array('username', 'role', 'last_login_time', 'date_created'));
    }

    public function model() {
        $this->model = new User();
    }

    public function getQueryFields() {
        return array('name', 'mobile', 'role', 'gender', 'hpName', 'hpDeptName', 'cTitle', 'aTitle', 'state', 'city', 'isContracted', 'isVerified');
    }

    public function addQueryConditions() {
//        $this->criteria->addCondition('t.date_deleted is NULL');

        if ($this->hasQueryParams()) {
            $role = StatCode::USER_ROLE_PATIENT;
            if (isset($this->queryParams['mobile'])) {
                $mobile = $this->queryParams['mobile'];
                $this->criteria->compare('t.username', $mobile, true);
            }

            //Search User->userDoctorProfile when User->role = 2
        if (isset($this->queryParams['role'])) {
                $role = $this->queryParams['role'];
                $this->criteria->compare("t.role", $role);
            }
            if ($role == StatCode::USER_ROLE_DOCTOR) {
                $udpAlias = 'udp';
                $this->criteria->with = array('userDoctorProfile' => array('alias' => $udpAlias));
                $this->criteria->distinct = true;
                //医生姓名
                if (isset($this->queryParams['name'])) {
                    $name = $this->queryParams['name'];
                    $this->criteria->compare($udpAlias . ".name", $name, true);
                }
                //临床职称
                if (isset($this->queryParams['cTitle'])) {
                    $clinicalTitle = $this->queryParams['cTitle'];
                    $this->criteria->compare($udpAlias . ".clinical_title", $clinicalTitle);
                }
                //学术职称
                if (isset($this->queryParams['aTitle'])) {
                    $academic_title = $this->queryParams['aTitle'];
                    $this->criteria->compare($udpAlias . ".academic_title", $academic_title);
                }
                //医院名称
                if (isset($this->queryParams['hpName'])) {
                    $hpName = $this->queryParams['hpName'];
                    $this->criteria->compare($udpAlias . ".hospital_name", $hpName, true); // sql like condition
                }
                //医院科室名称
                if (isset($this->queryParams['hpDeptName'])) {
                    $hpDeptName = $this->queryParams['hpDeptName'];
                    $this->criteria->compare($udpAlias . ".hp_dept_name", $hpDeptName, true); // sql like condition
                }
                //所在省名称
                if (isset($this->queryParams['state'])) {
                    $state = $this->queryParams['state'];
                    $this->criteria->compare($udpAlias . ".state", $state, true); // sql like condition
                }
                //所在市名称
                if (isset($this->queryParams['city'])) {
                    $city = $this->queryParams['city'];
                    $this->criteria->compare($udpAlias . ".city", $city, true); // sql like condition
                }
//                //是否签约
//                if (isset($this->queryParams['isContracted']) && $this->queryParams['isContracted'] == 1) {
//                    $this->criteria->addCondition($udpAlias . '.date_contracted IS NOT NULL');
//                }
//                //是否认证
//                if (isset($this->queryParams['isVerified']) && $this->queryParams['isVerified'] == 1) {
//                    $this->criteria->addCondition($udpAlias . '.date_verified IS NOT NULL');
//                }
                //是否签约
                if (isset($this->queryParams['isContracted'])) {
                    $str = '';
                    if($this->queryParams['isContracted'] == 1){
                        $str = 'NOT';
                    }
                    $this->criteria->addCondition($udpAlias . '.date_contracted IS '.$str.' NULL');
                }
                //是否认证
                if (isset($this->queryParams['isVerified'])) {
                    $str = '';
                    if($this->queryParams['isVerified'] == 1){
                        $str = 'NOT';
                    }
                    $this->criteria->addCondition($udpAlias . '.date_verified IS '.$str.' NULL');
                }
            }
        }
    }

}
