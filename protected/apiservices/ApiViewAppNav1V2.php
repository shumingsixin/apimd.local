<?php

class ApiViewAppNav1V2 extends EApiViewService {

    private $expertteams;
    private $banners;   // clickable slide show
    private $hospitalLocations; // search hospital by city    

    protected function loadData() {
        // load ExpertTeams.
        $this->loadExpertTeams();
        // load slideshow banners.
        $this->loadBanners();
        // load Hospitals.
        $this->loadHospitalLocations();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'expertteams' => $this->expertteams,
                'banners' => $this->banners,
                'locations' => $this->hospitalLocations
            );
        }
    }

    private function loadExpertTeams() {
        if (is_null($this->expertteams)) {
            $this->expertteams = array();
            $teamMgr = new ExpertTeamManager();
            $expteams = $teamMgr->loadAllExpertTeams();
            if (arrayNotEmpty($expteams)) {
                $this->setExpertTeams($expteams);
            }
        }
    }

    private function loadBanners() {
        if (is_null($this->banners)) {
            $this->setBanners();
        }
    }

    private function loadHospitalLocations() {
        if (is_null($this->hospitalLocations)) {
            $this->hospitalLocations = array();
            $cityList = array(
                array(
                    'id' => 1,
                    'name' => "北京",
                    'imageUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fcity-beijing.jpg',
                    'bgUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fbg-city.jpg'),
                array(
                    'id' => 73,
                    'name' => "上海",
                    'imageUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fcity-shanghai.jpg',
                    'bgUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fbg-city.jpg'),
                array(
                    'id' => 74,
                    'name' => "南京",
                    'imageUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fcity-nanjing.jpg',
                    'bgUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fbg-city.jpg'),
                array(
                    'id' => 87,
                    'name' => "杭州",
                    'imageUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fcity-hangzhou.jpg',
                    'bgUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fbg-city.jpg')
            );
            $this->setHospitalLocations($cityList);
        }
    }

    /**
     * 
     * @param array $models array of ExpertTeam models.
     */
    private function setExpertTeams(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->hpDept = $model->getHpDeptName();
            $data->imageUrl = $model->getAppImageUrl();
            $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/view', array('model' => 'expertteam', 'id' => $model->getId()));
            $this->expertteams[] = $data;
        }
    }

    private function setBanners() {
        $data = new stdClass();
        $data->pageTitle = '手术直通车';
        //  $data->actionUrl = Yii::app()->createAbsoluteUrl('mobile/home/page', array('view' => 'shoushuzhitongche', 'showBtn' => 0, 'header' => 0, 'footer' => 0));
        $data->actionUrl = Yii::app()->createAbsoluteUrl('mobile/home/page', array('view' => 'shoushuzhitongche', 'addBackBtn'=>1));
        $data->imageUrl = 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fshoushuzhitongche.jpg';
        $this->banners[] = $data;
    }

    /**
     * 
     * @param array $cityList
     */
    private function setHospitalLocations(array $cityList) {
        foreach ($cityList as $city) {
            $data = new stdClass();
            $data->id = $city['id'];
            $data->name = $city['name'];
            $data->imageUrl = $city['imageUrl'];
            $data->bgUrl = $city['bgUrl'];
            $data->actionUrl = Yii::app()->createAbsoluteUrl("/api/list", array('model' => 'hospital', 'city' => $data->id));
            $this->hospitalLocations[] = $data;
        }
    }

}
