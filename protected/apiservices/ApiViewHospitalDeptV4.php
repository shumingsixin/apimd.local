<?php

class ApiViewHospitalDeptV4 extends EApiViewService {

    private $deptId;
    private $department;
    private $doctors;
    private $queryOptions;

    public function __construct($deptId, $queryOptions = null) {
        parent::__construct();
        $this->deptId = $deptId;
        $this->queryOptions = $queryOptions;
        $this->results = new stdClass();
    }

    protected function loadData() {
        // Load hospital department by $deptId.
        $this->loadDepartment();
        // Load doctors by $deptId.
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
//                'status' => self::RESPONSE_OK,
//                'dept' => $this->department,
//                'doctors' => $this->doctors
            );
        }
    }

    private function loadDepartment() {
        if (is_null($this->department)) {
            $hospitalMgr = new HospitalManager();
            $with = array('hpDeptHospital');
            $dept = $hospitalMgr->loadHospitalDeptById($this->deptId, $with);
            if (isset($dept)) {
                $this->setDepartment($dept);
            }
        }
    }

    private function loadDoctors() {
        if (is_null($this->doctors)) {
            $this->doctors = array();
            $doctorMgr = new DoctorManager();
            $searchInputs = $this->queryOptions;
            $searchInputs['hpdept'] = $this->deptId;
            $doctors = $doctorMgr->searchDoctor($searchInputs);
            //$doctors = $doctorMgr->loadAllDoctorsByHpDeptId($this->deptId, null, $this->queryOptions);
            if (arrayNotEmpty($doctors)) {
                $this->setDoctors($doctors);
            }
        }
    }

    private function setDepartment(HospitalDepartment $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $hospital = $model->getHospital();
        if (isset($hospital)) {
            $data->hpName = $hospital->getName();
        } else {
            $data->hpName = '';
        }

        $this->results->department = $data;
    }

    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $doctor = new stdClass();
            $doctor->id = $model->getId();
            $doctor->name = $model->getName();
            $doctor->imageUrl = $model->getAbsUrlAvatar(false);
            $doctor->mTitle = $model->getMedicalTitle();
            $doctor->aTitle = $model->getAcademicTitle();
            $doctor->hpDept = $model->getHpDeptName();
            $doctor->desc = $model->getDescription();
            //    $doctor->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/home/enquiry', array('doctor' => $model->getId()));
            //$doctor->actionUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('did' => $model->getId(), 'header' => 0, 'footer' => 0));            
            $doctor->actionUrl = 'http://mingyizhudao.com/mobile/booking/create/did/' . $doctor->id . '/header/0/footer/0/agent/app';
            $this->results->doctors[] = $doctor;
        }
    }

}
