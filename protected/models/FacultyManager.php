<?php

class FacultyManager {
    /*     * ********************    app v2.0 api.   ************************ */

    public function loadIFaculty2($id, $with = null) {
        if (is_null($with)) {
            $with = array('facultyExpertTeams' => array('with' => 'expteamLeader'), 'facultyDoctors');
        }
        $faculty = Faculty::model()->getById($id, $with);
        if (is_null($faculty)) {
            return null;
        }
        $attributes = null;
        $ifaculty = $this->convertToIFaculty($faculty, $attributes, $with);
        if (arrayNotEmpty($faculty->facultyExpertTeams)) {
            $iexpTeams = $ifaculty->expertTeams;
            foreach ($faculty->facultyExpertTeams as $expteam) {
                foreach ($iexpTeams as $iexpteam) {
                    if ($expteam->id == $iexpteam->id && isset($expteam->expteamLeader)) {
                        $iexpertLeader = new IExpertLeader();
                        $iexpertLeader->initModel($expteam->expteamLeader);
                        $iexpteam->setIExpertLeader($iexpertLeader);
                    }
                }
            }
            $ifaculty->expertTeams = $iexpTeams;
        }

        return $ifaculty;
    }

    public function convertToIFaculty(Faculty $model, $attributes = null, $with = null) {
        if (isset($model)) {
            $imodel = new IFaculty();
            $imodel->initModel($model, $attributes);
            $imodel->addRelatedModel($model, $with);
            return $imodel;
        } else {
            return null;
        }
    }

    // load faculty data for json api.
    /**
     * @TODO: rewrite this entire method.
     * loadIFacultyByIdJson2()
     * loadIFacultyById
     * loadFacultyById()     
     */
    /*
      public function loadIFacultyJson2($id) {
      // $with=array();
      //$ifaculty = $this->loadIFacultyById($id,$with);
      //$faculty = Faculty::model()->getByIdWithDoctors($id, true);
      $faculty = Faculty::model()->getById($id);
      if (is_null($faculty)) {
      return null;
      } else {
      return $this->_createIFacultyJson2($faculty);
      }
      }
     * 
     */

    /**
     * 
     * @param Faculty $faculty
     * @return \IDoctor
     */
    private function _createIFacultyJson2(Faculty $faculty) {
        //$output=array('faculty'=>array(),'diseases'=>'', 'hospitals'=>'', 'doctors'=>'');
        $output = array();
        $output['faculty'] = array(
            'id' => $faculty->getId(),
            'name' => $faculty->getName(),
            'code' => $faculty->getCode(),
            'desc' => $faculty->getDescription()
        );
        // add Faculty.disease_list.
        $output['diseases'] = $faculty->getDiseaseList();
        // add Expert Team to $output.
        $exTeamMgr = new ExpertTeamManager();
        $with = array('expteamHospital', 'expteamLeader');
        $teamModelList = $exTeamMgr->loadTeamsByFacultyId($faculty->getId(), $with);
        if (arrayNotEmpty($teamModelList)) {
            foreach ($teamModelList as $teamModel) {
                $output['expertTeams'][] = $exTeamMgr->convertToIExpertTeam($teamModel, null, $with);
            }
        }

        // load related doctors.
        $limitD = 4;
        $doctorJoins = $faculty->facultyDoctorJoins();
        if (arrayNotEmpty($doctorJoins)) {
            $doctorIds = arrayExtractValue($doctorJoins, "doctor_id");
            $doctors = Doctor::model()->getAllByIds($doctorIds, array("doctorAvatar", "doctorHospital"));
            $index = 1;
            foreach ($doctors as $doctorModel) {
                //$doctorModel = $join->doctor;
                $idoctor = new IDoctor();
                $idoctor->initModel($doctorModel);
                $idoctor->url = Yii::app()->createAbsoluteUrl('api/doctor', array('id' => $idoctor->id));
                $output['doctors'][] = $idoctor;
                $index++;
                if ($index > $limitD) {
                    break;
                }
            }
            /*
              $index = 1;
              foreach ($doctorJoins as $join) {
              $doctorModel = $join->doctor;
              $idoctor = new IDoctor();
              $idoctor->initModel($doctorModel);
              $idoctor->url = Yii::app()->createAbsoluteUrl('api/doctor', array('id' => $idoctor->id));
              $output['doctors'][] = $idoctor;
              $index++;
              if ($index > $limitD) {
              break;
              }
              }
             * 
             */
        }

        return $output;
    }

