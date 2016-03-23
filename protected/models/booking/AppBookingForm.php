<?php

class AppBookingForm extends AppFormModel {

    public $id;
    public $ref_no;
    public $user_id;
    public $mobile;
    public $booking_type;
    public $contact_name;
    public $faculty_id;
    public $doctor_id;
    public $expteam_id;
    public $hospital_id;
    public $hospital_dept;
    public $status;
    public $appt_date;
    public $appt_date_str;
    public $patient_condition;
    public $contact_email;
    public $contact_weixin;
    public $verify_code;

    const BOOKING_TYPE_FACULTY = 1;     // 预约科室
    const BOOKING_TYPE_DOCTOR = 2;      // 预约医生
    const BOOKING_TYPE_EXPERTTEAM = 3;  // 预约专家团队
    const BOOKING_TYPE_HOSPITAL = 4;    // 预约医院

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
// NOTE: you should only define rules for those attributes that
// will receive user inputs.
        return
                array_merge(parent::rules(), array(
            array('mobile, contact_name, booking_type, status, patient_condition', 'required', 'message' => '请填写{attribute}'),
            array('user_id, booking_type, faculty_id, doctor_id, expteam_id, hospital_id, hospital_dept, status', 'numerical', 'integerOnly' => true),
            array('ref_no', 'length', 'is' => 10),
            array('mobile', 'length', 'is' => 11, 'message' => '请填写正确的11位中国手机号码'),
            array('mobile', 'numerical', 'integerOnly' => true, 'message' => '请填写正确的11位中国手机号码'),
            array('appt_date', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('contact_name', 'length', 'max' => 45),
            array('patient_condition', 'length', 'max' => 500, 'min' => 10, 'tooShort' => '至少输入10个字'),
            array('contact_email, contact_weixin, appt_date_str', 'length', 'max' => 100),
            array("faculty_id", "required", "message" => "请选择{attribute}", "on" => self::BOOKING_TYPE_FACULTY),
            array("doctor_id", "required", "message" => "请选择{attribute}", "on" => self::BOOKING_TYPE_DOCTOR),
            array("expteam_id", "required", "message" => "请选择{attribute}", "on" => self::BOOKING_TYPE_EXPERTTEAM),
            array("hospital_id, hospital_dept", "required", "message" => "请选择{attribute}", "on" => self::BOOKING_TYPE_HOSPITAL),
                //    array('id', 'safe'),
                //    array('verify_code', 'required', 'message' => '请填写{attribute}'),
                //    array('verify_code', 'length', 'is' => 6, 'message' => '{attribute}不正确'),
                //    array('verify_code', 'numerical', 'integerOnly' => true, 'message' => '{attribute}不正确'),
                )
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), array(
            'id' => 'ID',
            'ref_no' => Yii::t('booking', '预约号'),
            'user_id' => Yii::t('booking', '用户名'),
            'faculty_id' => Yii::t('booking', '科室'),
            'mobile' => Yii::t('booking', '手机号'),
            "booking_type" => "预约分类",
            "doctor_id" => "医生",
            "expteam_id" => "专家团队",
            "hospital_id" => "医院",
            "hospital_dept" => "科室",
            'contact_name' => Yii::t('booking', '称呼'),
            'verify_code' => Yii::t('booking', '验证码'),
            'status' => Yii::t('booking', '状态'),
            'appt_date' => Yii::t('booking', '就诊日期'),
            'appt_date_str' => Yii::t('booking', '就诊日期'),
            'patient_condition' => Yii::t('booking', '病情'),
            'contact_email' => Yii::t('booking', '邮箱'),
            'contact_weixin' => Yii::t('booking', '微信'),
            'files' => '上传文件'
                )
        );
    }

    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
        $this->setScenario($this->booking_type);
    }

}
