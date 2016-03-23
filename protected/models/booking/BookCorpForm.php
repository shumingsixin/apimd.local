<?php

class BookCorpForm extends EFormModel {

    public $id;
    public $ref_no;
    public $user_id;
    public $mobile;
    public $bk_type;
    public $bk_status;
    public $contact_name;
    public $contact_email;
    public $doctor_id;
    public $doctor_name;
    public $expteam_id;
    public $city_id;
    public $hospital_id;
    public $hospital_name;
    public $hp_dept_id;
    public $hp_dept_name;
    public $date_start;
    public $date_end;
    public $appt_date;
    public $disease_name;
    public $disease_detail;
    public $remark;
    public $is_corporate;
    public $corporate_name;
    public $corp_staff_rel;
    public $verify_code;
    private $default_scenario = 'quickbook';
    private $default_bk_type = StatCode::BK_TYPE_QUICKBOOK;
    private $model_doctor;
    private $model_expertteam;
    private $model_hopsital;
    private $model_hp_dept;
    private $options_bk_status;
    private $options_city;
    private $options_hp_dept;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('mobile ,corporate_name ,disease_name, verify_code, disease_detail, corp_staff_rel', 'required'),
            array('user_id, bk_status, bk_type, doctor_id, expteam_id, city_id, hospital_id, hp_dept_id, is_corporate', 'numerical', 'integerOnly' => true),
            array('ref_no', 'length', 'is' => 12),
            array('mobile', 'length', 'is' => 11),
            array('contact_name, doctor_name, hospital_name, hp_dept_name, disease_name, corporate_name, corp_staff_rel', 'length', 'max' => 50),
            array('contact_email', 'length', 'max' => 100),
            array('disease_detail', 'length', 'max' => 1000),
            array('doctor_id, doctor_name, hospital_id, hospital_name, hp_dept_id, hp_dept_name', 'required', 'on' => 'doctor'), // 预约医生
            array('expteam_id, hospital_id, hospital_name, hp_dept_id, hp_dept_name', 'required', 'on' => 'expertteam'), // 预约专家团队
            array('doctor_name, hospital_name, hp_dept_name', 'required', 'on' => 'quickbook'), // 快速预约
            array('remark', 'length', 'max' => 500),
            array('id, ref_no, user_id, mobile, verify_code,contact_name, contact_email, bk_status, bk_type, doctor_id, doctor_name, expteam_id, city_id, hospital_id, hospital_name, hp_dept_id, hp_dept_name, disease_name, disease_detail, date_start, date_end, appt_date, remark, date_created', 'safe'),
                //     array('verify_code', checkVerifryCode)
        );
    }

    private function checkVerifyCode() {
        if (isset($this->verify_code) && isset($this->mobile)) {
            $authMgr = new AuthManager();
            $authSmsVerify = $authMgr->verifyCodeForBooking($form->mobile, $form->verify_code, null);
            if ($authSmsVerify->isValid() === false) {
                //$output['errors']['verify_code'] = $authSmsVerify->getError('code');
                $this->addError('verify_code', $authSmsVerify->getError('code'));
            }
        }
    }

    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no' => '预约号',
            'user_id' => '用户',
            'mobile' => '手机号',
            'contact_name' => '患者姓名',
            'contact_email' => '邮箱',
            'bk_status' => '状态',
            'bk_type' => '种类',
            'doctor_id' => '医生',
            'doctor_name' => '医生姓名',
            'expteam_id' => '专家团队',
            'expteam_name' => '专家团队',
            'city_id' => '城市',
            'hospital_id' => '医院',
            'hospital_name' => '医院名称',
            'hp_dept_id' => '科室',
            'hp_dept_name' => '科室名称',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病情',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'appt_date' => 'Appt Date',
            'remark' => 'Remark',
            'is_corporate' => '是否是企业用户',
            'corporate_name' => '企业名称',
            'corp_staff_rel' => '与患者的关系',
            'verify_code' => '验证码',
            'submit_via' => 'Submit Via',
            'date_created' => '创建日期',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
            'expertBooked' => '所约专家'
        );
    }

    public function initModel(Booking $model = null) {
        if (isset($model)) {
            $attributes = $model->attributes;
            $this->setAttributes($attributes, true);
            $this->scenario = $model->scenario;
        } else {
            $this->bk_status = StatCode::BK_STATUS_NEW;
            $this->bk_type = StatCode::BK_TYPE_QUICKBOOK;
        }
        $this->loadOptions();
    }

    public function loadOptions() {
        
    }

    public function setDoctorData($doctorId) {
        // doctor_id.
        $this->doctor_id = $doctorId;
        $with = array('doctorHospital', 'doctorHpDept', 'doctorCity');
        $this->modelDoctor = Doctor::model()->getById($this->doctorId);
        if (is_null($this->modelDoctor)) {
            throw new CDbException('no data');
        }
    }

    public function setHospital($hospitalId) {
        
    }

    public function setHpDept($hpDeptId) {
        
    }

    public function setUserId($v) {
        $this->user_id = $v;
    }

}
