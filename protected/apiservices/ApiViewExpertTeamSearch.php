<?php

class ApiViewExpertTeamSearch extends EApiViewService {

    private $searchInputs;      // Search inputs passed from request url.
    private $getCount = false;  // whether to count no. of ExpertTeams satisfying the search conditions.
    private $pageSize = 12;
    private $expertTeamSearch;  // ExpertTeamSearch model.
    private $expertTeams;
    private $expertTeamCount;     // count no. of ExpertTeams.

    public function __construct($searchInputs) {
        parent::__construct();
        $this->searchInputs = $searchInputs;
        $this->getCount = isset($searchInputs['getcount']) && $searchInputs['getcount'] == 1 ? true : false;
        $this->searchInputs['pagesize'] = isset($searchInputs['pagesize']) && $searchInputs['pagesize'] > 0 ? $searchInputs['pagesize'] : $this->pageSize;
        $this->expertTeamSearch = new ExpertTeamSearch($this->searchInputs);
        $this->expertTeamSearch->addSearchCondition("t.date_deleted is NULL");
        $this->results = new stdClass();
    }

    protected function loadData() {
        // load ExpertTeams.
        $this->loadExpertTeams();
        if ($this->getCount) {
            $this->loadExpertTeamCount();
        }
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

    private function loadExpertTeams() {
        $models = $this->expertTeamSearch->search();
        $this->setExpertTeams($models);
    }

    private function setExpertTeams(array $models) {
        $temp = array();
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();

            $data->slogan = $model->getSlogan();
//            $data->desc = $model->getDescription();
            $data->imageUrl = $model->getAppImageUrl();
//            $data->introImageUrl = $model->getBannerUrl();
            $data->actionUrl = Yii::app()->createAbsoluteUrl('api/expertteam/'.$data->id);
//            $data->teamDetailUrl = Yii::app()->createAbsoluteUrl('/mobile/expertteam/detail', array('code' => $data->code));
//            $data->teamUrl = $data->teamPageUrl;
            $data->hpName = $model->getHospitalName();
//            $data->hpDeptName = $model->getHpDeptName();
//            $data->faculty = $model->getFacultyName();
//            $data->facultyName = $data->faculty;
//            $data->disTags = $model->getDisTags();

//            $data->teamLeader = $this->getDoctor($data->leaderId);
//            $data->members = $this->getMembers($model->getOtherMember());
            $temp[] = $data;
        }
        $this->results = $temp;
    }

    private function loadExpertTeamCount() {
        if (is_null($this->results->expertTeamCount)) {
            $count = $this->expertTeamSearch->count();
            $this->setCount($count);
        }
    }

    private function setCount($count) {
        $this->results->expertTeamCount = $count;
    }
}
