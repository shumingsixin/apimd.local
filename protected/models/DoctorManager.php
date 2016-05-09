<?php

class DoctorManager {
    /*     * ****** Api 3.0 ******* */

    public function searchDoctor($searchInputs) {
        $doctorSearch = new DoctorSearch($searchInputs);
        return $doctorSearch->search();
    }

    /*
      public function createDoctorProfileForm(array $attributes, $validate = true) {
      $form = new UserDoctorProfileForm();
      //$form->initModel();
      $form->setAttributes($attributes, true);
      if ($validate) {
      $form->validate();
      }
      return $form;
      }
     * 
     */

    /*
      public function createDoctorProfile($attributes) {
      $doctor = new Doctor();
      $doctor->setAttributes($attributes, true);
      $doctor->save();
      return $doctor;
      }
     * 
     */

    /*     * ****** Api 2.0 ******* */

    public function loadAllIDoctors($query, $with = null, $options = null) {
        $imodelList = array();
        $modelList = $this->loadAllDoctors($query, $with, $options);
        if (arrayNotEmpty($modelList)) {
            foreach ($modelList as $model) {
                $imodelList[] = $this->convertToIDoctor($model, null, $with);
            }
        }
        return $imodelList;
    }

    public function loadAllDoctors($query = null, $with = null, $options = null) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        // building dynamic query string.
        if (isset($query['hpdept']) && strIsEmpty($query['hpdept']) === false) {
            $hpdeptId = $query['hpdept'];
            return Doctor::model()->getAllByHpDeptId($hpdeptId, $with, $options);
        }

