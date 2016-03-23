<?php

class ApiViewDoctorByHpDept extends EApiViewService {

    private $limitDoctor;
    private $deptId;
    private $doctors;

    public function __construct($deptId) {
        parent::__construct();
        $this->limitDoctor = 10;
        $this->deptId = $deptId;
    }

    protected function loadData() {
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'doctors' => $this->doctors,
            );
        }
    }

    private function loadDoctors() {
        if (is_null($this->doctors)) {
            $this->doctors = array();
            $doctorMgr = new DoctorManager();
            $options = array('limit' => $this->limitDoctor);
            $doctorList = $doctorMgr->loadAllDoctors(array('hpdept' => $this->deptId), null, $options);
            if (arrayNotEmpty($doctorList)) {
                $this->setDoctors($doctorList);
            }
        }
    }

    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->docName = $data->name;
            $data->hospital = $model->getHospitalName();
            $data->hpName = $data->hospital;
            $data->hpDeptName = $model->getHpDeptName();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $data->imageUrl = $model->getAbsUrlAvatar(false);
            $data->urlImage = $data->imageUrl;
            $data->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/home/enquiry', array('doctor' => $data->id));
            $this->doctors[] = $data;
        }
    }

}
