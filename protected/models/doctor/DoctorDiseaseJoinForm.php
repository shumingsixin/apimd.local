<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DoctorDiseaseJoinForm
 *
 * @author shuming
 */
class DoctorDiseaseJoinForm extends EFormModel {

    public $doctor_id;
    public $did_list_db;        //did in db
    public $did_list_input;
    public $did_list_delete;    // sql delete
    public $did_list_insert;    // sql insert

    public function rules() {
        return array(
            array('doctor_id', 'required'),
        );
    }

    public function initModel($doctorId, $diseaseList) {
        $this->doctor_id = $doctorId;
        $didList = $diseaseList;
        $this->did_list_db = $didList;
    }

    public function setDiseaseListInput(array $diseaseIds, $process = true) {
        $this->did_list_input = $diseaseIds;
        if ($process) {
            $this->createDiseaseListInsert();
            $this->createDiseaseListDelete();
        }
    }

    public function createDiseaseListDelete() {
        $this->did_list_delete = array_diff($this->did_list_db, $this->did_list_input);
    }

    public function createDiseaseListInsert() {
        $this->did_list_insert = array_diff($this->did_list_input, $this->did_list_db);
    }
    
    public function getDoctorId(){
        return $this->doctor_id;
    }

    public function getDiseaseListDelete() {
        return $this->did_list_delete;
    }

    public function getDiseaseListInsert() {
        return $this->did_list_insert;
    }

}
