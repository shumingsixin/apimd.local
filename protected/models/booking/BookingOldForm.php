<?php

class BookingOldForm extends EFormModel {

    public $id;
    public $ref_no;
    public $user_id;
    public $mobile;
    public $booking_type;
    public $contact_name;
    public $booking_target; // remove it.
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
    public $files;  // uploaded files.
    private $_obj_faculty;    // object Faculty.
    private $_obj_doctor;     // object Doctor.
    private $_obj_expert_team;     // object ExpertTeam.
    private $_obj_hospital;   // object Hospital.
    private $_obj_hospital_dept; // object HospitalDepartment
    private $_options_faculty;
    private $_options_hospital_dept;
    private $_options_booking_type;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
// NOTE: you should only define rules for those attributes that
// will receive user inputs.
        return array(
            array('mobile, contact_name, booking_type, status, patient_condition', 'required', 'message' => '请填写{attribute}'),
            array('user_id, booking_type, faculty_id, doctor_id, expteam_id, hospital_id, hospital_dept, status', 'numerical', 'integerOnly' => true),
            array('ref_no', 'length', 'is' => 10),
            array('mobile', 'length', 'is' => 11, 'message' => '请填写正确的11位中国手机号码'),
            array('mobile', 'numerical', 'integerOnly' => true, 'message' => '请填写正确的11位中国手机号码'),
            array('appt_date', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('contact_name, booking_target', 'length', 'max' => 45),
            array('patient_condition', 'length', 'max' => 500, 'min' => 10, 'tooShort' => '至少输入10个字'),
            array('contact_email, contact_weixin, appt_date_str', 'length', 'max' => 100),
            array("faculty_id", "required", "message" => "请选择{attribute}", "on" => Booking::BOOKING_TYPE_FACULTY),
            array("doctor_id", "required", "message" => "请选择{attribute}", "on" => Booking::BOOKING_TYPE_DOCTOR),
            array("expteam_id", "required", "message" => "请选择{attribute}", "on" => Booking::BOOKING_TYPE_EXPERTTEAM),
            array("hospital_id, hospital_dept", "required", "message" => "请选择{attribute}", "on" => Booking::BOOKING_TYPE_HOSPITAL),
            //    array('id', 'safe'),
            array('verify_code', 'required', 'message' => '请填写{attribute}'),
            array('verify_code', 'length', 'is' => 6, 'message' => '{attribute}不正确'),
            array('verify_code', 'numerical', 'integerOnly' => true, 'message' => '{attribute}不正确')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
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
        );
    }

    /*
      public function beforeValidate() {
      return parent::beforeValidate();
      }
     * 
     */

    public function initModel($userId, Booking $model = null) {
        if (isset($model)) {
            $this->id = $model->id;
            $this->ref_no = $model->ref_no;
            $this->user_id = $model->user_id;
            $this->mobile = $model->mobile;
            $this->booking_type = $model->booking_type;
            $this->faculty_id = $model->faculty_id;
            $this->doctor_id = $model->doctor_id;
            $this->expteam_id = $model->expteam_id;
            $this->hospital_id = $model->hospital_id;
            $this->hospital_dept = $model->hospital_dept;
            $this->contact_name = $model->contact_name;
            $this->status = $model->status;
            $this->appt_date = $model->appt_date;
            $this->appt_date_str = $model->appt_date_str;
            $this->patient_condition = $model->patient_condition;
            $this->contact_email = $model->contact_email;
            $this->contact_weixin = $model->contact_weixin;
            $this->scenario = $model->scenario;
        } else {
            $this->user_id = $userId;
            $this->status = Booking::STATUS_NEW;
        }
    }

    public function setAttributes($values, $safeOnly = true) {
        parent::setAttributes($values, $safeOnly);
        $this->setScenario($this->booking_type);
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
// determine booking_type first.
        if (isset($values["type"])) {
            $this->setBookingType($values["type"]);
        } else {
            $this->setBookingType();
        }
        switch ($this->booking_type) {
            case Booking::BOOKING_TYPE_FACULTY:
                $this->setScenario(Booking::BOOKING_TYPE_FACULTY);
                if (isset($values["fid"])) {
                    $this->setFacultyId($values["fid"]);
                }
                break;
            case Booking::BOOKING_TYPE_DOCTOR:
                $this->setScenario(Booking::BOOKING_TYPE_DOCTOR);
                if (isset($values["did"])) {
                    $this->setDoctorId($values["did"]);
                }
                break;
            case Booking::BOOKING_TYPE_EXPERTTEAM:
                $this->setScenario(Booking::BOOKING_TYPE_EXPERTTEAM);
                if (isset($values["tid"])) {
                    $this->setExpertTeamId($values["tid"]);
                }
                break;
            case BOOKING::BOOKING_TYPE_HOSPITAL:
                $this->setScenario(BOOKING::BOOKING_TYPE_HOSPITAL);
                if (isset($values["hid"])) {
                    $this->setHospitalId($values["hid"]);
                    if (isset($values["dept"])) {
                        $this->setHospitalDeptId($values["dept"]);
                    }
                }
                break;
            default:
                break;
        }

//$this->loadData();
    }

    public function normalizePostFields($post) {
        $output = array();
        $map = array("booking_type" => "type", "faculty_id" => "fid", "doctor_id" => "did", "expteam_id" => "tid", "hospital_id" => "hid", "hospital_dept" => "dept");
        foreach ($map as $postKey => $newKey) {
            if (isset($post[$postKey])) {
                $output[$newKey] = $post[$postKey];
            }
        }
        return $output;
    }

