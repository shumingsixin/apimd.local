<?php

class IMedicalRecordBooking extends EViewModel {

    public $id;
    public $refNo;
    //  public $mrid;
    //  public $facultyId;
    public $contactMobile;
    public $status;
    public $apptDate;
    public $bufferDays;
    public $patientIntention;
    public $contactEmail;
    public $contactWeixin;
    public $dateCreated;
    public $owner;  // IUser.
    public $faculty;    // IFaculty.
    public $medicalRecord;  // IMedicalRecord.

    public function initModel(MedicalRecordBooking $model) {
        $this->id = $model->getId();
        $this->refNo = $model->getRefNumber();
        $this->contactMobile = $model->getMobile();
        $this->status = $model->getStatus();
        $this->apptDate = $model->getApptDate();
        $this->bufferDays = $model->getBufferDays();
        $this->patientIntention = $model->getPatientIntention();
        $this->contactEmail = $model->getEmail();
        $this->contactWeixin = $model->getWechat();
        $this->dateCreated = $model->getDateCreated();

        $user = $model->getUser();
        if(isset($user)){
            $iuser = new IUser();
            $iuser->initModel($user);
            $this->owner = $iuser;
        }
        
        
        $faculty = $model->getFaculty();
        if (isset($faculty)) {
            $ifaculty = new IFaculty();
            $ifaculty->initModel($faculty);
            $this->faculty = $ifaculty;
        }
        $mRecord = $model->getMedicalRecord();
        if(isset($mRecord)){
            $iRecord = new IMedicalRecord();
            $iRecord->initModel($mRecord);
            $this->medicalRecord=$iRecord;
        }
    }

    public function getId() {
        return $this->id;
    }

    public function getRefNumber() {
        return $this->refNo;
    }

    public function getContactMobile() {
        return $this->contactMobile;
    }

    public function getStatus() {
        return $this->status;
    }
    
    public function getFacultyName(){
        if(isset($this->faculty)){
            return $this->faculty->getName();
        }else{
            return null;
        }
    }

    public function getApptDate() {
        return $this->apptDate;
    }

    public function getBufferDays() {
        return $this->bufferDays;
    }

    public function getPatientIntention($ntext=false) {
        return $this->getTextAttribute($this->patientIntention, $ntext);        
    }

    public function getContactEmail() {
        return $this->contactEmail;
    }

    public function getContactWeixin() {
        return $this->contactWeixin;
    }
    
    public function getDateCreated(){
        return $this->dateCreated;
    }

    public function getOwner(){
        return $this->owner;
    }
    
    public function getFaculty() {
        return $this->faculty;
    }

    public function getMedicalRecord() {
        return $this->medicalRecord;
    }

}
