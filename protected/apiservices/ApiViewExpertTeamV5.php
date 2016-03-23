<?php

class ApiViewExpertTeamV5 extends EApiViewService {

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
        $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking');
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
        $this->results->members[] = $data;
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
