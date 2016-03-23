<?php

class PatientMRForm extends EFormModel {

    //public $patient_id;
    public $id; // PatientInfo.id
    public $creator_id;
    public $disease_name;
    public $disease_detail;
    public $remark;
    public $patient_name;   // display
    public $patient_age;    // display
    public $patient_age_month;
    public $patient_gender; // display
    public $patient_city;   // display
    private $model;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('creator_id, disease_name, disease_detail', 'required'),
            array('id, creator_id', 'numerical', 'integerOnly' => true),
            array('disease_name', 'length', 'max' => 50),
            array('disease_detail', 'length', 'max' => 1000),
            array('remark', 'length', 'max' => 500),
        );
    }

    public function initModel(PatientInfo $model = null) {
        if (isset($model)) {
            $this->model = $model;
            $this->scenario = $model->scenario;
            $attributes = $model->getAttributes();
            // set safe attributes.
            $this->setAttributes($attributes, true);
        }

        $this->loadOptions();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'patient_id' => '患者',
            'creator_id' => '创建者',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病史描述',
            'remark' => '备注',
        );
    }

    public function loadOptions() {
        
    }

    public function setPatientInfo(PatientInfo $model) {        
        $this->id = $model->getId();
        $this->patient_name = $model->getName();
        $this->patient_gender = $model->getGender();
        $this->patient_age = $model->getAge();
        $this->patient_age_month = $model->getAgeMonth();
        $this->patient_city = $model->getCityName();
    }

    public function getPatientName() {
        return $this->patient_name;
    }

    public function getPatientGender() {
        return $this->patient_gender;
    }

    public function getPatientAge() {
        return $this->patient_age;
    }
    public function getPatientAgeMonth() {
        return $this->patient_age_month;
    }

    public function getPatientCity() {
        return $this->patient_city;
    }

    public function setPatientId($v) {
        $this->patient_id = $v;
    }

    public function setCreatorId($v) {
        $this->creator_id = $v;
    }

}
