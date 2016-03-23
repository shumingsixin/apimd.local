<?php

class ApiViewHospital extends EApiViewService {

    private $hospitalId;    // Hospital.id
    private $model; // Hospital model
    private $hospital;  // hospital stdClass.
    private $departments;   // hospital departments stdClass.

    public function __construct($hospitalId) {
        parent::__construct();
        $this->hospitalId = $hospitalId;
    }

    protected function loadData() {
        // load Hospital.
        $this->loadHospital();
        // load Hospital Departments.
        $this->loadDepartments();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'hospital' => $this->hospital,
                'deptUrl' => Yii::app()->createAbsoluteUrl('/api/view', array('model' => 'hospitaldept', 'id' => '')),
                'depts' => $this->departments
            );
        }
    }

    private function loadHospital() {
        if (is_null($this->hospital)) {
            $hospitalMgr = new HospitalManager();
            $with = array('hospitalDepartments' => array('on' => 'hospitalDepartments.is_show=1'));
            $hospital = $hospitalMgr->loadHospitalById($this->hospitalId, $with);
            if (isset($hospital)) {
                $this->model = $hospital;
                $this->setHospital($hospital);
            }
        }
    }

    private function loadDepartments() {
        if (is_null($this->departments)) {
            if (isset($this->model->hospitalDepartments)) {
                $this->setDepartments($this->model->hospitalDepartments);
            }
        }
    }

    private function setHospital(Hospital $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();

        $this->hospital = $data;
    }

    private function setDepartments(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $groupName = $model->getGroup();
            $this->departments[$groupName][] = $data;
        }
    }

}
