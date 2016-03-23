<?php

class ApiViewExpertTeamV4 extends EApiViewService {

    private $id;
    private $expertTeam;
    public function __construct($id) {
        parent::__construct();
        $this->id = $id;
        $this->results = new stdClass();
    }

    protected function loadData() {
        $this->loadHpTeam();
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                "errorMsg" => "success",
                'results' => $this->results,
            );
        }
    }

    private function loadHpTeam(){
        $expertTeam = ExpertTeam::model()->getById($this->id);
        if (is_null($expertTeam)) {
            $this->throwNoDataException();
        }
        if(is_object($expertTeam)){
            $this->expertTeam = $expertTeam;
            $this->setHpTeam($expertTeam);
        }
    }

    private function loadDoctors(){
        if(is_null($this->expertTeam)){
            $this->expertTeam = ExpertTeam::model()->getById($this->id);
        }
        $this->setData($this->expertTeam->getMembers());
//        $this->setLeader($leader);
//        $this->setMembers($this->expertTeam->getMembers());

    }

    private function setHpTeam(ExpertTeam $model) {
        $data = new stdClass();
        $data->teamId = $model->getId();
        $data->teamName = $model->getName();
        $data->desc = $model->getDescription();
        $data->goodAt = $model->getDisTags();
        //$data->actionUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('tid' => $data->teamId, 'header' => 0, 'footer' => 0));
        $data->actionUrl = "http://mingyizhudao.com/mobile/booking/create/tid/".$data->teamId."/header/0/footer/0/agent/app";
        $this->results->team = $data;
    }
    private function setData(array $models) {
        foreach($models as $key=>$model){
            if($key == 0){
                $this->setLeader($model);
            }else{
                $this->setMember($model);
            }
        }
    }
    private function setLeader(Doctor $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $data->hpId = $model->getHospitalId();
        $data->hpName = $model->getHospitalName();
        $data->mTitle = $model->getMedicalTitle();
        $data->aTitle = $model->getAcademicTitle();
        $data->imageUrl = $model->getAbsUrlAvatar();
        $data->desc = $model->getDescription();
        $data->hpDeptId = $model->getHpDeptId();
        $data->hpDeptName = $model->getHpDeptName();
        $data->hFaculty = $model->getFaculty();
        $data->honour = $model->getHonourList();
//        $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking/', array('doctor' => $data->id));
        $this->results->leader = $data;
    }

    private function setMember(Doctor $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $data->hpId = $model->getHospitalId();
        $data->hpName = $model->getHospitalName();
        $data->mTitle = $model->getMedicalTitle();
        $data->aTitle = $model->getAcademicTitle();
        $data->imageUrl = $model->getAbsUrlAvatar();
        $data->desc = $model->getDescription();
        $data->hpDeptId = $model->getHpDeptId();
        $data->hpDeptName = $model->getHpDeptName();
        $data->hFaculty = $model->getFaculty();
//        $data->honour = $model->getHonourList();
        $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking/', array('doctor' => $data->id));
        $this->results->members[] = $data;
    }

    private function setExpertTeams(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->code = $model->getCode();
            $data->teamId = $data->id;
            $data->teamName = $data->name;
            $data->teamCode = $data->code;
            $data->leaderId = $model->getLeaderId();
            $data->slogan = $model->getSlogan();
            $data->desc = $model->getDescription();
            $data->imageUrl = $model->getAppImageUrl();
            $data->introImageUrl = $model->getBannerUrl();
            $data->teamPageUrl = Yii::app()->createAbsoluteUrl('/expertteam/'.$data->id);
            $data->teamDetailUrl = Yii::app()->createAbsoluteUrl('/mobile/expertteam/detail', array('code' => $data->code));
            $data->teamUrl = $data->teamPageUrl;
            $data->hospital = $model->getHospitalName();
            $data->hpName = $data->hospital;
            $data->hpDeptName = $model->getHpDeptName();
            $data->faculty = $model->getFacultyName();
            $data->facultyName = $data->faculty;
            $data->disTags = $model->getDisTags();

            $data->teamLeader = $this->getDoctor($data->leaderId);
            $data->members = $this->getMembers($model->getOtherMember());
            $this->results->expertTeams[] = $data;
        }
    }

    private function loadExpertTeam() {
        if (is_null($this->expertTeam)) {
            $teamMgr = new ExpertTeamManager();
            $with = array('expteamHospital', 'expteamHpDept', 'expteamCity', 'expteamMembers' => array('with' => 'doctorHospital'),);
            //@TODO: Do not user IExpertTeam. Re-do this method.
            $expteam = $teamMgr->loadIExpertTeamById($this->id, $with);
            if (is_null($expteam)) {
                $this->throwNoDataException();
            }
            $this->setExpertTeam($expteam);
        }
    }

    private function setExpertTeam(IExpertTeam $model) {
        //$model->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/home/enquiry', array('expteam' => $model->id));
        $model->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('tid' => $model->id, 'header' => 0, 'footer' => 0));
        $this->expertTeam = $model;
    }


    private function loadExpertTeamCount() {
        if (is_null($this->results->expertTeamCount)) {
            $count = $this->expertTeamSearch->count();
            $this->setCount($count);
        }
    }

    private function setCount($count) {
        $this->results->expertTeamCount = $count;
    }

    private function getMembers($members){
        $data = array();
        foreach($members as $id){
            $data[] = $this->getDoctor($id);
        }
        return $data;
    }

    private function getDoctor($doctorID) {
        $model = new Doctor;
        $doctor = $model->getById($doctorID);
        $data = new stdClass();
        $data->id = $doctor->getId();
        $data->name = $doctor->getName();
        $data->hospital = $doctor->getHospitalName();
        $data->mTitle = $doctor->getMedicalTitle();
        $data->aTitle = $doctor->getAcademicTitle();
        $data->imageUrl = $doctor->getAbsUrlAvatar();
        $data->mobile = $doctor->getMobile();
        $data->hid = $doctor->getHospitalId();
        $data->desc = $doctor->getDescription();
        $data->hpDeptName = $doctor->getHpDeptName();
        $data->hFaculty = $doctor->getFaculty();
        $data->honour = (array)$doctor->getHonourList();
        return $data;
    }
}