    // load faculty data for webpage.
    /*
      public function loadIFaculty2($id) {
      //$faculty = Faculty::model()->getByIdWithDoctors($id, false);
      $faculty = Faculty::model()->getById($id);
      if (is_null($faculty)) {
      return null;
      } else {
      return $this->_createIFaculty2($faculty);
      }
      }
     */

    private function _createIFaculty2(Faculty $faculty) {
        // create IFaculty model.

        $ifaculty = new IFaculty();
        // init values.
        $ifaculty->initModel($faculty);
        // add ExpertTeam to IFaculty.
        // TODO: re-implement this      - 2015-07-10 by QP
        $exTeamMgr = new ExpertTeamManager();
        $with = array('expteamHospital', 'expteamLeader');
        $teamModelList = $exTeamMgr->loadTeamsByFacultyId($faculty->getId(), $with);
        if (arrayNotEmpty($teamModelList)) {
            $iTeamList = array();
            foreach ($teamModelList as $teamModel) {
                $iTeamList[] = $exTeamMgr->convertToIExpertTeam($teamModel, null, $with);
            }
            $ifaculty->expertTeams = $iTeamList;
        }

        // add IDoctor to IFaculty.
        $limitD = 6;
        $doctorJoins = $faculty->facultyDoctorJoins;
        if (arrayNotEmpty($doctorJoins)) {
            $doctorIds = arrayExtractValue($doctorJoins, "doctor_id");
            $doctors = Doctor::model()->getAllByIds($doctorIds, array("doctorAvatar", "doctorHospital"));
            $counter = 1;
            foreach ($doctors as $doctorModel) {
                if ($counter > $limitD) {
                    break;
                }
                //$doctorModel = $join->doctor;
                $idoctor = new IDoctor();
                $idoctor->initModel($doctorModel);
                $ifaculty->addDoctor($idoctor);
                $counter++;
            }
        }
        return $ifaculty;
    }

    public function loadFacultyList2($limit = 9) {
        if ($limit < 1) {
            $limit = 9;
        }
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('is_active', 1);
        $criteria->order = 't.display_order ASC';
        $criteria->limit = $limit;
        $facultyList = Faculty::model()->findAll($criteria);
        $output = array();
        if (arrayNotEmpty($facultyList)) {
            foreach ($facultyList as $facultyModel) {
                $obj = new stdClass();
                $obj->id = $facultyModel->getId();
                $obj->name = $facultyModel->getName();
                $obj->desc = $facultyModel->getDescription();
                $obj->code = $facultyModel->getCode();
                $obj->url = Yii::app()->createAbsoluteUrl('api/faculty2', array('id' => $facultyModel->getId()));
                $obj->urlIcon = $facultyModel->getAbsUrlIcon();
                $output[] = $obj;
            }
        }
        return $output;
    }

    /*     * **************************     API v1.0      **************************** */

    // app v1.0 api
    public function loadIFacultyJson($id) {
        $faculty = Faculty::model()->getByIdWithHospitalsAndDoctors($id, true);
        if (is_null($faculty)) {
            return null;
        } else {
            return $this->_createIFacultyJson($faculty);
        }
    }

    public function loadIFaculty($id) {
        $faculty = Faculty::model()->getByIdWithHospitalsAndDoctors($id, true);
        if (is_null($faculty)) {
            return null;
        } else {
            return $this->_createIFaculty($faculty);
        }
    }

    public function loadFacultyListJson($limit = 6) {
        //$output=array();
        $output['faculties'] = $this->loadFacultyList($limit);
        return $output;
    }

