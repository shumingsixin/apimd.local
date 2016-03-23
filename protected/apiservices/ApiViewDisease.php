<?php

class ApiViewDisease extends EApiViewService {

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
        //  $this->loadHospitalDoctors();           
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'disease' => $this->disease,
                'expertteams' => $this->expertteams,
                'doctorUrl' => Yii::app()->createAbsoluteUrl('/api/list', array('model' => 'doctor', 'disease' => $this->diseaseId))
                    //    'hospitals' => $this->hospitals
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

    /**
     * @NOTE run this method after $this->disease is loaded.
     * load Hospitals by Disease.id from db.
     */
    private function loadHospitals() {
        if (is_null($this->hospitals)) {
            $this->hospitals = array();
            $hospitalMgr = new HospitalManager();
            $options = array('limit' => $this->limitHospital);
            $disId = $this->disease->id;
            $hospitalList = $hospitalMgr->loadAllHospitalsByDiseaseId($disId, null, $options);
            if (arrayNotEmpty($hospitalList)) {
                $this->setHospitals($hospitalList);
            }
        }
    }

    /**
     * @NOTE run this method after $this->hospitals is loaded.
     * load Doctors by Disease.id and Hospital.id from db.
     */
    private function loadHospitalDoctors() {
        $doctorMgr = new DoctorManager();
        $options = array('limit' => $this->limitDoctor);
        $disId = $this->disease->id;
        if (arrayNotEmpty($this->hospitals)) {
            foreach ($this->hospitals as $hospital) {
                $hpId = $hospital->id;
                $doctors = $doctorMgr->loadAllDoctorsByDiseaseIdAndHospitalId($disId, $hpId, null, $options);
                if (arrayNotEmpty($doctors)) {
                    $this->setDoctors($hpId, $doctors);
                }
            }
        }
    }

    /*
      private function loadHospitalsWithDoctors($diseaseId) {
      $db = Yii::app()->db;
      $sql = "SELECT h.`id` AS hpId, p.`id` AS deptId, dr.`id` AS docId
      FROM hospital h
      LEFT JOIN hospital_department p ON p.`hospital_id` = h.`id`
      LEFT JOIN hospital_dept_doctor_join dpj ON dpj.`hp_dept_id` = p.`id`
      LEFT JOIN doctor dr ON dr.`id` = dpj.`doctor_id`
      LEFT JOIN disease_doctor_join drj ON drj.`doctor_id` = dr.`id`
      WHERE drj.`disease_id`= :diseaseId
      AND h.`id` IN (
      SELECT hospital_id FROM disease_hospital_join WHERE disease_id = :diseaseId ORDER BY display_order
      );";
      $results = $db->createCommand($sql)->query(array(':diseaseId' => $diseaseId));
      foreach ($results as $result) {
      var_dump($result);
      }
      }
     * 
     */

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
    private function setExpertTeams(array $models) {
        foreach ($models as $model) {
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
            $this->expertteams[] = $team;
        }
    }

    /**
     * 
     * @param array $models array of Hospital models.
     */
    private function setHospitals(array $models) {
        foreach ($models as $model) {
            $hospital = new stdClass();
            $hospital->id = $model->getId();
            $hospital->name = $model->getName();
            $hospital->imageUrl = $model->getAbsUrlAvatar(false);
            $hospital->hpClass = $model->getClass();
            $hospital->hpType = $model->getType();
            $hospital->phone = $model->getPhone();

            $this->hospitals[] = $hospital;
        }
    }

    /**
     * 
     * @param integer $hospitalId
     * @param array $models array of Doctor models.
     * @return type
     */
    private function setDoctors($hospitalId, array $models) {
        $hospital = $this->getHospitalById($hospitalId);
        if (is_null($hospital)) {
            return null;
        }
        foreach ($models as $model) {
            $doctor = new stdClass();
            $doctor->id = $model->getId();
            $doctor->name = $model->getName();
            $doctor->imageUrl = $model->getAbsUrlAvatar(false);
            $doctor->mTitle = $model->getMedicalTitle();
            $doctor->aTitle = $model->getAcademicTitle();
            $doctor->hpDept = $model->getHpDeptName();
            $doctor->desc = $model->getDescription();
            $hospital->doctors[] = $doctor;
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
