<?php

class ApiViewIndex extends EApiViewService {

    private $banners;

    public function __construct() {
        parent::__construct();
        $this->results = new stdClass();
    }

    protected function loadData() {
        $this->loadDoctors();
        $this->loadBanners();
        $this->setUrl();
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
        //echo date('w');exit;
            $doctorIds = $this->getDoctorIdArray(date('w'));
            $criteria = new CDbCriteria();
            $criteria->addCondition('t.date_deleted is NULL');
            $criteria->addInCondition('t.id', $doctorIds);
            $models = Doctor::model()->findAll($criteria);
            if (arrayNotEmpty($models)) {
                $this->setDoctors($models);
            }
        }
    

    private function setDoctors(array $models) {
            foreach ($models as $model) {
                $data = new stdClass();
                $data->id = $model->getId();
                $data->name = $model->getName();
                $data->mTitle = $model->getMedicalTitle();
                $data->aTitle = $model->getAcademicTitle();
                //$data->hpId = $model->getHospitalId();
                $data->hpName = $model->getHospitalName();
                //$data->hpDeptId = $model->getHpDeptId();
                $data->hpDeptName = $model->getHpDeptName();
                //$data->desc = $model->getDescription();
                //$data->imageUrl = $model->getAbsUrlAvatar();
                //    $data->actionUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('did' => $data->id, 'header' => 0, 'footer' => 0));
                //$data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking');
    //            $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking', array('doctor' => $data->id));    // @used by app.
                //$this->doctors[] = $data;
                $this->results->doctors[] = $data;
            }
        }
        
    private function loadBanners() {
        if (is_null($this->banners)) {
            $this->setBanners();
        }
    }
    
    private function setBanners() {
        $data = array(
            array(
                'pageTitle' => '名医主刀大事记',
                'actionUrl' => 'http://md.mingyizhudao.com/mobiledoctor/home/page/view/bigEvent',
                'imageUrl' => 'http://md.mingyizhudao.com/themes/md2/images/event/bigEvent/banner.png',
            ),
            array(
                'pageTitle' => '名医主刀入选50强榜单',
                'actionUrl' => 'http://md.mingyizhudao.com/mobiledoctor/home/page/view/newList',
                'imageUrl' => 'http://md.mingyizhudao.com/themes/md2/images/event/newList/banner.png',
            ),
            array(
                'pageTitle' => '达芬奇机器人',
                'actionUrl' => 'http://md.mingyizhudao.com/mobiledoctor/home/page/view/robot',
                'imageUrl' => 'http://md.mingyizhudao.com/themes/md2/images/event/robot/banner.jpg',
            )
        );
    
        $this->results->banners = $data;
    }
    
    private function getDoctorIdArray($day){
        $array = array('1'=>array('112','1177','3149','2979','137'),
                '2'=>array('3007','3087','3146','3055','3110'),
                '3'=>array('1030','3061','3017','3018','3165'),
                '4'=>array('68','3175','65','3100','3025'),
                '5'=>array('290','130','359','3050','3049'),
                '6'=>array('3173','2999','270','3106','3102'),
                '0'=>array('3105','3004','3174','3027','2992')
                /* '1'=>array('3217','1784','3051','3078','1887'),
                   '2'=>array('1624','1750','3069','3220','3232'),
                   '3'=>array('3193','3196','3042','3038','3054'),
                   '4'=>array('3107','3238','2973','3147','949'),
                   '5'=>array('3067','139','2934','1192','481'),
                   '6'=>array('3031','117','3204','3224','3223'),
                   '7'=>array('3244','3180','3080','3074','3122'),
                   *
                   *
                   *
                   */
        );
        return $array[$day];
    }
    
    /*
     * 填充首页 签约专家列表，加入名医公益，了解名医主刀URL 
     */
    private function setUrl() {
        $this->results->publicWelfareUrl = "http://md.mingyizhudao.com/mobiledoctor/home/page/view/mygy";
        $this->results->doctorUrl = Yii::app()->createAbsoluteUrl('/apimd/contractdoctor');;
        $this->results->joinUrl = "http://md.mingyizhudao.com/mobiledoctor/home/page/view/myzd";
    }
}
?>
