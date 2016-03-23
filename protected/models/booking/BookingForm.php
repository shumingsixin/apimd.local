<?php

class BookingForm extends EFormModel {

    public $id;
    public $ref_no;
    public $user_id;
    public $mobile;
    public $bk_type;
    public $bk_status;
    public $contact_name;
    public $contact_email;
    //public $booking_target; // remove it.
    //public $faculty_id;
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
    public $verify_code;
    //   public $files;  // uploaded files.
    //   private $_obj_faculty;    // object Faculty.
    //   private $_obj_doctor;     // object Doctor.
//    private $_obj_expert_team;     // object ExpertTeam.
    //   private $_obj_hospital;   // object Hospital.
    //   private $_obj_hospital_dept; // object HospitalDepartment
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
            array('ref_no, mobile, bk_status, bk_type', 'required'),
            array('user_id, bk_status, bk_type, doctor_id, expteam_id, city_id, hospital_id, hp_dept_id', 'numerical', 'integerOnly' => true),
            array('ref_no', 'length', 'is' => 12),
            array('mobile', 'length', 'is' => 11),
            array('contact_name, doctor_name, hospital_name, hp_dept_name, disease_name', 'length', 'max' => 50),
            array('contact_email', 'length', 'max' => 100),
            array('disease_detail', 'length', 'max' => 1000),
            array('doctor_id, doctor_name, hospital_id, hospital_name, hp_dept_id, hp_dept_name', 'required', 'on' => 'doctor'), // 预约医生
            array('expteam_id, hospital_id, hospital_name, hp_dept_id, hp_dept_name', 'required', 'on' => 'expertteam'), // 预约专家团队
            array('doctor_name, hospital_name, hp_dept_name', 'required', 'on' => 'quickbook'), // 快速预约
            array('remark', 'length', 'max' => 500),
            array('id, ref_no, user_id, mobile, contact_name, contact_email, bk_status, bk_type, doctor_id, doctor_name, expteam_id, city_id, hospital_id, hospital_name, hp_dept_id, hp_dept_name, disease_name, disease_detail, date_start, date_end, appt_date, remark, date_created', 'safe'),
        );
    }

    public function initModel(Booking $model) {
        if (isset($model)) {
            $attributes = $model->attributes;
            $this->setAttributes($attributes, true);
            $this->scenario = $model->scenario;
        } else {
            $this->status = StatCode::BK_STATUS_NEW;
        }
        $this->loadOptions();
    }

    public function loadOptions() {
        
    }

    /**
     * gets and sets values form request parameters.
     * @param array $values 
     * @param stirng $request 
     */
    public function setValuesFromRequest($values, $request = "get") {
        if ($request == "post") {
            $values = $this->normalizePostFields($values);
        }
        if (isset($values['tid'])) {
            $this->scenario = 'expertteam';
            $this->bk_type = StatCode::BK_TYPE_EXPERTTEAM;
            $this->expteam_id = $values['tid'];
        } elseif (isset($values['did'])) {
            $this->scenario = 'doctor';
            $this->bk_type = StatCode::BK_TYPE_DOCTOR;
            $this->doctor_id = $values['did'];
        } else {
            $this->scenario = 'quickbook';
            $this->bk_type = StatCode::BK_TYPE_QUICKBOOK;
        }
        try {
            switch ($this->scenario) {
                case StatCode::BK_TYPE_EXPETTEAM:
                    // 专家团队
                    $this->setExpertteamData($this->expteam_id);
                    break;
                case StatCode::BK_TYPE_DOCTOR:
                    // 某个医生
                    $this->setDoctorData($this->doctor_id);
                    break;
                case StatCode::BK_TYPE_QUICKBOOK:
                    // 快速预约
                    break;
                default:
                    break;
            }
        } catch (CDbException $cdbex) {
            $this->bk_type = $this->default_bk_type;
            $this->setScenario($this->default_scenario);
        } catch (CException $cex) {
            $this->bk_type = $this->default_bk_type;
            $this->setScenario($this->default_scenario);
        }
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
