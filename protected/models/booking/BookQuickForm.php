<?php

class BookQuickForm extends EFormModel {

    public $id;
    public $ref_no;
    public $user_id;
    public $mobile;
    public $bk_type;
    public $bk_status;
    public $contact_name;
    public $contact_email;
    public $doctor_name;
    public $city_id;
    public $hospital_name;
    public $hp_dept_name;
    public $date_start;
    public $date_end;
    public $appt_date;
    public $disease_name;
    public $disease_detail;
    public $remark;
    public $user_agent;
    public $submit_via;
    public $verify_code;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('mobile, verify_code, bk_status, bk_type, contact_name, disease_name, disease_detail', 'required', 'message' => '请填入{attribute}'),
            array('user_id, bk_status, bk_type, city_id', 'numerical', 'integerOnly' => true),
            //    array('ref_no', 'length', 'is' => 14),
            array('mobile', 'length', 'is' => 11),
            array('contact_name, doctor_name, hospital_name, hp_dept_name, disease_name', 'length', 'max' => 50),
            array('contact_email', 'length', 'max' => 100),
            array('disease_detail', 'length', 'max' => 1000, 'min' => 10),
            //    array('doctor_id, doctor_name, hospital_id, hospital_name, hp_dept_id, hp_dept_name', 'required', 'on' => 'doctor'), // 预约医生
            //    array('expteam_id, hospital_id, hospital_name, hp_dept_id, hp_dept_name', 'required', 'on' => 'expertteam'), // 预约专家团队
            //    array('doctor_name, hospital_name, hp_dept_name', 'required', 'on' => 'quickbook'), // 快速预约
            array('remark', 'length', 'max' => 500),
            array('user_agent', 'length', 'max' => 20),
            array('submit_via', 'length', 'max' => 10),
            array('id, ref_no, user_id, mobile, contact_name, contact_email, bk_status, bk_type, doctor_name, city_id, hospital_name, hp_dept_name, disease_name, disease_detail, date_start, date_end, appt_date, remark, submit_via, date_created', 'safe'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no' => '单号',
            'user_id' => '用户',
            'mobile' => '手机号',
            'contact_name' => '患者姓名',
            'contact_email' => '联系邮箱',
            'bk_status' => '状态',
            'bk_type' => '分类',
            'doctor_id' => '医生',
            'doctor_name' => '医生姓名',
            'expteam_id' => '专家团队',
            'expteam_name' => '专家团队名称',
            'city_id' => '城市',
            'hospital_id' => '医院',
            'hospital_name' => '医院名称',
            'hp_dept_id' => '科室',
            'hp_dept_name' => '科室名称',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病情',
            'date_start' => '最早',
            'date_end' => '最晚',
            'appt_date' => '预约日期',
            'remark' => '备注',
            'submit_via' => '提交来源',
            'verify_code' => '短信验证码'
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

    public function setUserId($v) {
        $this->user_id = $v;
    }

}
