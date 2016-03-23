<?php

class ApiViewHospitalByDisease extends EApiViewService {

    private $diseaseId;
    private $hospitals;
    private $limitHospital = 3;
    private $limitDoctor = 4;

    public function __construct($diseaseId) {
        parent::__construct();
        $this->diseaseId = $diseaseId;
    }

    protected function loadData() {
        // load Hospitals.
        $this->loadHospitals();
        // load Hospital's Doctors.
        $this->loadHospitalDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'hospitals' => $this->hospitals
            );
        }
    }

    /**
     * load Hospitals by Disease.id from db.
     */
    private function loadHospitals() {
        if (is_null($this->hospitals)) {
            $this->hospitals = array();
            $hospitalMgr = new HospitalManager();
            $options = array('limit' => $this->limitHospital);
            $disId = $this->diseaseId;
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
        $disId = $this->diseaseId;
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
