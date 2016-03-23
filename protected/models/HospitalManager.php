<?php

class HospitalManager {

    public function createHospital(HospitalForm $form) {
        if ($form->validate()) {
            $model = new Hospital();
            $model->attributes = $form->attributes;

            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            } else {
                $form->id = $model->id;
            }
        }
        return ($form->hasErrors() === false);
    }

    public function updateHospital(HospitalForm $form) {
        if ($form->validate()) {
            $model = $this->loadHospital($form->id);

            $model->attributes = $form->attributes;

            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            }
        }
        return ($form->hasErrors() === false);
    }

    public function deleteHospital(Hospital $model) {
        // HospitalFacultyJoin.
        HospitalFacultyJoin::model()->deleteAllByAttributes(array('hospital_id' => $model->id));

        // Hospital.
        $model->delete();

        return ($model->hasErrors() === false);
    }

    public function loadAllHospitalsByDiseaseId($diseaseId, $with = null, $options = null) {
        return Hospital::model()->getAllByDiseaseId($diseaseId, $with, $options);
    }

    public function loadListHospital($query = null, $options = null) {
        $with = null;
        $imodelList = $this->loadAllIHospitals($query, $with, $options);
        if (arrayNotEmpty($imodelList)) {

            foreach ($imodelList as $ihospital) {
                unset($ihospital->desc);
            }
        }
        return $imodelList;
    }

    public function loadAllIHospitals($query = null, $with = null, $options = null) {
        $imodelList = array();
        $modelList = $this->loadAllHospitals($query, $with, $options);

        if (arrayNotEmpty($modelList)) {
            foreach ($modelList as $model) {
                $imodelList[] = $this->convertToIHospital($model, null, $with);
            }
        }
        return $imodelList;
    }

    public function loadAllHospitals($query = null, $with = null, $options = null) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->compare("t.is_show", 1);
        // building dynamic query string.
        if (isset($query['city']) && strIsEmpty($query['city']) === false) {
            $criteria->compare("t.city_id", $query['city']);
        }
        if (isset($with))
            $criteria->with = $with;
        if (isset($options['order']))
            $criteria->order = $options['order'];
        if (isset($options['limit']))
            $criteria->limit = $options['limit'];
        if (isset($options['offset']))
            $criteria->offset = $options['offset'];

        return Hospital::model()->findAll($criteria);
    }

    /*
      public function loadIHospitalsByCity($city = null) {
      $output = array();
      // get location city.
      $regionMgr = new RegionManager();
      $icity = $regionMgr->loadILocationCity($city);
      $output['location']['city'] = $icity;
      // get hospitals belongs to the city.
      $hospitals = Hospital::model()->loadHospitalsByCity($city);
      // convert Hospital to IHospital
      if (arrayNotEmpty($hospitals)) {
      foreach ($hospitals as $hospital) {
      // unset "hospitalCity" relation, to avoid lazy loading.
      $hospital->hospitalCity = null;
      // create IHospital.
      $ihospital = new IHospital();
      $ihospital->initModel($hospital);
      // 医院相关联的科室
      $depts = $hospital->getDepartments();
      $ihospital->addIHospitalDepartments($depts);
      $output['hospitals'][] = $ihospital;
      }
      }
      return $output;
      }
     * 
     */
    /*
      public function loadIHospitalJson($id, $fid = null) {
      $ihospital = $this->loadIHospital($id, $fid);
      if (is_null($ihospital)) {
      return null;
      } else {
      return array('hospital' => $ihospital);
      }
      }
     */

    public function loadIHospitalById($id, $with = null) {
        if (is_null($with)) {
            $with = array('hospitalCity', 'hospitalDepartments');
        }
        $model = Hospital::model()->getById($id, $with);
        if (is_null($model)) {
            return null;
        }
        $imodel = $this->convertToIHospital($model, null, $with);

        return $imodel;
    }

    public function loadHospitalById($id, $with = array()) {
        return Hospital::model()->getById($id, $with);
    }

    /*
      public function loadIHospital($id, $fid = null) {
      $with = array("hospitalDepartments", 'hospitalCity');
      if (isset($fid)) {
      $join = FacultyHospitalJoin::model()->getByFacultyIdAndHospitalId($fid, $id, array('hospital'));
      if (is_null($join) || is_null($join->hospital)) {
      return null;
      } else {
      $hospital = $join->hospital;
      $ihospital = $this->convertToIHospital($hospital, null, $with);

      return $ihospital;
      }
      } else {

      $hospital = $this->loadHospital($id, $with);
      $ihospital = $this->convertToIHospital($hospital, null, $with);
      return $ihospital;
      }
      }
     */

    public function loadHospitalDeptById($deptId, $with = null) {
        return HospitalDepartment::model()->getById($deptId, $with);
    }

    public function loadHospital($id, $with = array()) {
        $model = Hospital::model()->getById($id, $with);
        if ($model === null)
            throw new CHttpException(404, 'Not found.');
        return $model;
    }

    public function convertToIHospital(Hospital $model, $attributes = null, $with = null) {
        if (isset($model)) {
            $imodel = new IHospital();
            $imodel->initModel($model, $attributes);
            $imodel->addRelatedModel($model, $with);
            return $imodel;
        } else {
            return null;
        }
    }

    /**
     * 根据科室名查看是否存在
     * @param type $name
     */
    public function checkDepartment($name,$hospitalId) {
        $data = HospitalDepartment::model()->getByNameAndHostitalId($name,$hospitalId);
        if(isset($data)){
            return false;
        }else{
            return true;
        }
    }
    
     public function addDepartment(DepartmentForm $form) {
        if ($form->validate()) {
            $model = new HospitalDepartment();
            $model->attributes = $form->attributes;
            if ($model->save() === false) {
                $form->addErrors($model->getErrors());
            }
        }
        return ($form->hasErrors() === false);
    }

}
