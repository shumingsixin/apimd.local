<?php

class ApiViewAppNav1V3 extends EApiViewService {

    private $diseaesUrl;
    private $doctorUrl;
    private $banners;   // clickable slide show
    private $disCategoryList;    // 疾病分类
    private $doctors;   // search hospital by city    

    protected function loadData() {
        // load slideshow banners.
        $this->loadBanners();
        // load Disease Categories.
        $this->loadDiseaseCategoryList();
        // load Doctors.
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'actionUrl' => Yii::app()->createAbsoluteUrl('/api/view', array('model' => 'disease', 'id' => '')),
                'doctorUrl' => Yii::app()->createAbsoluteUrl('/api/list', array('model' => 'doctor', 'getcount' => 1, 'disease' => '')),
                'banners' => $this->banners, // 轮播图
                'disNavs' => $this->disCategoryList, // 疾病分类导航
                'doctors' => $this->doctors             // 医生列表
            );
        }
    }

    private function loadBanners() {
        if (is_null($this->banners)) {
            $this->setBanners();
        }
    }

    private function loadDiseaseCategoryList() {
        if (is_null($this->disCategoryList)) {
            $this->disCategoryList = array();
            $disMgr = new DiseaseManager();
            $models = $disMgr->loadDiseaseCategoryList();
            if (arrayNotEmpty($models)) {
                $this->setDiseaseCategoryList($models);
            }
        }
    }

    private function loadDoctors() {
        //$doctorMgr = new DoctorManager();
        //$query = array('mTitle' => '1', 'limit' => 5, 'order' => 'display_order');
        //$models = $doctorMgr->searchDoctor($query);
        
        /** change 2015-10-28 by QP **/
        $expertteams = ExpertTeam::model()->getAll();
        $doctorIds = arrayExtractValue($expertteams, 'leader_id');
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->addInCondition('t.id', $doctorIds);
        $models = Doctor::model()->findAll($criteria);
        
        
        if (arrayNotEmpty($models)) {
            $this->setDoctors($models);
        }
    }

    private function setBanners() {
        $data = new stdClass();
        $data->pageTitle = '手术直通车';
        //  $data->actionUrl = Yii::app()->createAbsoluteUrl('mobile/home/page', array('view' => 'shoushuzhitongche', 'showBtn' => 0, 'header' => 0, 'footer' => 0));
        $data->actionUrl = Yii::app()->createAbsoluteUrl('mobile/home/page', array('view' => 'shoushuzhitongche', 'addBackBtn' => 0, 'header' => 0, 'footer' => 0));
        $data->imageUrl = 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fshoushuzhitongche.jpg';
        $this->banners[] = $data;
    }

    /**
     * 
     * @param array $models DiseaseCategory.
     */
    private function setDiseaseCategoryList(array $models) {
        $navList = array();
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getCategoryId();
            $data->name = $model->getCategoryName();
            // sub group.
            $subGroup = new stdClass();
            $subGroup->id = $model->getSubCategoryId();
            $subGroup->name = $model->getSubCategoryName();
            $disList = $model->getDiseases();
            if (arrayNotEmpty($disList)) {
                foreach ($disList as $disModel) {
                    $dataDis = new stdClass();
                    $dataDis->id = $disModel->getId();
                    $dataDis->name = $disModel->getName();
                    //    $dataDis->actionUrl = Yii::app()->createAbsoluteUrl('/api/view', array('model' => 'disease', 'id' => $disModel->getId()));
                    $subGroup->diseases[] = $dataDis;
                }
                $data->subCat[] = $subGroup;
            }
            if (isset($navList[$data->id])) {
                $navList[$data->id]->subCat = $data->subCat;
            } else {
                $navList[$data->id] = $data;
            }
        }
        foreach ($navList as $nav) {
            $this->disCategoryList[] = $nav;
        }
    }

    /**
     * 
     * @param array $models Doctor.
     */
    private function setDoctors(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->name = $model->getName();
            $data->mTitle = $model->getMedicalTitle();
            $data->aTitle = $model->getAcademicTitle();
            $data->hpId = $model->getHospitalId();
            $data->hpName = $model->getHospitalName();
            $data->hpDeptId = $model->getHpDeptId();
            $data->hpDeptName = $model->getHpDeptName();
            $data->desc = $model->getDescription();
            $data->imageUrl = $model->getAbsUrlAvatar();
            //   $data->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/home/enquiry', array('doctor' => $data->id));    // @used by app.
            $data->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('did' => $data->id, 'header' => 0));    // @used by app.
            $this->doctors[] = $data;
        }
    }

}
