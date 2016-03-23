<?php

class DiseaseController extends MobileController {

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return array(
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    public function actionView() {
        $this->render('view');
    }

    /**
     * 按照疾病找名医
     */
    public function actionIndex() {
        $disMgr = new DiseaseManager();
        $models = $disMgr->loadDiseaseCategoryList();
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
                    $subGroup->diseases[] = $dataDis;
                }
                $data->subCat[] = $subGroup;
            }
            if (isset($navList[$data->id])) {
                $navList[$data->id]->subCat[] = $data->subCat[0];
            } else {

                $navList[$data->id] = $data;
            }
        }
        $this->render('index', array('model' => array_values($navList)));
    }

}
