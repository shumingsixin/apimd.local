<?php

class ApiViewSearch extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Doctors satisfying the search conditions.
    private $pageSize = 100;
    private $doctorSearch;  // DoctorSearch model.
    private $diseaseSearch;  // DiseaseSearch model.
    private $doctors;
    private $diseases;

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->doctorSearch = new DoctorSearchV7($this->searchInputs);
        $this->doctorSearch->addSearchCondition("t.date_deleted is NULL");
        $this->diseaseSearch = new DiseaseSearch($this->searchInputs);
        $this->diseaseSearch->addSearchCondition("t.date_deleted is NULL");
    }

    protected function loadData() {
        // load Doctors.
        $this->loadDoctors();
        $this->loadDiseases();
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

    private function loadDoctors() {
        if (is_null($this->doctors)) {
            $models = $this->doctorSearch->search();
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
        }
    }

    private function loadDiseases() {
        if (is_null($this->diseases)) {
            $models = $this->diseaseSearch->search();
            if (arrayNotEmpty($models)) {
                $this->setDiseases($models);
            }
        }
    }

    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->hpName = $model->getHospitalName();
            $data->hpDeptName = $model->getHpDeptName();
            $data->hpDeptName = $model->getHpDeptName();
            $data->isContracted = $model->getIsContracted();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $this->results->doctors[] = $data;
        }
    }

    private function setDiseases(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $this->results->diseases[] = $data;
        }
    }


}