    public function loadFacultyList($limit = 6) {
        if ($limit < 1) {
            $limit = 6;
        }
        /*
          $criteria = new CDbCriteria();
          $criteria->addCondition('t.date_deleted is NULL');
          $criteria->compare('is_active', 1);
          $criteria->order = 't.display_order ASC';
          $criteria->limit = $limit;
          $facultyList = Faculty::model()->findAll($criteria);
         */

        $criteria = new CDbCriteria();
        $criteria->addInCondition("id", array(1, 2, 3, 4, 6, 102));

        $facultyList = Faculty::model()->findAll($criteria);

        $output = array();
        if (arrayNotEmpty($facultyList)) {
            foreach ($facultyList as $facultyModel) {
                $obj = new stdClass();
                $obj->id = $facultyModel->getId();
                $obj->name = $facultyModel->getName();
                $obj->desc = $facultyModel->getDescription();
                $obj->code = $facultyModel->getCode();
                $obj->url = Yii::app()->createAbsoluteUrl('api/faculty', array('id' => $facultyModel->getId()));
                $obj->urlIcon = $facultyModel->getAbsUrlIcon();
                $output[] = $obj;
            }
        }
        return $output;
    }

    /**
     * 
     * @param Faculty $faculty
     * @return \IDoctor
     */
    private function _createIFacultyJson(Faculty $faculty) {
        //$output=array('faculty'=>array(),'diseases'=>'', 'hospitals'=>'', 'doctors'=>'');
        $output = array();
        $output['faculty'] = array(
            'id' => $faculty->getId(),
            'name' => $faculty->getName(),
            'code' => $faculty->getCode(),
            'desc' => $faculty->getDescription()
        );
        // add Faculty.disease_list.
        $output['diseases'] = $faculty->getDiseaseList();
        // add IHospital to $output.
        $limitH = 4;
        $hospitalJoins = $faculty->facultyHospitalJoins();
        if (arrayNotEmpty($hospitalJoins)) {
            $index = 1;
            foreach ($hospitalJoins as $join) {
                $hospitalModel = $join->hospital;
                $ihospital = new IHospital();
                $ihospital->initModel($hospitalModel);
                $ihospital->facultyDesc = $join->getDescription(false);
                $ihospital->url = Yii::app()->createAbsoluteUrl('api/hospital', array('id' => $ihospital->id, 'fid' => $faculty->getId()));
                $output['hospitals'][] = $ihospital;
                $index++;
                if ($index > $limitH) {
                    break;
                }
            }
        }
        $limitD = 6;
        $doctorJoins = $faculty->facultyDoctorJoins();
        if (arrayNotEmpty($doctorJoins)) {
            $index = 1;
            foreach ($doctorJoins as $join) {
                $doctorModel = $join->doctor;
                $idoctor = new IDoctor();
                $idoctor->initModel($doctorModel);
                $idoctor->url = Yii::app()->createAbsoluteUrl('api/doctor', array('id' => $idoctor->id));
                $output['doctors'][] = $idoctor;
                $index++;
                if ($index > $limitD) {
                    break;
                }
            }
        }
        return $output;
    }

    private function _createIFaculty(Faculty $faculty) {
        // create IFaculty model.
        $ifaculty = new IFaculty();
        // init values.
        $ifaculty->initModel($faculty);
        // add IHospital to IFaculty.
        $limitH = 4;
        $hospitalJoins = $faculty->facultyHospitalJoins;
        if (arrayNotEmpty($hospitalJoins)) {
            $index = 1;
            foreach ($hospitalJoins as $join) {
                $hospitalModel = $join->hospital;
                $ihospital = new IHospital();
                $ihospital->initModel($hospitalModel);
                $ihospital->facultyDesc = $join->getDescription(false);
                $ifaculty->addHospital($ihospital);
                $index++;
                if ($index > $limitH) {
                    break;
                }
            }
        }
        // add IDoctor to IFaculty.
        $limitD = 6;
        $doctorJoins = $faculty->facultyDoctorJoins;
        if (arrayNotEmpty($doctorJoins)) {
            $index = 1;
            foreach ($doctorJoins as $join) {
                $doctorModel = $join->doctor;
                $idoctor = new IDoctor();
                $idoctor->initModel($doctorModel);
                $ifaculty->addDoctor($idoctor);
                $index++;
                if ($index > $limitD) {
                    break;
                }
            }
        }
        return $ifaculty;
    }

    public function loadFaculty($id, $with = null) {
        $model = Faculty::model()->getById($id, $with);
        if (is_null($model)) {
            throw new CHttpException(404, 'Record is not found.');
        } else {
            return $model;
        }
    }

    public function loadFacultyByCode($code, $with = null) {
        $model = Faculty::model()->getByAttributes(array('code' => $code), $with);
        if (is_null($model)) {
            throw new CHttpException(404, 'Record is not found.');
        } else {
            return $model;
        }
    }

}
