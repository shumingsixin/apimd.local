<?php

class BookingSearchForm extends EFormModel {

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
    public $patient_condition;
    public $contact_email;
    public $contact_weixin;
    private $_options_status;
    private $_options_faculty;
    private $_options_expteam;
    private $_options_hospital_dept;
    private $_options_booking_type;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array_merge(Booking::model()->attributeLabels(), array(
        ));
    }

    /*
      public function beforeValidate() {
      return parent::beforeValidate();
      }
     * 
     */

    public function initModel($model = null) {
        if (isset($model)) {
            $this->setAttributes($model->getSafeAttributes());
        }

        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsFaculty();
        $this->loadOptionsBookingType();
    }

    public function loadOptionsStatus() {
        if (is_null($this->_options_status)) {
            $this->_options_status = Booking::model()->getOptionsStatus();
        }
        return $this->_options_status;
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

    public function loadOptionsExpertTeam() {
        if (is_null($this->_options_expteam)) {
            $this->_options_expteam = array();

            $teamMgr = new ExpertTeamManager();
            $teams = $teamMgr->loadTeamData();
            if (arrayNotEmpty($teams)) {
                foreach ($teams as $team) {
                    $this->_options_expteam[$team->teamId] = $team->teamName;
                }
            }
            return $this->_options_expteam;
        }
    }

    function loadOptionsHospitalDept() {
        if (is_null($this->_options_hospital_dept)) {
            if (isset($this->hospital_id)) {
                $this->_options_hospital_dept = CHtml::listData(HospitalDepartment::model()->getAllByHospitalId($this->hospital_id), "id", "name");
            } else {
                $this->_options_hospital_dept = array();
            }
        }
        return $this->_options_hospital_dept;
    }

}