        return array();
    }

    public function loadAllIDoctorsByHpDeptId($deptId, $with = null, $options = null) {
        $imodelList = array();
        if (is_null($with)) {
            $with = array("doctorAvatar");
        }
        $models = $this->loadAllDoctorsByHpDeptId($deptId, $with, $options);
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $imodelList[] = $this->convertToIDoctor($model, $with);
            }
        }
        return $imodelList;
    }

    public function loadAllDoctorsByHpDeptId($deptId, $with = null, $options = null) {
        return Doctor::model()->getAllByHpDeptId($deptId, $with, $options);
    }

    public function loadAllIDoctorsByDiseaseIdAndHospitalId($diseaseId, $hpId, $with = null, $options = null) {
        $output = array();
        $doctors = Doctor::model()->getAllByDiseaseIdAndHospitalId($diseaseId, $hpId, $with, $options);
        if (arrayNotEmpty($doctors)) {
            $attributes = null;
            foreach ($doctors as $doctor) {
                $idoctor = $this->convertToIDoctor($doctor, $attributes, $with);
                $output[] = $idoctor;
            }
        }
        return $output;
    }

    public function loadAllDoctorsByDiseaseIdAndHospitalId($disId, $hpId, $with = null, $options = null) {
        return Doctor::model()->getAllByDiseaseIdAndHospitalId($disId, $hpId, $with, $options);
    }

    /*
      public function apiLoadIDoctorByIdJson($id) {
      $idoctor = $this->loadIDoctorById($id);
      $output['doctor'] = $idoctor;
      return $output;
      }
     * 
     */

    public function loadIDoctorById($id) {
        $with = array("doctorAvatar", "doctorCerts", 'doctorHospital');
        $doctor = $this->loadDoctorById($id, $with);
        if (isset($doctor)) {
            $idoctor = $this->convertToIDoctor($doctor, $with);
            return $idoctor;
        }
        return null;
    }

    public function createDoctor(DoctorForm $form, $checkVerifyCode = true) {
        if ($form->validate()) {
            if ($checkVerifyCode) {
                // check sms verify code.
                $userHostIp = Yii::app()->request->getUserHostAddress();
                $authMgr = new AuthManager();
                $authSmsVerify = $authMgr->verifyCodeForRegister($form->getMobile(), $form->getVerifyCode(), $userHostIp);
                if ($authSmsVerify->isValid() === false) {
                    // sms verify code is not valid.
                    $form->addError('verify_code', $authSmsVerify->getError('code'));
                    return false;
                }
            }

            $model = new Doctor();
            $model->setAttributes($form->getSafeAttributes());
            $model->scenario = $form->scenario;
            $model->prepareNewModel();
            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            } else {
                // deactive current smsverify.                
                if (isset($authSmsVerify)) {
                    $authMgr->deActiveAuthSmsVerify($authSmsVerify);
                }
                // Create DoctorCert from $_FILES.
                // saves uploaded files into filesystem and db.
                $form->id = $model->getId();
                $this->createDoctorCerts($model->getId());
            }
        }
        return ($form->hasErrors() === false);
    }

    /**
     * 添加医生头像
     * @param type $id
     * @param type $file
     */
    public function saveDoctorAvatar(Doctor $doctor, $file) {
        if (arrayNotEmpty($file)) {
            //文件处理
            $fileName = str_pad($doctor->id, 5, "0", STR_PAD_LEFT) . substr($file['name'], strrpos($file['name'], '.'));
            $newFile = file_get_contents($file['tmp_name']);
            $fileUrl = $doctor->getFileUploadPath() . '/' . $fileName;
            if (file_put_contents($fileUrl, $newFile) !== false) {
                $doctor->base_url = $doctor->getBaseUrl();
                $doctor->avatar_url = $fileUrl;
                if ($doctor->update(array('base_url', 'avatar_url'))) {
                    return true;
                };
            };
        }
        return false;
    }

    /**
     * 新增医生团队
     * @param Doctor $doctor
     * @param type $values
     */
    public function saveExpertTeam(Doctor $doctor, $values) {
        $status = 'update';
        $doctorId = $doctor->getId();
        $form = new ExpertTeamForm();
        //判断是修改还是创建
        $expertTeam = $doctor->getDoctorExpertTeam();
        if (is_null($expertTeam)) {
            $expertTeam = new ExpertTeam();
            $status = 'save';
            $form->initModel($doctor);
        }
        $form->setAttributes($values, true);
        $form->validate();
        if ($status === 'save') {
            $expertTeam->attributes = $form->getSafeAttributes();
        } else {
            $expertTeam->dis_tags = $form->dis_tags;
            $expertTeam->description = $form->description;
        }
        $dbTran = Yii::app()->db->beginTransaction();
        try {
            if ($expertTeam->save()) {
                if ($status === 'save') {
                    //医生团队关联表存储
                    $teamId = $expertTeam->getId();
                    $member = new ExpertTeamMemberJoin();
                    $member->doctor_id = $doctorId;
                    $member->team_id = $teamId;
                    $member->save();
                    //疾病关联表存储
                    $diseaseMgr = new DiseaseManager();
                    $output = $diseaseMgr->loadAllDiseasesByDoctorId($doctorId);
                    if (isset($output->diseaseIds)) {
                        $diseaseIds = $output->diseaseIds;
                        foreach ($diseaseIds as $diseaseId) {
                            $diseaseExpteamJoin = new DiseaseExpteamJoin();
                            $diseaseExpteamJoin->disease_id = $diseaseId;
                            $diseaseExpteamJoin->expteam_id = $teamId;
                            $diseaseExpteamJoin->save();
                        }
                    }
                }
            }
            //操作成功 数据库提交
            $dbTran->commit();
        } catch (CDbException $e) {
            $form->addError('数据库操作失败');
            $dbTran->rollback();
            throw new CHttpException($e->getMessage());
        } catch (CException $e) {
            $form->addError('操作失败');
            $dbTran->rollback();
            Yii::log("database table disease_expteam_join jdbc: " . $e->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
            throw new CHttpException($e->getMessage());
        }
    }

    /**
     * 根据医生id查询其团队
     * @param type $doctorId
     */
    public function loadExpertTeamByDoctorId($doctorId, $with = null) {
        return ExpertTeam::model()->getByAttributes(array('leader_id' => $doctorId), $with);
    }

    /*
      public function createDoctorCert($doctorId) {
      $uploadField = DoctorCert::model()->file_upload_field;
      $file = EUploadedFile::getInstanceByName($uploadField);
      if (isset($file)) {
      $output['filemodel'] = $this->saveDoctorCert($file, $doctorId);
      } else {
      $output['error'] = 'missing uploaded file.';
      }
      return $output;
      }
     */

    public function createDoctorCerts($ownerId) {
        $uploadField = DoctorCert::model()->file_upload_field;
        $files = EUploadedFile::getInstancesByName($uploadField);

        $output = array();
        if (arrayNotEmpty($files)) {
            foreach ($files as $file) {
                $output[] = $this->saveDoctorCert($file, $ownerId);
            }
        }
        return $output;
    }

    /**
     * Get EUploadedFile from $_FILE. 
     * Create DoctorCert model. 
     * Save file in filesystem. 
     * Save model in db.
     * @param EUploadedFile $file EUploadedFile::getInstances()
     * @param integer $doctorId Doctor.id     
     * @return DoctorCert 
     */
    private function saveDoctorCert($file, $ownerId) {
        //$dFile = new DoctorCert();
        $dFile = new DoctorCert();
        $dFile->initModel($ownerId, $file);
        $dFile->saveModel();

        return $dFile;
    }

    /**
     * 
     * @param type $mobile
     * @param type $code
     * @param type $userIp
     * @return type AuthSmsVerify
     */
    private function checkVerifyCode($mobile, $code, $userIp = null) {
        $authMgr = new AuthManager();
        $actionType = AuthSmsVerify::ACTION_USER_REGISTER;
        return $authMgr->verifyAuthSmsCode($mobile, $code, $actionType, $userIp);
    }

    public function updateDoctor(DoctorForm $form) {
        if ($form->validate()) {
            $model = $this->loadDoctorById($form->id);
            $model->attributes = $form->attributes;
            //TODO: enable validation on save.
            if ($model->save(false) === false) {
                $form->addErrors($model->getErrors());
            }
        }
        return ($form->hasErrors() === false);
    }

    public function deleteDoctor(Doctor $model) {
        // DoctorFacultyJoin.
        FacultyDoctorJoin::model()->deleteAllByAttributes(array('doctor_id' => $model->id));
        if (isset($model->doctorAvatar)) {
            $model->doctorAvatar->delete();
        }
        // DoctorAvatar.
        DoctorAavatar::model()->deleteAllByAttributes(array('doctor_id' => $model->id));
        // Doctor. 
        $model->delete();

        return ($model->hasErrors() === false);
    }

    public function createDoctorAvatar(DoctorAvatarForm $form) {
        if ($form->validate()) {
            $avatar = new DoctorAvatar();
            $avatar->saveNewModel($form->doctor_id, $form->image_url, $form->thumbnail_url, $form->display_order);
            if ($avatar->hasErrors()) {
                $form->addErrors($avatar->getErrors());
            }
        }
        return ($form->hasErrors() === false);
    }

    public function updateDoctorAvatar(DoctorAvatarForm $form) {
        if ($form->validate()) {
            $doctor = $this->loadDoctorById($form->doctor_id, array('doctorAvatar'));
            if (isset($doctor->doctorAvatar)) {
                $avatar = $doctorAvatar;
                $avatar->attributes = $form->attributes;
                if ($avatar->save() === false) {
                    $form->addErrors($avatar->getErrors());
                }
            } else {
                throw new CHttpException(404, 'DoctorAvatar record is not found.');
            }
        }
        return ($form->hasErrors() === false);
    }

    public function loadIDoctorJson($id) {
        $idoctor = $this->loadIDoctor($id);
        if (isset($idoctor)) {
            return array('doctor' => $idoctor);
        } else {
            return null;
        }
    }

    public function loadIDoctor($id, $with = null) {
        if (is_null($with)) {
            $with = array('doctorAvatar', 'doctorHospital');
        }
        $attributes = null;
        $doctor = $this->loadDoctorById($id, $with);
        if (isset($doctor)) {
            return $this->convertToIDoctor($doctor, $attributes, $with);
        } else {
            return null;
        }
    }

    public function loadDoctorById($id, $with = null) {
        return Doctor::model()->getById($id, $with);
        /*
          $model = Doctor::model()->getById($id, $with);
          if (is_null($model)) {
          throw new CHttpException(404, 'Record is not found.');
          }
          return $model;
         * 
         */
    }

    /**
     * 
     * @param Doctor $model
     * @param array $with 
     * @return IDoctor
     */
    public function convertToIDoctor(Doctor $model, $attributes = null, $with = null) {
        if (isset($model)) {
            $imodel = new IDoctor();
            $imodel->initModel($model, $attributes);
            $imodel->addRelatedModel($model, $with);
            return $imodel;
        } else {
            return null;
        }
    }

    public function loadUserDoctorProfileByUserid($userid) {
        return UserDoctorProfile::model()->getByAttributes(array('user_id' => $userid));
    }

    private function saveUserDoctorCert($file, $userId) {
        $dFile = new UserDoctorCert();
        $dFile->initModel($userId, $file);
        $dFile->saveModel();

        return $dFile;
    }

    /**
     * api 创建或修改(id设值)医生个人信息
     * @param User $user
     * @param $values
     * @param null $id
     * @return mixed
     */
    public function apiCreateProfile(User $user, $values, $id = null) {
        $userId = $user->getId();
        $model = UserDoctorProfile::model()->getByUserId($userId);
        if (isset($id)) {
            if (is_null($model)) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::UNAUTHORIZED;
                $output['errorMsg'] = '您没有权限执行此操作';
                return $output;
            }
        } else {
            if (is_object($model)) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::UNAUTHORIZED;
                $output['errorMsg'] = '您没有权限执行此操作';
                return $output;
            }
            $model = new UserDoctorProfile();
        }

        $model->setAttributes($values);
        // user_id.
        $model->user_id = $userId;
        $model->mobile = $user->getUsername();
        //给省会名 城市名赋值
        $regionState = RegionState::model()->getById($model->state_id);
        $model->state_name = $regionState->getName();
        $regionCity = RegionCity::model()->getById($model->city_id);
        $model->city_name = $regionCity->getName();

        if ($model->save()) {
            $output['status'] = EApiViewService::RESPONSE_OK;
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = 'success';
            $output['results'] = array(
                'id' => $model->getId(),
                'actionUrl' => Yii::app()->createAbsoluteUrl('/apimd/profilefile'),
            );
        } else {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = $model->getFirstErrors();
        }
        return $output;
    }

    /**
     * api 上传实名认证图片
     * @param User $user
     * @param $file
     * @return array
     */
    public function apiCreateProfileFile(User $user, $file) {
        if (is_null($file)) {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = '请上传图片';
            return $output;
        }
        $userId = $user->getId();
//        $models = UserDoctorCert::model()->getAllByAttributes(array('user_id'=>$userId, 'date_deleted'=>null));
//        foreach($models as $model){
//            $model->deleteModel(false); //删除原来已上传的实名认证
//        }

        $profileFile = $this->saveUserDoctorCert($file, $userId);
        if ($profileFile->hasErrors()) {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = $profileFile->getFirstErrors();
            return $output;
        }
        //自动生成一张Task
        $taskMgr = new TaskManager();
        $model = UserDoctorProfile::model()->getByAttributes(array('user_id'=>$user->getId()));
        $task = $taskMgr->createTaskDoctor($model);
        if ($task == false) {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = 'task data error.';
            return $output;
        }
        return array(
            'status' => EApiViewService::RESPONSE_OK,
            'errorCode' => ErrorList::ERROR_NONE,
            'errorMsg' => 'success',
            'results' => $profileFile->getId(),
        );
    }

    /**
     * api 申请成为签约专家
     * @param User $user
     * @param $values
     * @return mixed
     */
    public function apiCreateApplyContract(User $user, $values) {

        $model = $user->getUserDoctorProfile();
        if (isset($model)) {
            $model->preferred_patient = $values['preferred_patient'];
            if (is_null($model->date_contracted)) {
                $model->date_contracted = date('Y-m-d H:i:s');
            }
            if ($model->save()) {
                $output['status'] = EApiViewService::RESPONSE_OK;
                $output['errorCode'] = ErrorList::ERROR_NONE;
                $output['errorMsg'] = 'success';
                $output['results'] = '';
            } else {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::BAD_REQUEST;
                $output['errorMsg'] = $model->getFirstErrors();
            }
        } else {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = '您没有权限执行此操作';
        }
        return $output;
    }

    //api 删除医生证明图片
    public function apiDelectDoctorCertByIdAndUserId(User $user, $id, $absolute = false) {
        $model = UserDoctorCert::model()->getById($id);
        if (isset($model)) {
            if ($model->getUserId() != $user->getId()) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::UNAUTHORIZED;
                $output['errorMsg'] = '您没有权限执行此操作';
            } else {
                if ($model->deleteModel($absolute)) {
                    $output['status'] = EApiViewService::RESPONSE_OK;
                    $output['errorCode'] = 0;
                    $output['errorMsg'] = 'success';
                    $output['results'] = '';
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = $model->getErrors();
                }
            }
        } else {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = 'no data';
        }
        return $output;
    }

    //api 删除(病历/出院小结)图片
    public function apiDelectPatientFileByIdAndUserId(User $user, $id, $absolute = false) {
        $model = PatientMRFile::model()->getById($id);
        if (isset($model)) {
            if ($model->getCreatorId() != $user->getId()) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::UNAUTHORIZED;
                $output['errorMsg'] = '您没有权限执行此操作';
            } else {
                if ($model->deleteModel($absolute)) {
                    $output['status'] = EApiViewService::RESPONSE_OK;
                    $output['errorCode'] = 0;
                    $output['errorMsg'] = 'success';
                    $output['results'] = '';
                } else {
                    $output['status'] = EApiViewService::RESPONSE_NO;
                    $output['errorCode'] = ErrorList::BAD_REQUEST;
                    $output['errorMsg'] = $model->getErrors();
                }
            }
        } else {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::BAD_REQUEST;
            $output['errorMsg'] = 'no data';
        }
        return $output;
    }
   
    /**
     * api 修改密码
     */
    public function apiChangePassword($values,$id){
        $model = User::model()->getByUserId($id);
        if (isset($id)) {
            if (is_null($model)) {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::UNAUTHORIZED;
                $output['errorMsg'] = '您没有权限执行此操作';
                return $output;
            }
        }
        if($values['changepassword']['newPass']!=$values['changepassword']['dPass']){
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = '两次密码输入不符';
            return $output;
        }
        elseif($model->password != User::model()->encryptPassword($values['changepassword']['oldPass']))
        {
            $output['status'] = EApiViewService::RESPONSE_NO;
            $output['errorCode'] = ErrorList::ERROR_NONE;
            $output['errorMsg'] = '旧密码输入错误';
            return $output;
        }
        else{
            $postOldMd5Pass=User::model()->encryptPassword($values['changepassword']['oldPass']);
            $postNewPass=User::model()->encryptPassword($values['changepassword']['newPass']);
            $oldMd5Pass=$model->password_raw;
            $model->setAttributes($values);
            // user_id.
            $model_res = $model->find('id=:id and  password=:password',array(":id"=>$id,":password"=>$postOldMd5Pass));
            $model_res = $model->password_raw = $values['changepassword']['newPass'];
            $model_res = $model->password = $postNewPass;
            if ($model_res=$model->save()) {
                $output['status'] = EApiViewService::RESPONSE_OK;
                $output['errorCode'] = ErrorList::ERROR_NONE;
                $output['errorMsg'] = 'success';
                $output['results'] = array(
                    'id' => $model->getId(),
                );
            } else {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::BAD_REQUEST;
                $output['errorMsg'] = $model->getFirstErrors();
            }
            return $output;
        }      
    }
    
    /**
     * api 忘记密码
     */
    public function apiForgetPassword($mobile,$smsCode,$newPass,$userId,$userIp){
        $authM=new AuthManager();
        $authSmsVerify=$authM->verifyCodeForPasswordReset($mobile, $smsCode,$userIp);
        if ($authSmsVerify->isValid() === false) {
            $output['errorCode'] = ErrorList::NOT_FOUND;
            $output['errorMsg'] = $authSmsVerify->getError('code');
            return $output;
        }
        else{
            $model = User::model()->getByUsername($mobile);
            $newEncryptPass=$model->encryptPassword($newPass);
            $model->find('id=:id and  username=:username',array(":id"=>$userId,":username"=>$mobile));
            $model->password_raw = $newPass;
            $model->password = $newEncryptPass;
            if ($model->save()) {
                $output['status'] = EApiViewService::RESPONSE_OK;
                $output['errorCode'] = ErrorList::ERROR_NONE;
                $output['errorMsg'] = 'success';
                $output['results'] = array(
                    'id' => $model->getId(),
                );
            } else {
                $output['status'] = EApiViewService::RESPONSE_NO;
                $output['errorCode'] = ErrorList::BAD_REQUEST;
                $output['errorMsg'] = $model->getFirstErrors();
            }
            return $output;
        }
    }
}
