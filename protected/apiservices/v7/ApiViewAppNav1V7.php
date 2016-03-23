<?php

class ApiViewAppNav1V7 extends EApiViewService {

    private $diseaesUrl;
    private $doctorUrl;
    private $banners;   // clickable slide show
    private $disCategoryList;    // 疾病分类
    private $doctors;   // search hospital by city

    protected function loadData() {
        $this->results = new stdClass();

        // load slideshow banners.
        $this->loadBanners();
        // load Disease Categories.
        $this->loadDiseaseCategoryList();
        // load Doctors.
        $this->loadDoctors();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->results->diseaseHeadUrl = Yii::app()->createAbsoluteUrl('/api2/view', array('model'=>'disease', 'id' => ''));
            $this->results->doctorUrl = Yii::app()->createAbsoluteUrl('api2/doctor', array('disease' => ''));
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
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
            $models = $disMgr->loadDiseaseCategoryListV7();
            if (arrayNotEmpty($models)) {
                $this->setDiseaseCategoryList($models);
            }
        }
    }

    private function loadDoctors() {
        //$doctorMgr = new DoctorManager();
        //$query = array('mTitle' => '1', 'limit' => 5, 'order' => 'display_order');
        //$models = $doctorMgr->searchDoctor($query);

        /** change 2015-10-28 by QP * */
//        $expertteams = ExpertTeam::model()->getAll(null, array('order'=> 't.id', 'limit'=>6));
//        $doctorIds = arrayExtractValue($expertteams, 'leader_id');
        /*从配置文件中读取*/
        $doctorList = include dirname(__FILE__) . '/../../config/doctor_list.php';
        $doctorIds = $doctorList[date('w')];
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->addInCondition('t.id', $doctorIds);
        $models = Doctor::model()->findAll($criteria);
        if (arrayNotEmpty($models)) {
            $this->setDoctors($models);
        }
    }

    private function setBanners() {
        $data = array(
            array(
                'pageTitle' => '手术直通车',
                'actionUrl' => 'http://m.mingyizhudao.com/mobile/home/page/view/shoushuzhitongche/agent/app/addBackBtn/0/header/0/footer/0',
                'imageUrl' => 'http://myzd.oss-cn-hangzhou.aliyuncs.com/app%2Fhome%2Fshoushuzhitongche.jpg',
            ),
        );

        $this->results->banners = $data;
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
            $subGroup = new stdClass();
            $subGroup->id = $model->getSubCategoryId();
            $subGroup->name = $model->getSubCategoryName();
            $diseasesList = CategoryDiseaseJoin::model()->getAllBySubCatId($model->getSubCategoryId());

            if (arrayNotEmpty($diseasesList)) {
                foreach ($diseasesList as $disease) {
                    $disModel = $disease->getDisease();
                    $dataDis = new stdClass();
                    $dataDis->id = $disModel->getId();
                    $dataDis->name = $disModel->getName();
                    $subGroup->diseases[] = $dataDis;
                }
            }

            if (isset($navList[$data->id])) {
                $navList[$data->id]->subCat[] = $subGroup;
            } else {

                $navList[$data->id] = $data;
                $navList[$data->id]->subCat[] = $subGroup;
            }
        }
        $this->results->disNavs = array_values($navList);
//        foreach ($navList as $nav) {
//            //$this->disCategoryList[] = $nav;
//            $this->results->disNavs[] = $nav;
//        }
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
            //    $data->actionUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('did' => $data->id, 'header' => 0, 'footer' => 0));
            $data->actionUrl = Yii::app()->createAbsoluteUrl('/api2/booking');
//            $data->actionUrl = Yii::app()->createAbsoluteUrl('/api/booking', array('doctor' => $data->id));    // @used by app.
            //$this->doctors[] = $data;
            $this->results->doctors[] = $data;
        }
    }

}
