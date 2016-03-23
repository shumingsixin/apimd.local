<?php

class ApiViewUserSearch extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of Users satisfying the search conditions.
    private $pageSize = 1000000;
    private $userSearch;  // UserSearch model.
    private $users;
    private $userCount;     // count no. of Users.

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->userSearch = new UserSearch($this->searchInputs);
        $this->userSearch->addSearchCondition("t.date_deleted is NULL");
    }

    protected function loadData() {
        // load Users.
        $this->loadUsers();
        if ($this->getCount) {
            $this->loadUserCount();
        }
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->users,
            );
        }
    }

    private function loadUsers() {
        if (is_null($this->users)) {
            $models = $this->userSearch->search();
            if (arrayNotEmpty($models)) {
                $this->setUsers($models);
            }
        }
    }

    private function setUsers(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->mobile = $model->getUsername();
            if (isset($model->userDoctorProfile)) {
                $udp = $model->userDoctorProfile;
                $data->name = $udp->getName();
                $data->cTitle = $udp->getClinicalTitle();
                $data->aTitle = $udp->getAcademicTitle();
                //$data->aTitle=$udp->academic_title;
                $data->hospital = $udp->getHospitalId();
                $data->hpName = $udp->getHospitalName();
                $data->hpDeptId = $udp->getHpDeptId();
                $data->hpDeptName = $udp->getHpDeptName();
                //$data->desc = $udp->getDescription();
                //$data->imageUrl = $udp->getAbsUrlAvatar();
                //    $data->actionUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('did' => $data->id, 'header' => 0, 'footer' => 0));   // @used by app.

                $this->users[] = $data;
            }
        }
    }

    private function loadUserCount() {
        if (is_null($this->userCount)) {
            $count = $this->userSearch->count();
            $this->setCount($count);
        }
    }

    private function setCount($count) {
        $this->userCount = $count;
    }

}
