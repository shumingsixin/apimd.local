<?php

class MedicalRecordForm extends EFormModel {

    public $user_id;
    public $name;
    public $nric;
    public $gender;
    public $dob;
    public $state;
    public $city;
    public $occupation;
    public $patient_condition;
    public $drug_allergy;
    public $surgery_history;
    public $drug_history;
    public $disease_history;
    public $remark;
    public $dob_year;
    public $dob_month;
    public $dob_day;
    public $options_dob_year;
    public $options_dob_month;
    public $options_dob_day;
    public $options_gender;
    public $options_occupation;
    public $options_state;
    public $options_city;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, name, nric, gender, dob, dob_year, dob_month, dob_day, state, city, occupation, patient_condition', 'required', 'message' => '请输入{attribute}', 'on' => 'new, update'),
            //    array('nric', 'numerical', 'integerOnly' => true, 'message' => '请输入正确的18位数中国身份证号码'),
            //   array('nric', 'length', 'is' => 18, 'message' => '请输入正确的18位数中国身份证号码'),
            array('dob_year, dob_month, dob_day', 'numerical', 'integerOnly' => true, 'on' => 'new, update'),
            //array('nric', 'validateChineseNRIC'),
            array('nric', 'EValidators.IdentificationNumber', 'type' => IdentificationNumber::NRIC_CHINA, 'on' => 'new, update'),
            array('dob', 'validateYmdDate', 'on' => 'new, update'),
            array('patient_condition', 'required', 'message' => '请输入{attribute}', 'on' => 'new, update'),
            array('dob', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('id, user_id, name, nric, gender, dob, state, city, occupation, patient_condition, surgery_history, drug_history, disease_history, remark, date_created, date_updated, date_deleted', 'safe'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => Yii::t('user', '用户名'),
            'name' => Yii::t('mr', '患者姓名'),
            'nric' => Yii::t('mr', '身份证号码'),
            'gender' => Yii::t('mr', '患者性别'),
            'dob' => Yii::t('mr', '出生日期'),
            'state' => Yii::t('mr', '所在省份'),
            'city' => Yii::t('mr', '所在地区'),
            'occupation' => Yii::t('mr', '患者的职业'),
            'patient_condition' => Yii::t('mr', '目前的病情'),
            'drug_allergy' => Yii::t('mr', '药物过敏'),
            'surgery_history' => Yii::t('mr', '以往的手术经历'),
            'drug_history' => Yii::t('mr', '以往的用药经历'),
            'disease_history' => Yii::t('mr', '以往的患病史'),
            'remark' => Yii::t('mr', '备注'),
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    public function beforeValidate() {
        $dob = $this->parseDobFromChinaNRIC($this->nric);
        if (isset($dob)) {
            $this->dob_year = $dob['year'];
            $this->dob_month = $dob['month'];
            $this->dob_day = $dob['day'];
        }
        $this->dob = $this->parseYmdToDate($this->dob_year, $this->dob_month, $this->dob_day);

        return parent::beforeValidate();
    }

    public function initModel(MedicalRecord $mr = null) {
        if (isset($mr)) {
            $this->id = $mr->id;
            $this->attributes = $mr->attributes;
            $this->isNewRecord = false;

            $date = date_parse_from_format(self::FORMAT_DATETIME_FORM, $this->dob);  //Y-m-d
            $this->dob_year = $date['year'];
            $this->dob_month = $date['month'];
            $this->dob_day = $date['day'];
        }
        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsGender();
        $this->loadOptionsOccupation();
        $this->loadOptionsDobYear();
        $this->loadOptionsDobMonth();
        $this->loadOptionsDobDay();
        $this->loadOptionsState();
        $this->loadOptionsCity();
    }

    public function loadOptionsGender() {
        if (arrayNotEmpty($this->options_gender) === false) {
            $this->options_gender = parent::loadOptionsGender();
        }
        return $this->options_gender;
    }

    public function loadOptionsOccupation() {
        if (arrayNotEmpty($this->options_occupation) === false) {
            //$this->options_occupation = array(1 => '金融', 2 => 'IT', 3 => '教育', 4 => '自营', 5 => '建筑', 6 => '工程', 7 => '服务', 8 => '其它');
            $this->options_occupation = MedicalRecord::model()->getOptionsOccupation();
        }
        return $this->options_occupation;
    }

    public function loadOptionsDobYear() {
        if (arrayNotEmpty($this->options_dob_year) === false) {
            $this->options_dob_year = $this->loadOptionsYear();
        }
        return $this->options_dob_year;
    }

    public function loadOptionsDobMonth() {
        if (arrayNotEmpty($this->options_dob_month) === false) {
            $this->options_dob_month = $this->loadOptionsMonth();
        }
        return $this->options_dob_month;
    }

    public function loadOptionsDobDay() {
        if (arrayNotEmpty($this->options_dob_day) === false) {
            $this->options_dob_day = $this->loadOptionsDay($this->dob_year, $this->dob_month);
        }
        return $this->options_dob_day;
    }

    public function loadOptionsState() {
        if (arrayNotEmpty($this->options_state) === false) {
            $this->options_state = CHtml::listData(RegionState::model()->getAllByCountryCode('CHN'), 'id', 'name_cn');
        }
        return $this->options_state;
    }

    public function loadOptionsCity() {
        if (arrayNotEmpty($this->options_city) === false) {
            if (isset($this->state)) {
                $this->options_city = CHtml::listData(RegionCity::model()->getAllByStateId($this->state), 'id', 'name_cn');
            } else {
                $this->options_city = array();
            }
        }
        return $this->options_city;
    }

    /*     * ****** Accessors ******* */

    public function setUserId($v) {
        $this->user_id = $v;
    }

}