    public function loadData() {
        switch ($this->scenario) {
            case Booking::BOOKING_TYPE_FACULTY:
                $this->loadOptionsFaculty();
                break;
            case Booking::BOOKING_TYPE_DOCTOR:
                if (isset($this->doctor_id) && is_null($this->_obj_doctor)) {
                    $model = Doctor::model()->getById($this->doctor_id);
                    if (isset($model)) {
                        $doctor = new stdClass();
                        $doctor->id = $model->getId();
                        $doctor->name = $model->getName();
                        $doctor->hid = $model->getHospitalId();
                        $this->_obj_doctor = $doctor;
                    }
                }
                break;
            case Booking::BOOKING_TYPE_EXPERTTEAM:
                if (isset($this->expteam_id) && is_null($this->_obj_expert_team)) {
                    $teamMgr = new ExpertTeamManager();
                    $model = $teamMgr->loadIExpertTeamById($this->expteam_id);
                    if (isset($model)) {
                        $this->_obj_expert_team = $model;
                    }
                }
                break;
            case BOOKING::BOOKING_TYPE_HOSPITAL:
                if (isset($this->hospital_id) && is_null($this->_obj_hospital)) {
                    $hospitalMgr = new HospitalManager();
                    $hospital = $hospitalMgr->loadIHospitalById($this->hospital_id);
                    if (isset($hospital)) {
                        $this->_obj_hospital = $hospital;
                        $optionsDept = array();
                        $departments = $hospital->getDepartments(true);
                        if (arrayNotEmpty($departments)) {
                            $optionsDept = arrayExtractKeyValue($departments, "id", "name");
                        }
                        $this->_options_hospital_dept = $optionsDept;
                        if (isset($optionsDept[$this->hospital_dept])) {
                            $dept = new stdClass();
                            $dept->id = $this->hospital_dept;
                            $dept->name = $optionsDept[$this->hospital_dept];
                            $this->_obj_hospital_dept = $dept;
                        }
                    }
                }
//$this->loadOptionsHospitalDept();

                break;
            default:
                $this->loadOptionsFaculty();
                break;
        }
    }

    public function loadOptionsBookingType() {
        if (is_null($this->_options_booking_type)) {
            $this->_options_booking_type = Booking::model()->getOptionsBookingType();
        }
        return $this->_options_booking_type;
    }

    public function loadOptionsFaculty() {
        if (is_null($this->_options_faculty)) {
            $this->_options_faculty = CHtml::listData(Faculty::model()->getAllActiveRecords(array('is_active' => 1)), 'id', 'name');
        }
        return $this->_options_faculty;
    }

    public function loadOptionsHospitalDept() {
        if (is_null($this->_options_hospital_dept)) {
            if (isset($this->hospital_id)) {
                $this->_options_hospital_dept = CHtml::listData(HospitalDepartment::model()->getAllByHospitalId($this->hospital_id), "id", "name");
            } else {
                $this->_options_hospital_dept = array();
            }
        }
        return $this->_options_hospital_dept;
    }

    /*     * ****** Accessors ******* */

    public function getFaculty() {
        return $this->_obj_faculty;
    }

    public function getFacultyName() {
        if (isset($this->_obj_faculty)) {
            return $this->_obj_faculty->name;
        } else {
            return "";
        }
    }

    public function getDoctor() {
        return $this->_obj_doctor;
    }

    public function getDoctorName() {
        if (isset($this->_obj_doctor)) {
            return $this->_obj_doctor->name;
        } else {
            return "";
        }
    }

    public function getExpertTeam() {
        return $this->_obj_expert_team;
    }

    public function getExpertTeamName() {
        if (isset($this->_obj_expert_team)) {
            return $this->_obj_expert_team->teamName;
        } else {
            return "";
        }
    }

    public function getHospital() {
        return $this->_obj_hospital;
    }

    public function getHospitalName() {
        if (isset($this->_obj_hospital)) {
            return $this->_obj_hospital->name;
        } else {
            return "";
        }
    }

    public function getHospitalDepartment() {
        return $this->_obj_hospital_dept;
    }

    public function getHospitalDeptName() {
        if (isset($this->_obj_hospital_dept)) {
            return $this->_obj_hospital_dept->name;
        } else {
            return "";
        }
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getBookingType() {
        return $this->booking_type;
    }

    public function setBookingType($v = null) {
        $optionsType = $this->loadOptionsBookingType();
        if (isset($optionsType[$v])) {
            $this->booking_type = $v;
        } else {
// set defaulty booking.type to "faculty".
            $this->booking_type = Booking::BOOKING_TYPE_FACULTY;
        }
    }

    public function getVerifyCode() {
        return $this->verify_code;
    }

    public function getFacultyId() {
        return $this->faculty_id;
    }

    public function setFacultyId($v) {
        $this->faculty_id = $v;
    }

    public function getDoctorId() {
        return $this->doctor_id;
    }

    public function setDoctorId($v) {
        $this->doctor_id = $v;
    }

    public function getExpertTeamId() {
        return $this->expteam_id;
    }

    public function setExpertTeamId($v) {
        $this->expteam_id = $v;
    }

    public function getHospitalId() {
        return $this->hospital_id;
    }

    public function setHospitalId($v) {
        $this->hospital_id = $v;
    }

    public function getHospitalDeptId() {
        return $this->hospital_dept;
    }

    public function setHospitalDeptId($v) {
        $this->hospital_dept = $v;
    }

    public function getBookingTarget() {
        return $this->booking_target;
    }

    public function setBookingTarget($v) {
        $this->booking_target = $v;
    }

}
