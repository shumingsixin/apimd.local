<?php

class ApiViewHospitalSearchV7 extends EApiViewService {

    private $searchInputs;
    private $getCount = false;    // whether to count the total no. of Hospitals satisfying the search conditions.
    private $pageSize = 10;
    private $hospitalSearch;
    private $hospitals;
    private $locations;
    private $cityId;
    private $currentLocation;
    private $count;

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
//        $this->searchInputs['is_show'] = 1;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->hospitalSearch = new HospitalDepartmentSearch($this->searchInputs);
    }

    protected function loadData() {
        // load Hospitals.
        $this->loadHospitals();
        if ($this->getCount) {
            $this->loadCount();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'dataNum' => $this->count,
                'results' => $this->hospitals,
            );
        }
    }

    private function loadHospitals() {
        if (is_null($this->hospitals)) {
            $this->hospitals = array();
            $hospitals = $this->hospitalSearch->search();
            if (arrayNotEmpty($hospitals)) {
                $this->setHospitals($hospitals);
            }
        }
    }

    private function setHospitals(array $models) {
        foreach ($models as $model) {
            $hospital = $model->getHospital();
            $data = new stdClass();
            $data->hospital_id = $hospital->getId();
            $data->name = $hospital->getName();
            $data->imageUrl = $hospital->getAbsUrlAvatar();

            $hospitalDept = $model->getHpDept();
            $data->hp_dept_id = $model->hp_dept_id;
            $data->hp_dept_name = $hospitalDept->getName();
            $data->hp_dept_desc = $hospitalDept->getDescription();
            $this->hospitals[] = $data;
        }
    }

    private function loadCount() {
        if (is_null($this->count)) {
            $count = $this->hospitalSearch->count();
            $this->setCount($count);
        }
    }

    private function setCount($count) {
        $this->count = $count;
    }

}
