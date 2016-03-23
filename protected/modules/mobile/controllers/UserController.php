<?php

class UserController extends MobileController {

      /**
     * @return array action filters
     */
    public function filters() {
        return array(
            'accessControl', // perform access control for CRUD operations
            'postOnly + delete', // we only allow deletion via POST request
        );
    }
    
    public function accessRules() {
        return array(
            array('allow', // allow all users to perform 'index' and 'view' actions
                'actions' => array('register', 'ajaxRegister', 'login', 'ajaxForgetPassword'),
                'users' => array('*'),
            ),
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('logout', 'view', 'changePassword'),
                'users' => array('@'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    //进入患者注册页面
    public function actionRegister() {
        $userRole = StatCode::USER_ROLE_PATIENT;
        $form = new UserRegisterForm();
        $form->role = $userRole;
        $form->terms = 1;

        $this->performAjaxValidation($form);
        $this->render('register', array(
            'model' => $form,
        ));
    }

    //无刷新注册
    public function actionAjaxRegister() {
        $output = array('status' => 'no');
        $userRole = StatCode::USER_ROLE_PATIENT;
        $form = new UserRegisterForm();
        $form->role = $userRole;
        $form->terms = 1;
        $this->performAjaxValidation($form);
        if (isset($_POST['UserRegisterForm'])) {
            $values = $_POST['UserRegisterForm'];
            $form->setAttributes($values, true);
            $userMgr = new UserManager();
            $userMgr->registerNewUser($form);
            if ($form->hasErrors() === false) {
                // success                
                $loginForm = $userMgr->autoLoginUser($form->username, $form->password, $userRole, 1);
                $output['status'] = 'ok';
            }
            $output['error'] = $form->getErrors();
        }
        $this->renderJsonOutput($output);
    }

    public function actionView() {
        $user = $this->getCurrentUser();
        $this->render('view', array('user' => $user));
    }

    //登陆
    public function actionLogin() {
        $user = $this->getCurrentUser();
        //用户已登陆 直接进入个人中心
        if (isset($user)) {
            $this->redirect(array('view'));
        }
        $form = new UserDoctorMobileLoginForm();
        $form->role = StatCode::USER_ROLE_PATIENT;
        if (isset($_POST['UserDoctorMobileLoginForm'])) {
            $values = $_POST['UserDoctorMobileLoginForm'];
            $form->setAttributes($values, true);
            $form->autoRegister = true;
            $userMgr = new UserManager();
            $isSuccess = $userMgr->mobileLogin($form);
            if ($isSuccess) {
                $user = $this->getCurrentUser();
                $this->redirect(array('view'));
            }
        }
        //失败 则返回登录页面
        $this->render("login", array(
            'model' => $form
        ));
    }

    //修改密码
    public function actionChangePassword() {
        $user = $this->getCurrentUser();
        $form = new UserPasswordForm('new');
        $form->initModel($user);
        $this->performAjaxValidation($form);
        if (isset($_POST['UserPasswordForm'])) {
            $form->attributes = $_POST['UserPasswordForm'];
            $userMgr = new UserManager();
            $success = $userMgr->doChangePassword($form);
            if ($this->isAjaxRequest()) {
                if ($success) {
                    Yii::app()->user->logout();
                    //do anything here
                    echo CJSON::encode(array(
                        'status' => 'true'
                    ));
                    Yii::app()->end();
                } else {
                    $error = CActiveForm::validate($form);
                    if ($error != '[]') {
                        echo $error;
                    }
                    Yii::app()->end();
                }
            } else {
                if ($success) {
                    Yii::app()->user->logout();
                    $this->setFlashMessage('user.password', '密码修改成功！');
                }
            }
        }
        $this->render('changePassword', array(
            'model' => $form
        ));
    }

    //进入忘记密码页面
    public function actionForgetPassword() {
        $form = new ForgetPasswordForm();
        $this->render('forgetPassword', array(
            'model' => $form,
        ));
    }

    //忘记密码功能
    public function actionAjaxForgetPassword() {
        $output = array('status' => 'no');
        $form = new ForgetPasswordForm();
        if (isset($_POST['ForgetPasswordForm'])) {
            $form->attributes = $_POST['ForgetPasswordForm'];
            if ($form->validate()) {
                $userMgr = new UserManager();
                $user = $userMgr->loadUserByUsername($form->username);
                if (isset($user)) {
                    $success = $userMgr->doResetPassword($user, null, $form->password_new);
                    if ($success) {
                        $output['status'] = 'ok';
                    } else {
                        $output['errors']['errorInfo'] = '密码修改失败!';
                    }
                } else {
                    $output['errors']['username'] = '用户不存在';
                }
            } else {
                $output['errors'] = $form->getErrors();
            }
        }

        $this->renderJsonOutput($output);
    }

    public function actionLogout() {
        Yii::app()->user->logout();
        $this->redirect('login');
    }

}
