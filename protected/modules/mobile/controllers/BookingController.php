<?php

class BookingController extends MobileController {

    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }

    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('createCorp', 'ajaxCreateCorp', 'ajaxUploadCorp', 'ajaxUploadFile'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('create', 'view', 'ajaxCreate', 'patientBookingList', 'patientBooking'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionView() {
        $this->render("view");
    }

    /**
     * if neither $did nor $tid is given, do 快速预约
     * @param integer $did  Doctor.id
     * @param integer $tid  Expertteam.id 
     */
    public function actionCreate() {
        $values = $_GET;
        //$request = Yii::app()->request;
        if (isset($values['tid'])) {
            // 预约专家团队
            $form = new BookExpertTeamForm();
            $form->initModel();
            $form->setExpertTeamId($values['tid']);
            $form->setExpertTeamData();
            $userId = $this->getCurrentUserId();
            if (isset($userId)) {
                $form->setUserId($userId);
            }
            //@TEST:
            //     $data = $this->testDataDoctorBook();
            //   $form->setAttributes($data, true);
            if ($this->isUserAgentWeixin()) {
                $form->user_agent = StatCode::USER_AGENT_WEIXIN;
            } else {
                $form->user_agent = StatCode::USER_AGENT_MOBILEWEB;
            }
            if ($this->isUserAgentIOS()) {
                $this->render('bookExpertteam', array('model' => $form));
            } else {
                $this->render('bookExpertteamAndroid', array('model' => $form));
            }
        } elseif (isset($values['did'])) {
            // 预约医生
            $form = new BookDoctorForm();
            $form->initModel();
            $form->setDoctorId($values['did']);
            $form->setDoctorData();
            $userId = $this->getCurrentUserId();
            if (isset($userId)) {
                $form->setUserId($userId);
            }
            //@TEST:
            //234号
            //    $data = $this->testDataDoctorBook();
            //    $form->setAttributes($data, true);
            if ($this->isUserAgentWeixin()) {
                $form->user_agent = StatCode::USER_AGENT_WEIXIN;
            } else {
                $form->user_agent = StatCode::USER_AGENT_MOBILEWEB;
            }
            if ($this->isUserAgentIOS()) {
                $this->render('bookDoctor', array('model' => $form));
            } else {
                $this->render('bookDoctorAndroid', array('model' => $form));
            }
        } else {
            // 快速预约
            $form = new BookQuickForm();
            $form->initModel();
            $userId = $this->getCurrentUserId();
            if (isset($userId)) {
                $form->setUserId($userId);
            }
            if ($this->isUserAgentWeixin()) {
                $form->user_agent = StatCode::USER_AGENT_WEIXIN;
            } else {
                $form->user_agent = StatCode::USER_AGENT_MOBILEWEB;
            }
            //@TEST:
            //    $data = $this->testDataQuickBook();
            //    $form->setAttributes($data, true);
            //操作系统判断 返回不同的页面
            if ($this->isUserAgentIOS()) {
                $this->render('quickbook', array('model' => $form));
            } else {
                $this->render('createAjaxFileUpload', array('model' => $form));
            }
        }
    }

    public function actionAjaxCreate() {
        $output = array('status' => 'no');
        if (isset($_POST['booking'])) {
            $values = $_POST['booking'];
            if (isset($values['expteam_id'])) {
                // 预约专家团队
                $form = new BookExpertTeamForm();
                $form->setAttributes($values, true);
                $form->setExpertTeamData();
                $form->initModel();
                $form->validate();
            } elseif (isset($values['doctor_id'])) {
                // 预约医生
                $form = new BookDoctorForm();
                $form->setAttributes($values, true);
                $form->setDoctorData();
                $form->initModel();
                $form->validate();
            } else {
                // 快速预约
                $form = new BookQuickForm();
                $form->setAttributes($values, true);
                $form->initModel();
                $form->validate();
                //验证码校验
                $authMgr = new AuthManager();
                $authSmsVerify = $authMgr->verifyCodeForBooking($form->mobile, $form->verify_code, null);
                if ($authSmsVerify->isValid() === false) {
                    $form->addError('verify_code', $authSmsVerify->getError('code'));
                }
            }

            //验证码校验
//            $authMgr = new AuthManager();
//            $authSmsVerify = $authMgr->verifyCodeForBooking($form->mobile, $form->verify_code, null);
//            if ($authSmsVerify->isValid() === false) {
//                $form->addError('verify_code', $authSmsVerify->getError('code'));
//            }
            try {
                if ($form->hasErrors() === false) {
                    $booking = new Booking();
                    // 处理booking.user_id
                    $userId = $this->getCurrentUserId();
                    $bookingUser = null;
                    if (isset($userId)) {
                        $bookingUser = $userId;
                        $user = $this->getCurrentUser();
                        $form->mobile = $user->mobile;
                    } else {
                        $mobile = $form->mobile;
                        $user = User::model()->getByUsernameAndRole($mobile, StatCode::USER_ROLE_PATIENT);
                        if (isset($user)) {
                            $bookingUser = $user->getId();
                        } else {
                            // create new user.
                            $userMgr = new UserManager();
                            $user = $userMgr->createUserPatient($mobile);
                            if (isset($user)) {
                                $bookingUser = $user->getId();
                            }
                        }
                    }
                    $booking->setAttributes($form->attributes, true);
                    if ($this->isUserAgentWeixin()) {
                        $booking->user_agent = StatCode::USER_AGENT_WEIXIN;
                    } else {
                        $booking->user_agent = StatCode::USER_AGENT_MOBILEWEB;
                    }
                    $booking->user_id = $bookingUser;
                    if ($booking->save() === false) {
                        $output['errors'] = $booking->getErrors();
                        throw new CException('error saving data.');
                    }
                    //预约单保存成功  生成一张支付单
                    $orderMgr = new OrderManager();
                    $salesOrder = $orderMgr->createSalesOrder($booking);
                    if ($salesOrder->hasErrors() === false) {
                        $output['status'] = 'ok';
                        $output['salesOrderRefNo'] = $salesOrder->getRefNo();
                        $output['booking']['id'] = $booking->getId();
                    } else {
                        $output['errors'] = $salesOrder->getErrors();
                        throw new CException('error saving data.');
                    }
                } else {
                    $output['errors'] = $form->getErrors();
                    throw new CException('error saving data.');
                }
            } catch (CException $cex) {
                $output['status'] = 'no';
            }
        } else {
            $output['error'] = 'missing parameters';
        }
        //$this->renderJsonOutput($output);
        if (isset($_POST['plugin'])) {
            echo CJSON::encode($output);
            Yii::app()->end(200, true); //结束 返回200
        } else {
            $this->renderJsonOutput($output);
        }
    }

    public function actionAjaxUploadFile() {
        $output = array('status' => 'no');
        if (isset($_POST['booking'])) {
            $values = $_POST['booking'];
            $bookingMgr = new BookingManager();
            if (isset($values['id']) === false) {
                // ['patient']['mrid'] is missing.
                $output['status'] = 'no';
                $output['error'] = 'invalid parameters';
                $this->renderJsonOutput($output);
            }
            $bookingId = $values['id'];
            //    $userId = $this->getCurrentUserId();
            $booking = $bookingMgr->loadBookingMobileById($bookingId);
            //$patientMR = $patientMgr->loadPatientMRById($mrid);
            if (isset($booking) === false) {
                // PatientInfo record is not found in db.
                $output['status'] = 'no';
                $output['errors'] = 'invalid id';
                $this->renderJsonOutput($output);
            } else {
                $output['bookingId'] = $booking->getId();
                $ret = $bookingMgr->createBookingFile($booking);
                if (isset($ret['error'])) {
                    $output['status'] = 'no';
                    $output['error'] = $ret['error'];
                    $output['file'] = '';
                } else {
                    // create file output.
                    $fileModel = $ret['filemodel'];
                    $data = new stdClass();
                    $data->id = $fileModel->getId();
                    $data->bookingId = $fileModel->getBookingId();
                    $data->fileUrl = $fileModel->getAbsFileUrl();
                    $data->tnUrl = $fileModel->getAbsThumbnailUrl();
                    //    $data->deleteUrl = $this->createUrl('patient/deleteMRFile', array('id' => $fileModel->getId()));
                    $output['status'] = 'ok';
                    $output['file'] = $data;
                    //$output['redirectUrl'] = $this->createUrl("home/index");
                }
            }
        } else {
            $output['error'] = 'missing parameters';
        }
        if (isset($_POST['plugin'])) {
            echo CJSON::encode($output);
            Yii::app()->end(200, true); //结束 返回200
        } else {
            $this->renderJsonOutput($output);
        }
    }

    //进入公司快速预约页面
    public function actionCreateCorp() {
        $form = new BookCorpForm();
        $returnUrl = 'createCorp';
        if (!$this->isUserAgentIOS()) {
            $returnUrl = $returnUrl . 'Android';
        }
        $this->render($returnUrl, array(
            'model' => $form
        ));
    }

    //ajax 公司快速预约数据保存
    public function actionAjaxCreateCorp() {
        $output = array('status' => 'no');
        if (isset($_POST['booking'])) {
            //给form赋值
            $values = $_POST['booking'];
            $form = new BookCorpForm();
            $form->setAttributes($values, true);
            $form->initModel();
            //数据校验之后再检测验证码
            $form->validate();

            //验证码校验
            $authMgr = new AuthManager();
            $authSmsVerify = $authMgr->verifyCodeForBooking($form->mobile, $form->verify_code, null);
            if ($authSmsVerify->isValid() === false) {
                $form->addError('verify_code', $authSmsVerify->getError('code'));
            }
            //form数据校验
            if ($form->hasErrors() === false) {
                $booking = new Booking();
                $booking->setAttributes($form->attributes, true);
                $booking->setIsCorporate();
                if ($booking->save()) {
                    $output['status'] = 'ok';
                    $output['booking']['id'] = $booking->getId();
                } else {
                    $output['errors'] = $booking->getErrors();
                }
            } else {
                $output['errors'] = $form->getErrors();
            }
        }
        $this->renderJsonOutput($output);
    }

    //上传企业员工证明
    public function actionAjaxUploadCorp() {
        $output = array('status' => 'no');
        if (isset($_POST['booking'])) {
            $values = $_POST['booking'];
            if (isset($values['id']) === false) {
                // ['patient']['mrid'] is missing.
                $output['status'] = 'no';
                $output['error'] = 'invalid parameters';
                $this->renderJsonOutput($output);
            }
            $bookingMgr = new BookingManager();
            $bookingId = $values['id'];
            $booking = $bookingMgr->loadBookingMobileById($bookingId);
            if (isset($booking) === false) {
                // PatientInfo record is not found in db.
                $output['status'] = 'no';
                $output['errors'] = 'invalid id';
                $this->renderJsonOutput($output);
            } else {
                $output['bookingId'] = $booking->getId();
                $ret = $bookingMgr->cerateBookingCorp($booking);
                if (isset($ret['error'])) {
                    $output['status'] = 'no';
                    $output['error'] = $ret['error'];
                    $output['file'] = '';
                } else {
                    // create file output.
                    $fileModel = $ret['filemodel'];
                    $data = new stdClass();
                    $data->id = $fileModel->getId();
                    $data->bookingId = $fileModel->getBookingId();
                    $data->fileUrl = $fileModel->getAbsFileUrl();
                    $data->tnUrl = $fileModel->getAbsThumbnailUrl();
                    //    $data->deleteUrl = $this->createUrl('patient/deleteMRFile', array('id' => $fileModel->getId()));
                    $output['status'] = 'ok';
                    $output['file'] = $data;
                    //$output['redirectUrl'] = $this->createUrl("home/index");
                }
            }
        } else {
            $output['error'] = 'missing parameters';
        }
        if (isset($_POST['plugin'])) {
            echo CJSON::encode($output);
            Yii::app()->end(200, true); //结束 返回200
        } else {
            $this->renderJsonOutput($output);
        }
    }

    //病人预约列表查询
    public function actionPatientBookingList() {
        $user = $this->getCurrentUser();
        $booking = new ApiViewBookingListV4($user);
        $output = $booking->loadApiViewData();
        $this->render('patientBookingList', array(
            'data' => $output
        ));
    }

    //病人续约详细信息查询
    public function actionPatientBooking($id) {
        $user = $this->getCurrentUser();
        $booking = new ApiViewBookingV4($user, $id);
        $output = $booking->loadApiViewData();
        if ($this->isUserAgentIOS()) {
            $this->render('patientBooking', array(
                'data' => $output
            ));
        } else {
            $this->render('patientBookingAndroid', array(
                'data' => $output
            ));
        }
    }

    protected function sendBookingEmailNew($booking) {
        $data = new stdClass();
        $data->id = $booking->getId();
        $data->refNo = $booking->getRefNo();
        if ($booking->bk_type == StatCode::BK_TYPE_EXPERTTEAM) {
            $data->expertBooked = $booking->getExpertteamName();
        } elseif ($booking->bk_type == StatCode::BK_TYPE_DOCTOR) {
            $data->expertBooked = $booking->getDoctorName();
        } else {
            $data->expertBooked = $booking->getDoctorName();
        }
        $data->hospitalName = $booking->getHospitalName();
        $data->hpDeptName = $booking->getHpDeptName();
        $data->patientName = $booking->getContactName();
        $data->mobile = $booking->getMobile();
        $data->diseaseName = $booking->getDiseaseName();
        $data->diseaseDetail = $booking->getDiseaseDetail();
        $data->dateCreated = $booking->getDateCreated();
        $data->submitFrom = '';
        $emailMgr = new EmailManager();
        return $emailMgr->sendEmailBookingNew($data);
    }

    private function testDataQuickBook() {
        return array(
            'hospital_name' => '肿瘤医院',
            'hp_dept_name' => '肿瘤科',
            'doctor_name' => '李医生',
            'contact_name' => '王小明',
            'mobile' => '18217531537',
            'verify_code' => '123456',
            'disease_name' => '小腿骨折',
            'disease_detail' => '小腿都碎了啊！咋办啊'
        );
    }

    private function testDataDoctorBook() {
        return array(
//    'hospital_name' => '肿瘤医院',
//    'hp_dept_name' => '肿瘤科',
//    'doctor_name' => '李医生',
            'contact_name' => '王小明',
            'mobile' => '18217531537',
            'verify_code' => '123456',
            'disease_name' => '小腿骨折',
            'disease_detail' => '小腿都碎了啊！咋办啊'
        );
    }

    private function testDataExpertTeamBook() {
        return array(
//    'hospital_name' => '肿瘤医院',
//    'hp_dept_name' => '肿瘤科',
//    'doctor_name' => '李医生',
            'contact_name' => '王小明',
            'mobile' => '18217531537',
            'verify_code' => '123456',
            'disease_name' => '小腿骨折',
            'disease_detail' => '小腿都碎了啊！咋办啊'
        );
    }

}
