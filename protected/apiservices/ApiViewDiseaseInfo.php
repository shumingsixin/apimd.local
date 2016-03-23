<?php

class ApiViewDiseaseInfo extends EApiViewService {

    private $diseaseId;
    private $disease;
    private $expertteam;
    private $doctors;

    public function __construct($diseaseId) {
        parent::__construct();
        $this->diseaseId = $diseaseId;
    }

    /**
     * loads data by the given $id (Disease.id).
     * @param integer $diseaeId Disease.id     
     */
    protected function loadData() {
        // load Disease.
        $this->loadDisease();
        // load ExpertTeams.
        //$this->loadExpertTeam();
        // load Doctors.
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'disease' => $this->disease,
                'expertteam' => $this->expertteam,
                'doctors' => $this->doctors
            );
        }
    }

    /**
     * 
     * @param integer $diseaseId
     * @throws CException
     */
    private function loadDisease() {
        if (is_null($this->disease)) {
            $disMgr = new DiseaseManager();
            $disease = $disMgr->loadDiseaseById($this->diseaseId);
            if (is_null($disease)) {
                $this->throwNoDataException();
            }
            $this->setDisease($disease);
        }
    }

    /**
     * @NOTE run this method after $this->disease is loaded.
     * load ExpertTeams by Disease.id from db.
     */
    private function loadExpertTeam() {
        /*
          if (is_null($this->expertteam)) {
          $this->expertteam = array();
          $teamMgr = new ExpertTeamManager();
          //$with = array('expteamMembers');
          $with = array('expteamLeader');
          $options = array('limit' => $this->limitExpteam);
          $disId = $this->disease->id;
          $expteamList = $teamMgr->loadAllExpertTeamsByDiseaseId($disId, $with, $options);
          $expteam = array_shift($expteamList);
          if (isset($expteam)) {
          $this->setExpertTeam($expteam);
          }
          }
         * 
         */
    }

    private function loadDoctors() {
        $doctorMgr = new DoctorManager();
        $query = array('disease' => $this->diseaseId, 'pagesize' => 5, 'order' => 'display_order');
        $models = $doctorMgr->searchDoctor($query);
        if (arrayNotEmpty($models)) {
            $this->setDoctors($models);
        }
    }

    private function setDisease(Disease $model) {
        $d = new stdClass();
        $d->id = $model->getId();
        $d->name = $model->getName();
        $d->desc = $model->getDescription();

        $this->disease = $d;
    }

    /**
     * 
     * @param array $models array of ExpertTeam models.
     */
    private function setExpertTeam(ExpertTeam $model) {

        $team = new stdClass();
        $team->id = $model->getId();
        $team->name = $model->getName();
        $team->slogan = $model->getSlogan();
        $team->hospital = $model->getHospitalName();
        $team->hpDept = $model->getHpDeptName();
        $team->desc = $model->getDescription();
        // Team leader.
        $expteamLeader = $model->getExpteamLeader();
        if (isset($expteamLeader)) {
            $modelDoctor = $expteamLeader;
            $leader = new stdClass();
            $leader->id = $modelDoctor->getId();
            $leader->name = $modelDoctor->getName();
            $leader->imageUrl = $modelDoctor->getAbsUrlAvatar(false);
            $leader->mTitle = $modelDoctor->getMedicalTitle();
            $leader->aTitle = $modelDoctor->getAcademicTitle();
            $leader->hospital = $modelDoctor->getHospitalName();
            $leader->hpDept = $modelDoctor->getHpDeptName();
            $team->leader = $leader;
        }
        $this->expertteam = $team;
    }

    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $data->hpId = $model->getHospitalId();
            $data->hpName = $model->getHospitalName();
            $data->hpDeptId = $model->getHpDeptId();
            $data->hpDeptName = $model->getHpDeptName();
            $data->desc = $model->getDescription();
            $data->imageUrl = $model->getAbsUrlAvatar();
            $data->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/home/enquiry', array('doctor' => $data->id));    // @used by app.
            $this->doctors[] = $data;
        }
    }

}
