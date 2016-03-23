<?php

class ApiViewDiseaseV4 extends EApiViewService {

    private $diseaseId;
    private $disease;
    private $expertteams;
    private $hospitals;
    private $doctorUrl;
    private $limitExpteam = 1;
    private $limitHospital = 3;
    private $limitDoctor = 4;

    public function __construct($diseaseId) {
        parent::__construct();
        $this->diseaseId = $diseaseId;
        $this->results = new stdClass();
    }

    /**
     * loads data by the given $id (Disease.id).
     * @param integer $diseaeId Disease.id     
     */
    protected function loadData() {
        // load Disease.
        $this->loadDisease();
        // load ExpertTeams.
        $this->loadExpertTeams();
        // load Hospitals.
        //  $this->loadHospitals();
        // load Hospital's Doctors.
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
    private function loadExpertTeams() {
        if (is_null($this->expertteams)) {
            $this->expertteams = array();
            $teamMgr = new ExpertTeamManager();
            //$with = array('expteamMembers');
            $with = array('expteamLeader');
            $options = array('limit' => $this->limitExpteam);
            $disId = $this->disease->id;
            $expteamList = $teamMgr->loadAllExpertTeamsByDiseaseId($disId, $with, $options);

            if (arrayNotEmpty($expteamList)) {
                $this->setExpertTeams($expteamList);
            }
        }
    }

    private function setDisease(Disease $model) {
        $d = new stdClass();
        $d->id = $model->getId();
        $d->name = $model->getName();
        $d->desc = $model->getDescription();

        $this->disease = $d;
        $this->results->disease = $d;
    }

    /**
     * 
     * @param array $models array of ExpertTeam models.
     */
    private function setExpertTeams(array $models) {
        foreach ($models as $model) {
            $team = new stdClass();
            $team->id = $model->getId();
            $team->name = $model->getName();
            $team->slogan = $model->getSlogan();
            $team->hospital = $model->getHospitalName();
            $team->hpDept = $model->getHpDeptName();
            $team->desc = $model->getDescription();
            $team->actionUrl = Yii::app()->createAbsoluteUrl('/api/expertteam/'.$team->id.'?api=4');
            $this->results->team = $team;

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
                $leader->honour = (array)$modelDoctor->getHonourList();

                $this->results->leader = $leader;
            }
            // Team leader & members.
            /*
              if (isset($model->expteamMembers)) {
              foreach ($model->expteamMembers as $modelDoctor) {
              $teamMember = new stdClass();
              $teamMember->id = $modelDoctor->getId();
              $teamMember->name = $modelDoctor->getName();
              $teamMember->imageUrl = $modelDoctor->getAbsUrlAvatar(false);
              $teamMember->mTitle = $modelDoctor->getMedicalTitle();
              $teamMember->aTitle = $modelDoctor->getAcademicTitle();
              $teamMember->hospital = $modelDoctor->getHospitalName();
              $teamMember->hpDept = $modelDoctor->getHpDeptName();

              if ($teamMember->id == $leaderId) {
              // set as team leader.
              $team->leader = $teamMember;
              } else {
              // add to team members.
              $team->members[] = $teamMember;
              }
              }
              }
             * 
             */
//            $this->expertteams[] = $team;

        }
    }

    private function getHospitalById($hpId) {
        foreach ($this->hospitals as $hospital) {
            if ($hospital->id == $hpId) {
                return $hospital;
            }
        }
        return null;
    }

}
