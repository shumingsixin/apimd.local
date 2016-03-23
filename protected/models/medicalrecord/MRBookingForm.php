<?php

class MRBookingForm extends EFormModel {

    public $id;
    public $ref_no;
    public $user_id;
    public $mr_id;
    public $faculty_id;
    public $mobile;
    public $status;
    public $appt_date;
    public $buffer_days;
    public $patient_intention;
    public $email;
    public $wechat;
    public $options_faculty;
    public $options_buffer_days;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, mr_id, faculty_id, mobile, status, appt_date, buffer_days, patient_intention', 'required', 'message' => '请输入{attribute}'),
            array('user_id, mr_id, faculty_id, status, buffer_days', 'numerical', 'integerOnly' => true),
            array('mobile', 'length', 'is' => 11, 'message' => '请输入正确的11位中国手机号码'),
            array('mobile', 'numerical', 'integerOnly' => true, 'message' => '请输入正确的11位中国手机号码'),
            array('appt_date', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('wechat', 'length', 'max' => 45),
            array('patient_intention, email', 'length', 'max' => 100),
            array('id', 'safe'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no'=>Yii::t('mr', '单号'),
            'user_id' => Yii::t('user', '用户名'),
            'mr_id' => Yii::t('mr', '病历'),
            'faculty_id' => Yii::t('mr', '科室'),
            'mobile' => Yii::t('user', '手机号码'),
            'status' => Yii::t('mr', '状态'),
            'appt_date' => Yii::t('mr', '期望就诊日期'),
            'buffer_days' => Yii::t('mr', '前后几天'),
            'patient_intention' => Yii::t('mr', '具体需求'),
            'email' => Yii::t('user', '邮箱'),
            'wechat' => Yii::t('user', '微信'),
        );
    }

    public function initModel(MedicalRecordBooking $mrBooking=null) {
        if (isset($mrBooking)) {
            $this->faculty_id = $mrBooking->faculty_id;
            $this->mobile = $mrBooking->mobile;
            $this->status = $mrBooking->status;
            $this->appt_date = $mrBooking->appt_date;
            $this->buffer_days = $mrBooking->buffer_days;
            $this->patient_intention = $mrBooking->patient_intention;
            $this->email = $mrBooking->email;
            $this->wechat = $mrBooking->wechat;
        } else {
            $this->status = MedicalRecordBooking::STATUS_NEW;
        }
        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsFaculty();
    }

    public function loadOptionsFaculty() {
        if (arrayNotEmpty($this->options_faculty) === false) {
            $this->options_faculty = CHtml::listData(Faculty::model()->getAllByAttributes(array('is_active' => 1)), 'id', 'name');
        }
        return $this->options_faculty;
    }

    public function loadOptionsBufferDays() {
        if (arrayNotEmpty($this->options_buffer_days) === false) {
            $this->options_buffer_days = MedicalRecordBooking::model()->getOptionsBufferDays();
        }
        return $this->options_buffer_days;
    }

    /*     * ****** Accessors ******* */

    public function getUserId() {
        return $user_id;
    }

    public function setUserId($v) {
        $this->user_id = $v;
    }

    public function getMrId() {
        return $this->mr_id;
    }

    public function setMrId($v) {
        $this->mr_id = $v;
    }

}
