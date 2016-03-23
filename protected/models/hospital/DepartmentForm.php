<?php

class DepartmentForm extends EFormModel {

    public $hospital_id;
    public $group;
    public $name;
    public $options_hospital_department;

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hospital_id, name, group', 'required'),
            array('hospital_id', 'numerical', 'integerOnly' => true),
            array('group, name', 'length', 'max' => 45),
            array('name', 'checkName'),
        );
    }

    public function checkName() {
        if (isset($this->name)) {
            $hospitalMgr = new HospitalManager();
            $output = $hospitalMgr->checkDepartment($this->name,$this->hospital_id);
            if (!$output) {
                $this->addError('name', '该科室已存在!');
            }
        }
    }

    public function initModel(HospitalDepartment $depatr = null) {
        if (isset($depatr)) {
            $this->attributes = $depatr->attributes;
            $this->scenario = $depatr->scenario;
        } else {
            $this->scenario = 'new';
        }
        $this->LoadOptionsDeptGroup();
    }

    public function setOptionsHospitalDepartment($listModel) {
        if (is_array($listModel) && count($listModel) > 0) {
            $this->options_hospital_department = CHtml::listData($listModel, 'id', 'name');
        } else {
            $this->options_hospital_department = array();
        }
    }

    public function LoadOptionsHospitalDepartment() {
        return $this->options_hospital_department;
    }

    public function LoadOptionsDeptGroup() {
        return HospitalDepartment::model()->getOptionsDeptGroup();
    }

}
