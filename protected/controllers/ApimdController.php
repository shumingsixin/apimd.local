<?php

/**
 * doctor app api
 * Class ApimdController
 */
class ApimdController extends Controller {

    // Members
    /**
     * Key which has to be in HTTP USERNAME and PASSWORD headers 
     */
    Const APPLICATION_ID = 'ASCCPE';

    /**
     * Default response format
     * either 'json' or 'xml'
     */
    private $format = 'json';

    /**
     * @return array action filters
     */
    public function filters() {
        return array();
    }

    public function init() {
        //header('Access-Control-Allow-Origin:http://m.mingyizhudao.com'); 
        header('Access-Control-Allow-Origin:http://mingyizhudao.com');    // Cross-domain access.
        header('Access-Control-Allow-Credentials:true');      // 允许携带 用户认证凭据（也就是允许客户端发送的请求携带Cookie）
        return parent::init();
    }

    // Actions
    public function actionList($model) {
        $api = $this->getApiVersionFromRequest();
        // Get the respective model instance
        switch ($model) {
            case 'dataversion'://数据版本号
                if ($api >= 2) {
                    $output = array(
                        'status' => EApiViewService::RESPONSE_OK,
                        'errorCode' => ErrorList::ERROR_NONE,
                        'errorMsg' => 'success',
                        'results' => array(
                            'version' => '20151221',
                            'localdataUrl' => Yii::app()->createAbsoluteUrl('/apimd/localdata'),
                        )
                    );
                } else {
                    $output = array('status' => EApiViewService::RESPONSE_OK, 'errorCode' => ErrorList::ERROR_NONE, 'errorMsg' => 'success', 'results' => '20151124');
                }
                break;
            case 'city'://城市列表
                $city = new ApiViewCity();
                $output = $city->loadApiViewData();
                break;
            case 'localdata'://本地需要缓存的数据
                if ($api >= 2) {
                    $apiService = new ApiViewLocalDataV2();
                } else {
                    $apiService = new ApiViewLocalData();
                }
                $output = $apiService->loadApiViewData();
                break;
            case 'patient'://我的患者列表
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId = $user->getId();
                $hasBooking=$values['hasBooking'];//0 有预约 1 未预约
                if ($api >= 2) {
                    $apiService = new ApiViewPatientListV2($userId,$hasBooking);
                } else {
                    $apiService = new ApiViewPatientList($userId);
                }
                $output = $apiService->loadApiViewData();
                break;
            case 'sendbooking'://发出的预约
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId = $user->getId();
                $status = $values['status'];
                $page = $values['page'];
                //$userId='100370';
                //$status=0;
                $apisvc = new ApiViewSendPatientBookingList($userId, $status,"100",$page);
                $output = $apisvc->loadApiViewData();
                break;
            case 'receivebooking'://收到的预约
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $doctorId = $user->getId();
                $apisvc = new ApiViewReceivePatientBookingList($doctorId,$values['status']);
                //$output =$apisvc->loadApiViewData();
                //print_r(json_decode(json_encode($output),true));exit;
                $output = $apisvc->loadApiViewData();
                break;
            case 'profile'://查看个人信息（基本信息）
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDoctorInfo($user->getId());
                $output = $apiService->loadApiViewData();
                break;
            case 'profilefile'://查看个人信息（实名认证）
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $apiService = new ApiViewDoctorFiles($user->getId());
                $output = $apiService->loadApiViewData();
                break;
            case 'appversion':
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $appMgr = new AppManager();
                $output = $appMgr->loadAppVersionJson($values);
                break;

            case 'patientbookinginfo'://已发出预约详情
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $patientId = $values['id'];
                $creatorId = $user->getId();
                $apiService = new ApiViewPatientInfo($patientId, $creatorId);
                $output = $apiService->loadApiViewData();
                break;

            case 'diseasecategory'://科室分类
                $apiService = new ApiViewDiseaseCategory();
                $output = $apiService->loadApiViewData();
                break;

            case 'contractdoctor'://签约医生
                $values = $_GET;
                //$user = $this->userLoginRequired($values);
                $apiService = new ApiViewDoctorSearch($values);
                //$output = $apiService->loadApiViewData();
                $output = $apiService->loadApiViewData();
                break;
            
            case 'orderpayment'://预约单支付列表
                $values = $_GET;
                if (count($values) > 1) {
                    $apiService = new ApiViewOrderPayment($values);
                    $output = $apiService->loadApiViewData();
                }
                break;
            case 'searchpatient'://搜索患者
                $values = $_GET;
                if (count($values) > 1) {
                    $userId = $this->getCurrentUserId();
                    $name = $values['name'];
                    $apiService = new ApiViewPatientSearch($userId, $name);
                    $output = $apiService->loadApiViewData();
                }
                break;
            case 'specialtopic'://发现
                //$userId = $this->getCurrentUserId();
                $apiService = new ApiViewSpecailTopic();
                $output = $apiService->loadApiViewData();
                break;
            /* case 'indexbanner'://首页轮播图
                //$userId = $this->getCurrentUserId();
                $apiService = new ApiViewSpecailTopic();
                $output = $apiService->loadApiViewData();
                break; */
            case 'indexannouncement'://首页公告
                //$userId = $this->getCurrentUserId();
                $apiService = new ApiViewIndex();
                $output = $apiService->loadApiViewData();
                //print_r(json_decode(json_encode($output,true)));exit;
                break;
            case 'isVerified'://是否实名认证
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId=$user->getId();
                //print_r($userInfo);exit;
                $apiSvc = new ApiViewDoctorVerified($userId);
                $output = $apiSvc->loadApiViewData();
                //$output = $this->encryptOutput($isContract);
                //print_r(json_decode(json_encode($output),true));
                break;
            default:
                // Model not implemented error
                //$this->_sendResponse(501, sprintf('Error: Mode <b>list</b> is not implemented for model <b>%s</b>', $model));
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        // Did we get some results?
        if (empty($output)) {
            // No
            //$this->_sendResponse(200, sprintf('No items where found for model <b>%s</b>', $model));
            $this->_sendResponse(200, sprintf('No result', $model));
        } else {
            /* if($output->status=="ok"){
                if(arrayNotEmpty($output->results)==true){
                    $output->results=[];
                }
            } */
            //print_r(json_decode(json_encode($output),true));exit;
            $output=$this->encryptOutput($output);
            $this->renderJsonOutput($output);
            //  header('Content-Type: text/html; charset=utf-8');
            // var_dump($output);
        }
    }

    public function actionView($model, $id) {
        // Check if id was submitted via GET
        if (isset($id) === false) {
            $this->_sendResponse(500, 'Error: Parameter <b>id</b> is missing');
        }
        $output = null;
        $api = $this->getApiVersionFromRequest();
        
        switch ($model) {
            // Find respective model

            case 'patientinfo'://我的患者详情患者资料和就诊意向
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId = $user->getId();
                $apisvc = new ApiViewPatientInfo($id, $userId);
                //$output = $apisvc->loadApiViewData();
                //print_r(json_decode(json_encode($output),true));
                $output = $this->encryptOutput($apisvc->loadApiViewData());
                break;
            case 'doctorpatientinfo'://收到的患者详情患者资料和就诊意向
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId = $user->getId();
                $apisvc = new ApiViewPatientBookingForDoctor($id, $userId);
                $output = $this->encryptOutput($apisvc->loadApiViewData());
                break;
            case 'patientfile'://我的患者(病历/出院小结)图片
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId = $user->getId();
                $apisvc = new ApiViewFilesOfPatient($id, $userId, $values);
                $output = $this->encryptOutput($apisvc->loadApiViewData());
                break;

            case 'doctorpatientfile'://收到的患者(病历/出院小结)图片
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $creatorId = $values['creatorId'];
                $apisvc = new ApiViewFilesOfPatient($id, $creatorId, $values);
                $output = $this->encryptOutput($apisvc->loadApiViewData());
                break;
            
            case 'doctorinfo'://医生信息
                $values = $_GET;
                //$user = $this->userLoginRequired($values);
                $doctorId = $values['id'];
                $apisvc = new ApiViewDoctor($doctorId);
                $output = $this->encryptOutput($apisvc->loadApiViewData());
                break;
            case 'payorders'://支付页面-分批支付
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $bookingId = $values['id'];
                $orderType = $values['ordertype'];
                $apiSvc = new ApiViewPayOrders($bookingId, $orderType);
                $output = $this->encryptOutput($apiSvc->loadApiViewData());
                //print_r(json_decode(json_encode($output),true));
                break;
            case 'orderview'://预约单详情
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $bookingId = $values['id'];
                $apiSvc = new ApiViewBookOrder($bookingId);
                //$output = $apiSvc->loadApiViewData();
                $output = $this->encryptOutput($apiSvc->loadApiViewData());
                //print_r(json_decode(json_encode($output),true));
                break;
            case 'patientbookingview'://预约单详情（查看全部）
                $values = $_GET;
                $user = $this->userLoginRequired($values);
                $userId=$user->getId();
                $id = $values['id'];
                $apiSvc = new ApiViewDoctorPatientInfo($id, $userId);
                //$output = $apiSvc->loadApiViewData();
                $output = $this->encryptOutput($apiSvc->loadApiViewData());
                //print_r(json_decode(json_encode($output),true));
                break;
            case 'orderpayment'://预约单支付列表
                $values = $_GET;
                if (count($values) > 1) {
                    $apiService = new ApiViewOrderPayment($values);
                    $output = $this->encryptOutput($apiSvc->loadApiViewData());
                    //$output = $apiService->loadApiViewData();
                }
                break;
            default:
                $this->_sendResponse(501, sprintf('Mode <b>view</b> is not implemented for model <b>%s</b>', $model));
                Yii::app()->end();
        }
        // Did we find the requested model? If not, raise an error
        if (is_null($output)) {
            $this->_sendResponse(404, 'No result');
        } else {
            //$this->_sendResponse(200, CJSON::encode($output));
            $this->renderJsonOutput($output);
        }
    }

    public function actionCreate($model) {
        $get = $_GET;
        if (empty($_POST)) {
            // application/json
            //$postData=$this->getPostData();
            $postData=urldecode($this->getPostData());
            $post=$this->decryptInput($postData); 
            //print_r($post);exit;
        } else {
            // application/x-www-form-urlencoded
            //$post = $this->decryptInput($_POST["param"]);
            $post = $this->decryptInput($_POST["name"]);
            //$post = $_POST;
        }
        $api = $this->getApiVersionFromRequest();
        $output = array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Invalid request.');
        switch ($model) {
            // Get an instance of the respective model
            case 'smsverifycode':// 发送验证码
                if (isset($post['smsVerifyCode'])) {
                    $values = $post['smsVerifyCode'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiSendVerifyCode($values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'userlogin'://手机号和验证码登录
                if (isset($post['userLogin'])) {
                    // get user ip from request.
                    $values = $post['userLogin'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiTokenDoctorLoginByMobile($values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'userpawlogin'://手机号密码登录
                if (isset($post['userpawLogin'])) {
                    // get user ip from request.
                    $values = $post['userpawLogin'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $authMgr = new AuthManager();
                    $output = $authMgr->apiTokenDoctorLoginByPaw($values);
                    $output['loginType'] = 'sms';
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'userregister':
                if (isset($post['userregister'])) {
                    $values = $post['userregister'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $userMgr = new UserManager();
                    $output = $userMgr->apiTokenDoctorRegister($values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'patient'://创建患者（患者基本信息）
                if (isset($post['patient'])) {
                    $values = $post['patient'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiCreatePatientInfo($user, $values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'patientfile'://上传患者(病历/出院小结)图片
                if (isset($post['patientFile'])) {
                    $values = $post['patientFile'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);
                    $file = EUploadedFile::getInstanceByName('patientFile[file_data]');  // This supports uploading of ONE file only!
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiCreatePatientFile($user, $values, $file);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'patientbooking'://创建患者预约
                if (isset($post['patientbooking'])) {
                    $values = $post['patientbooking'];
                    //$values=Array ( "patient_id" => "9246", "username" =>"13816439927", "token"=>"8B03B640F780AE5A01ABF0C6988A6247", "doctor_id"=> "3158", "travel_type"=> "1", "detail"=> "测试", "doctorname"=>"测试") ;
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $values['user_agent'] = ($this->isUserAgentIOS()) ? StatCode::USER_AGENT_APP_IOS : StatCode::USER_AGENT_APP_ANDROID;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiCreatePatientBooking($user, $values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'profile'://创建个人信息（基本信息）
                if (isset($post['profile'])) {
                    $values = $post['profile'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiCreateProfile($user, $values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'profilefile'://上传实名认证图片
                if (isset($post['profilefile'])) {
                    $values = $post['profilefile'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);
                    $file = EUploadedFile::getInstanceByName('profilefile[file_data]');  // This supports uploading of ONE file only!
                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiCreateProfileFile($user, $file);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'applycontract'://申请成为签约专家
                if (isset($post['applycontract'])) {
                    $values = $post['applycontract'];
                    $user = $this->userLoginRequired($values);
                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiCreateApplyContract($user, $values);
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'changepassword'://修改密码
                if(isset($post['changepassword'])){
                    $values=$post['changepassword'];
                    //$values=array('changepassword' => array('oldPassword' => '334455','newPassword' => '556677', 'dPassword' => '556677'));
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $userId=$user->getId();
                    //$userId='100370';
                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiChangePassword($values,$userId);
                }else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'forgetpassword'://忘记密码
                if(isset($post['forgetpassword'])){
                    $values=$post['forgetpassword'];
                    //print_r($values);exit;
                    //$values=array('forgetpassword' => array('username' => '13816439927','newPassword' => '556677', 'smscode' => '758941'));
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    //$user = $this->userLoginRequired($values);  // check if doctor has login.
                    //$userId='100400';
                    //$userId=$user->getId();
                    $doctorMgr = new DoctorManager();
                    $mobile=$values['username'];
                    $smsCode=$values['smscode'];
                    $newPass=$values['newPassword'];
                    $output = $doctorMgr->apiForgetPassword($mobile,$smsCode,$newPass,$values['userHostIp']);
                }else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'doctoropinion'://上级医生反馈
                if(isset($post['doctoropinion'])){
                    $values=$post['doctoropinion'];
                    //$values=array("id"=>"50","type"=>"2","accept"=>"0","opinion"=>"","username"=>"13816439927","token"=>"8B03B640F780AE5A01ABF0C6988A6247");
                    $user = $this->userLoginRequired($values);
                    $userId=$user->getId();
                    $doctorMgr = new DoctorManager();
                    $id=$values['id'];
                    $type=$values['type'];
                    $accept=$values['accept'];
                    $opinion=$values['opinion'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $output = $doctorMgr->apiDoctorOpinion($id, $type, $accept, $opinion, $userId);
                }else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
                
                case 'testpost'://上级医生反馈
                    print_r($post);exit;
                    if(isset($post['testpost'])){
                        $values=$post['doctoropinion'];
                        //$values=array("id"=>"50","type"=>"2","accept"=>"0","opinion"=>"","username"=>"13816439927","token"=>"8B03B640F780AE5A01ABF0C6988A6247");
                        $user = $this->userLoginRequired($values);
                        $userId=$user->getId();
                        $doctorMgr = new DoctorManager();
                        $id=$values['id'];
                        $type=$values['type'];
                        $accept=$values['accept'];
                        $opinion=$values['opinion'];
                        $values['userHostIp'] = Yii::app()->request->userHostAddress;
                        $output = $doctorMgr->apiDoctorOpinion($id, $type, $accept, $opinion, $userId);
                    }else {
                        $output['status'] = EApiViewService::RESPONSE_NO;
                        $output['errorCode'] = ErrorList::BAD_REQUEST;
                        $output['errorMsg'] = 'Wrong parameters.';
                    }
                    break;
            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        $this->renderJsonOutput($this->encryptOutput($output));
    }

    public function actionUpdate($model, $id) {
        if (isset($id) === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Error: Parameter <b>id</b> is missing'));
        }
        $get = $_GET;
        if (empty($_POST)) {

            // application/json
            $postData=urldecode($this->getPostData());
            $post=$this->decryptInput($postData);
            //print_r($post);
            //$post = CJSON::decode($this->getPostData());
        } else {
            // application/x-www-form-urlencoded
            //$post = $_POST;
            $post = $this->decryptInput($_POST["name"]);
        }
        $api = $this->getApiVersionFromRequest();
        $output = array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => 'Invalid request.');

        switch ($model) {
            // Get an instance of the respective model
            case 'patient'://患者（患者基本信息）
                if (isset($post['patient'])) {
                    $values = $post['patient'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $patientMgr = new PatientManager();
                    $output = $patientMgr->apiCreatePatientInfo($user, $values, $id);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            case 'profile'://个人信息（基本信息）
                if (isset($post['profile'])) {
                    $values = $post['profile'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    //print_r($values);exit;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiCreateProfile($user, $values, $id);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }
                break;
            
            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        $this->renderJsonOutput($this->encryptOutput($output));
    }

    public function actionDelete($model, $id) {
        $get = $_GET;

        switch ($model) {
            // Get an instance of the respective model
            case 'profilefile'://删除认证图片
                if (isset($get['username']) && isset($get['token'])) {
                    $values['username'] = $get['username'];
                    $values['token'] = $get['token'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.
                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiDelectDoctorCertByIdAndUserId($user, $id);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }

                break;
            case 'patientfile'://删除(病历/出院小结)图片
                if (isset($get['username']) && isset($get['token'])) {
                    $values['username'] = $get['username'];
                    $values['token'] = $get['token'];
                    $values['userHostIp'] = Yii::app()->request->userHostAddress;
                    $user = $this->userLoginRequired($values);  // check if doctor has login.

                    $doctorMgr = new DoctorManager();
                    $output = $doctorMgr->apiDelectPatientFileByIdAndUserId($user, $id);
                } else {
                    $output['errorMsg'] = 'Wrong parameters.';
                }

                break;

            default:
                $this->_sendResponse(501, sprintf('Error: Invalid request', $model));
                Yii::app()->end();
        }
        $this->renderJsonOutput($output);
    }

    private function userLoginRequired($values) {
        if (isset($values['username']) === false || isset($values['token']) === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '没有权限执行此操作'));
        }
        $username = $values['username'];
        $token = $values['token'];
        $authMgr = new AuthManager();
        $authUserIdentity = $authMgr->authenticateDoctorByToken($username, $token);
        if (is_null($authUserIdentity) || $authUserIdentity->isAuthenticated === false) {
            $this->renderJsonOutput(array('status' => EApiViewService::RESPONSE_NO, 'errorCode' => ErrorList::BAD_REQUEST, 'errorMsg' => '用户名或token不正确'));
        }
        return $authUserIdentity->getUser();
    }

    private function _sendResponse($status = 200, $body = '', $content_type = 'text/html') {
        // set the status
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->_getStatusCodeMessage($status);
        header($status_header);
        // and the content type
        header('Content-type: ' . $content_type);

        // pages with body are easy
        if ($body != '') {
            // send the body
            echo $body;
        }
        // we need to create the body if none is passed
        else {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on 
            // (this is an apache directive "ServerSignature On")
            $signature = ($_SERVER['SERVER_SIGNATURE'] == '') ? $_SERVER['SERVER_SOFTWARE'] . ' Server at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'] : $_SERVER['SERVER_SIGNATURE'];

            // this should be templated in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
                <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                    <title>' . $status . ' ' . $this->_getStatusCodeMessage($status) . '</title>
                </head>
                <body>
                    <h1>' . $this->_getStatusCodeMessage($status) . '</h1>
                    <p>' . $message . '</p>
                    <hr />
                    <address>' . $signature . '</address>
                </body>
            </html>';

            echo $body;
        }
        Yii::app()->end();
    }

    private function _getStatusCodeMessage($status) {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            200 => 'OK',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
        );
        return (isset($codes[$status])) ? $codes[$status] : '';
    }

    private function getApiVersionFromRequest() {
        return Yii::app()->request->getParam("api", 3);
    }

}
