<?php

class MedicalRecordManager {

    public function createMedicalRecord(MedicalRecordForm $form) {
        if ($form->validate()) {
            // $form->updateDOB();
            $model = new MedicalRecord();
            $model->scenario = $form->scenario;
            $model->attributes = $form->attributes;
            // $form->normalizeModel();

            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            } else {
                $form->id = $model->getId();
            }
        }
        return ($form->hasErrors() === false);
    }

    public function updateMedicalRecord(MedicalRecordForm $form, MedicalRecord $model) {
        if ($form->validate()) {
            // $model->scenario = $form->scenario;
            //$model->attributes = $form->attributes;
            $model->patient_condition = $form->patient_condition;
            $model->drug_history = $form->drug_history;
            $model->surgery_history = $form->surgery_history;
            $model->disease_history = $form->disease_history;

            if ($model->update(array('patient_condition', 'disease_history', 'drug_history', 'disease_history')) === false) {
                $form->addErrors($model->getErrors());
            } else {
                $form->success = true;
            }
        }
        return ($form->hasErrors() === false);
    }

    public function createNewBooking(MRBookingForm $form) {
        if ($form->validate()) {
            $model = new MedicalRecordBooking();
            $model->scenario = $form->scenario;
            $model->attributes = $form->attributes;

            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            } else {
                $form->id = $model->getId();
                $form->success = true;
            }
        }

        return ($form->hasErrors() === false);
    }

    public function apiCreateMRFIle($post) {
        $output = array();
        if (isset($post['mrfile']['user_id']) === false || isset($post['mrfile']['mr_id']) === false || isset($post['mrfile']['report_type']) === false) {
            $output = array('status' => false, 'error' => 'Missing parameters.');
            return $post;
            // return $output;
        } else {
            $userid = $post['mrfile']['user_id'];
            $mrid = $post['mrfile']['mr_id'];
            $rt = $post['mrfile']['report_type'];

            $recordFile = $this->createMRFile($mrid, $rt, $userid);
            $output['file'] = $this->createMRFileJsonOutput($recordFile);
            $output['status'] = true;
            //$output['files'] = $files;
        }
        return $output;
    }

    public function createMRFile($mrid, $rt, $userid) {
        //$uploadField = MedicalRecordFile::model()->file_upload_field;
        $uploadField = 'mrfile';
        $file = EUploadedFile::getInstanceByName($uploadField);
        
        $output = $this->saveMedicalRecordFile($file, $mrid, $rt, $userid);
        return $output;
    }

    //TODO: re-implement saving image process.
    public function createMRFiles($mrid, $rt, $userid) {
        $uploadField = MedicalRecordFile::model()->file_upload_field;
        $files = EUploadedFile::getInstancesByName($uploadField);

        $output = array();
        if (arrayNotEmpty($files)) {
            foreach ($files as $file) {
                $output[] = $this->saveMedicalRecordFile($file, $mrid, $rt, $userid);
            }
        }
        return $output;
    }

    public function createMRFileJsonOutput(MedicalRecordFile $model) {
        $output = array(
            'id' => $model->getId(),
            'mrId' => $model->getMrId(),
            'fileUrl' => $model->getAbsFileUrl(),
            'thumbnailUrl' => $model->getAbsThumbnailUrl(),
            'deleteUrl' => Yii::app()->createUrl('medicalRecord/deleteFile'),
            'deleteType' => 'post',
            'fileDate' => $model->getDateTaken(),
            'fileDesc' => $model->getDescription()
        );
        return $output;
    }

    /**
     * Delete model.
     * @param MedicalRecordFile $recordFile.
     */
    public function deleteMRFile(MedicalRecordFile $recordFile) {
        return $recordFile->deleteModel(true); //update date_deleted in db, not actually delete it.
    }

    public function updateMRFileMeta(User $user, $filemetasInput) {
        $output = array();
        $filemetaList = array();
        $fids = array();
        // check user rights.
        foreach ($filemetasInput as $key => $filemeta) {
            if (isset($filemeta['fid'])) {
                $fid = $filemeta['fid'];
                $fids[] = $fid;
                $filemetaList[$fid] = array();
                $filemetaList[$fid]['fid'] = $fid;
                if (arrayNotEmpty($filemeta)) {
                    foreach ($filemeta as $key => $value) {
                        $filemetaList[$fid][$key] = $value;
                    }
                }
            } else {
                $output[$key]['error'] = 'Missing fid';
            }
        }

        $criteriaFind = new CDbCriteria();
        $criteriaFind->addCondition('t.date_deleted is NULL');
        $criteriaFind->addInCondition('id', $fids);
        // $criteriaFind->select = 'id,mr_id, user_id';
        // get all MedicalRecordFile models from db.
        $models = MedicalRecordFile::model()->findAll($criteriaFind);

        // Check if any fid is not found in db.
        $fidsDB = arrayExtractValue($models, 'id');
        $fidsNotFound = array_diff($fids, $fidsDB);
        if (arrayNotEmpty($fidsNotFound)) {
            foreach ($fidsNotFound as $fid) {
                $outputTemp['fid'] = $fid;
                $outputTemp['error'] = 'Record is not found.';
            }
        }

        if (empty($models) === false) {
            // check for user rights.
            $userid = $user->getId();
            foreach ($models as $model) {
                $fid = $model->getId();
                $outputTemp = array('fid' => $fid);

                if ($model->getUserId() != $userid) {
                    $outputTemp['error'] = 'No rights.';
                    // remove from the list which is to be updated into db later.
                    unset($filemetaList[$fid]);
                } else {
                    // update records in db.
                    $data = $filemetaList[$fid];

                    $updateAttributes = array('date_updated');
                    if (isset($data['dateTaken'])) {
                        $model->setDateTaken($data['dateTaken']);
                        $updateAttributes[] = 'date_taken';
                    }
                    if (isset($data['desc'])) {
                        $model->setDescription($data['desc']);
                        $updateAttributes[] = 'description';
                    }

                    if ($model->validate()) {
                        if ($model->update($updateAttributes)) {
                            if (in_array('date_taken', $updateAttributes)) {
                                $outputTemp['dateTakenStatus'] = true;
                            }
                            if (in_array('description', $updateAttributes)) {
                                $outputTemp['descStatus'] = true;
                            }
                        }
                    }
                    // on errors.
                    if ($model->hasErrors()) {
                        if ($model->getError('date_taken') !== null) {
                            $outputTemp['dateTakenError'] = $model->getError('date_taken');
                        }
                        if ($model->getError('description') !== null) {
                            $outputTemp['descError'] = $model->getError('description');
                        }
                    }
                }
                $output[] = $outputTemp;
            }
        }

        return $output;
    }

    /**
     *
     * @param integer $mrid MRFile.mr_id
     * @param array $values 'id'=>MRFile.id, 'date'=>MRFile.date_taken, 'desc'=>MRFile.description.
     */
    public function updateMRFileMeta2($mrid, $values) {

        $output = array();
        $data = array();
        $mrFileIds = array();
        foreach ($values as $value) {
            if (isset($value['id'])) {
                $id = $value['id'];
                $mrFileIds[] = $id;
                $data[$id] = array();
                $data[$id]['id'] = $id;
                $data[$id]['date_taken'] = $value['date_taken'];
                $data[$id]['description'] = $value['description'];
            } else {
                continue;
            }
        }
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('mr_id', $mrid);
        $criteria->addInCondition('t.id', $mrFileIds);
        $models = MedicalRecordFile::model()->findAll($criteria);
        if (arrayNotEmpty($models)) {
            foreach ($models as $model) {
                $id = $model->getId();
                $model->setDescription($data[$id]['description']);
                $model->setDateTaken($data[$id]['date_taken']);
                $model->update(array('description', 'date_taken'));
                if ($model->hasErrors()) {
                    $output[$model->getId()] = $model->getErrors();
                }
            }
        }
        return $output;
    }

    public function loadMedicalRecordBooking($id, $with = null) {
        if (is_null($with)) {
            $with = array('mrbUser', 'mrbFaculty', 'mrbMedicalRecord');
        }
        $model = MedicalRecordBooking::model()->getById($id, $with);
        if (is_null($model)) {
            throw new CHttpException(404, 'Booking not found');
        } else {
            return $model;
        }
    }

    public function loadIMedicalRecordBooking($id) {
        $model = null;
        try {
            $model = $this->loadMedicalRecordBooking($id);
        } catch (CHttpException $e) {
            return null;
        }
        if (is_null($model)) {
            return null;
        } else {
            return $this->_createIMedicalRecordBooking($model);
        }
    }

    public function checkUserRights($rights, $user, $data) {
        if ($rights == 'filemeta.update') {
            return $user->getId() == $data->getUserId();
        } else {
            return false;
        }
    }

    private function _createIMedicalRecordBooking(MedicalRecordBooking $model) {
        // create IMedicalRecordBooking model.
        $ibooking = new IMedicalRecordBooking();
        // init values.
        $ibooking->initModel($model);

        return $ibooking;
    }

    /**
     * Get CUploadedFile from $_FILE. Create MedicalRecordFile model. Save file in filesystem. Save model in db.
     * @param CUploadedFile $file CUploadedFile::getInstances()
     * @param integer $mrid MedicalRecord.id
     * @param integer $rt   MedicalRecord::REPORT_TYPE
     * @return MedicalRecordFile 
     */
    private function saveMedicalRecordFile($file, $mrid, $rt, $userid) {
        $mrFile = new MedicalRecordFile();
        $mrFile->initModel($mrid, $rt, $userid, $file);
        $mrFile->saveModel();

        return $mrFile;
    }

    
    /**
     * Moves existing medical_record and medical_record_files to booking and its related tables.
     * By Zhou Bo - 2015-07-29
     * @throws CHttpException
     * @throws CException
     */
    public function actionCopyMrToBooking(){
        
        $dbTran = Yii::app()->db->beginTransaction();
        try {
            $mrbs = MedicalRecordBooking::Model()->getAll();
            foreach($mrbs as $key=> $mrb){
                
                $booking = new Booking();
                $booking->booking_type=Booking::BOOKING_TYPE_FACULTY;
                $booking->ref_no = str_pad($mrb->ref_no,10,"0",STR_PAD_LEFT);
                $booking->user_id = $mrb->user_id;
                $booking->mobile = $mrb->mobile;
                $booking->status = $mrb->status;
                $booking->faculty_id = $mrb->faculty_id;
                $apptDate = new DateTime($mrb->appt_date);
                $booking->appt_date = $apptDate->format('Y-m-d');
                $booking->contact_email = $mrb->email;
                $booking->contact_weixin = $mrb->wechat;
                $booking->date_created = $mrb->date_created;
                $booking->date_updated = $mrb->date_updated;
                $booking->date_deleted = $mrb->date_deleted;
                
                $mr = MedicalRecord::Model()->getByAttributes(array('id' => $mrb->mr_id));
                if(isset($mr)){
                    $booking->contact_name = $mr->name;
                    $booking->patient_condition = $mr->patient_condition;
                }else{
                    $booking->contact_name = "NoBody";
                    $booking->patient_condition = "";
                }
//                $booking->save();
               
                if ($booking->save() === false) {
                    var_dump($booking->attributes);
                    var_dump($booking->getErrors());
                    throw new CException("Error saving booking, ref_no: " . $booking->ref_no);
                }else{
                    echo "saving booking success;ref_no=",$booking->ref_no,";id=",$booking->id,"<br>";
                }
                if(isset($mr)){
                    $bpatient = new BookingPatient();
                    $bpatient->booking_id = $booking->id;
                    $bpatient->name = $mr->name;
                    $bpatient->prc_ic = $mr->nric;
                    $bpatient->gender = $mr->gender;
                    $bpatient->age = calYearsFromDatetime($mr->dob);
                    $birthday = new DateTime($mr->dob);
                    $bpatient->born_year = $birthday->format('Y');
                    $bpatient->from_state = $mr->state;
                    $bpatient->from_city = $mr->city;
                    $bpatient->medical_condition = $mr->patient_condition;
                    $bpatient->surgery_history = $mr->surgery_history;
                    $bpatient->drug_history = $mr->drug_history;
                    $bpatient->disease_history = $mr->disease_history;
                    $bpatient->remark = $mr->remark;
                    $bpatient->date_created = $mr->date_created;
                    $bpatient->date_updated = $mr->date_updated;
                    $bpatient->date_deleted = $mr->date_deleted;
//                    $bpatient->save();
                    if ($bpatient->save() === false) {
                        var_dump($bpatient->attributes);
                        var_dump($bpatient->getErrors());
                        throw new CException("Error saving bookingPatient, booking_id: " . $bpatient->booking_id);
                    }else{
                        echo "saving bookingPatient success<br>";
                    }                   
                }else{
                    echo "MRbooking->ref_no=",$booking->ref_no," has no medicalRecords<br>";
                }

                $mrfs = MedicalRecordFile::Model()->getAllByAttributes(array('mr_id' => $mrb->mr_id));
                if(arrayNotEmpty($mrfs)){
                    foreach($mrfs as $key => $mrf){
                        $bfile = new BookingFile();
                        $bfile->booking_id = $booking->id;
                        $bfile->uid = $mrf->uid;
                        $bfile->user_id = $mrf->user_id;
                        $bfile->file_ext = $mrf->file_ext;
                        $bfile->mime_type = $mrf->mime_type;
                        $bfile->file_name = $mrf->file_name;
                        $bfile->file_url = $mrf->file_url;
                        $bfile->file_size = $mrf->file_size;
                        $bfile->thumbnail_name = $mrf->thumbnail_name;
                        $bfile->thumbnail_url = $mrf->thumbnail_url;
                        $bfile->date_created = $mrf->date_created;
                        $bfile->date_updated = $mrf->date_updated;
                        $bfile->date_deleted = $mrf->date_deleted;
//                        $bfile->save();
                        if ($bfile->save() === false) {
                            var_dump($bfile->attributes);
                            var_dump($bfile->getErrors());
                            throw new CException("Error saving bookingFile, booking_id: " . $bfile->booking_id);
                        }else{
                            echo "saving bookingFile success<br>";
                        }
                    }
                }else{
                    echo "MRbooking->ref_no=",$booking->ref_no," has no medicalRecordFiles<br>";
                }
                echo "<br>";
            }
            $dbTran->commit();
        }catch (CDbException $e) {      
            $dbTran->rollback();
            throw new CHttpException($e->getMessage());
        } catch (CException $e) {
            $dbTran->rollback();
            throw new CHttpException($e->getMessage());
        }
    }
}
