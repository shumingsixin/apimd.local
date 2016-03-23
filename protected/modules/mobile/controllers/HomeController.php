<?php

class HomeController extends MobileController {

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

    public function actionIndex() {

        $apiService = new ApiViewAppNav1V5();
        $data = $apiService->loadApiViewData();
        $this->render('index', array(
            'data' => $data
        ));
    }
    public function actionFindExpertteamByDiseaseId(){
        $this->render('expertteamAndDoctors');
    }

    public function actionSetBrowser($browser) {
        if ($browser == 'pc') {
            $this->setBrowserInSession($browser);
            $this->redirect(Yii::app()->params['baseUrl'] . '/site/index?browser=pc');
        } else {
            $this->setBrowserInSession($browser);
            $this->redirect($this->getHomeUrl());
        }
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        //$this->redirect(array('index'));
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContactus() {
        $model = new ContactForm;

        $this->performAjaxValidation($model);

        $accessAgent = '';
        if (isset($_GET['agent'])) {
            $accessAgent = $_GET['agent'];
        } else {
            $accessAgent = 'app'; //default is app.
        }

        $successMsg = '提交成功！我们会尽快联系您。';

        if (isset($_POST['ContactForm'])) {

            $model->attributes = $_POST['ContactForm'];
            $model->subject = '新的服务咨询';
            if ($model->validate()) {
                $contactus = new Contactus();
                $contactus->attributes = $model->attributes;

                $contactus->access_agent = $accessAgent;
                $contactus->user_ip = $this->getUserIp();
                $contactus->user_agent = Yii::app()->request->getUserAgent();

                if ($contactus->save() === false) {
                    $model->addErrors($contactus->getErrors());
                } else {
                    //send email to inform admin.
                    $emailMgr = new EmailManager();
                    $emailMgr->sendEmailContactUs($contactus);
                }
            }
            /*
              if ($this->isAjaxRequest()) {
              $output = array();
              if ($model->hasErrors()) {
              $output['errors'] = $model->getErrors();
              } else {
              $output['s'] = array($successMsg);
              }
              $this->renderJsonOutput($output);
              } else if ($model->hasErrors() === false) {
              Yii::app()->user->setFlash('contactus', $successMsg);
              $this->refresh();
              }
             * 
             */
            if ($model->hasErrors() === false) {
                Yii::app()->user->setFlash('contactus', $successMsg);
                $this->refresh();
            }
        }

        $this->render('contactus', array('model' => $model));
    }

    public function actionEnquiry() {
        $this->redirect(array('booking/create'));
        
        $form = new ContactEnquiryForm;
        $this->performAjaxValidation($form);
        $accessAgent = '';
        if (isset($_GET['agent'])) {
            $accessAgent = $_GET['agent'];
        } else {
            $accessAgent = 'app'; //default is app.
        }

        if (isset($_POST['ContactEnquiryForm'])) {

            $form->attributes = $_POST['ContactEnquiryForm'];
            $success = false;
            if ($form->validate()) {
                $model = new ContactEnquiry();
                $model->attributes = $form->attributes;

                $model->access_agent = $accessAgent;
                $model->user_ip = $this->getUserIp();
                $model->user_agent = Yii::app()->request->getUserAgent();
                if ($model->save()) {
                    $success = true;
                    //send email to inform admin.
                    $emailMgr = new EmailManager();
                    $emailMgr->sendEmailEnquiry($model);
                } else {
                    $form->addErrors($model->getErrors());
                }
            }

            if ($this->isAjaxRequest()) {
                if ($success) {
                    //do anything here
                    echo CJSON::encode(array(
                        'status' => 'true'
                    ));
                    Yii::app()->end();
                } else {
                    $error = CActiveForm::validate($form);
                    //var_dump($error);exit;
                    if ($error != '[]') {
                        echo $error;
                    }
                    Yii::app()->end();
                }
            } else {
                if ($success) {
                    $this->setFlashMessage('enquiry.success', '提交成功!');
                    $this->refresh();
                }
            }
        }

        $this->render('enquiry', array('model' => $form));
    }

    /**
     * 按照疾病找名医
     */
    public function actionFindDoctor() {
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
        $this->render('findDoctor', array('model' => array_values($navList)));
    }

    protected function performAjaxValidation($model) {
        if (isset($_POST['ajax'])) {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
