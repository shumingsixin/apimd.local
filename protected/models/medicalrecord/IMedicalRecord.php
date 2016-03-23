<?php

class IMedicalRecord extends EViewModel {

    public $id;
    public $userId;
    public $name;
    public $gender;
    public $age;
    public $dob;
    public $state;
    public $city;
    public $occupation;
    public $patientCondition;
    public $drugAllergy;
    public $surgeryHistory;
    public $drugHistory;
    public $diseaseHistory;
    public $remark;
    public $files;  // array of IMedicalRecordFile.

    public function initModel(MedicalRecord $model) {
        $this->id = $model->getId();
        $this->userId = $model->getUserId();
        $this->name = $model->getName();
        $this->gender = $model->getGender();
        $this->age = $model->getPatientAge();
        $this->dob = $model->getDob();
        $this->state = $model->getStateName();
        $this->city = $model->getCityName();
        $this->occupation = $model->getOccupation();
        $this->patientCondition = $model->getPatientCondition(false);
        $this->surgeryHistory = $model->getSurgeryHistory(false);
        $this->diseaseHistory = $model->getDiseaseHistory(false);
        $this->drugHistory = $model->getDrugHistory(false);
        $this->drugAllergy = $model->getDrugAllergy(false);
        $this->remark = $model->getRemark(false);

        $this->files = array();
    }

    public function addFile($file) {
        if ($file instanceof MedicalRecordFile) {
            $ifile = new IMedicalRecordFile();
            $ifile->initModel($file);
            $this->files[$file->getId()] = $ifile;
        } else {
            $this->files[$file->getId()] = $file;
        }
    }

    public function getFiles() {
        return $this->files;
    }

    public function getId() {
        return $this->id;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getPatientName() {
        return $this->name;
    }

    public function getPatientGender() {
        return $this->gender;
    }

    public function getPatientAge() {
        return $this->age;
    }

    public function getPatientDob() {
        return $this->dob;
    }

    public function getPlaceFromState() {
        return $this->state;
    }

    public function getPlaceFromCity() {
        return $this->city;
    }

    public function getPlaceFrom($separator = '&nbsp;') {        
        if ($this->city == $this->state) {
            return $this->city;
        } else {
            return $this->city . $separator . $this->state;
        }
    }
    
    public function getOccupation(){
        return $this->occupation;
    }
    public function getPatientCondition($ntext=false){
        return $this->getTextAttribute($this->patientCondition, $ntext);        
    }
    public function getDrugAllergy($ntext=false){
        return $this->getTextAttribute($this->drugAllergy, $ntext);
    }
    public function getSurgeryHistory($ntext=false){
        return $this->getTextAttribute($this->surgeryHistory, $ntext);
    }
    public function getDrugHistory($ntext=false){
        return $this->getTextAttribute($this->drugHistory, $ntext);        
    }
    public function getDiseaseHistory($ntext=false){
        return $this->getTextAttribute($this->diseaseHistory, $ntext);        
    }

}
