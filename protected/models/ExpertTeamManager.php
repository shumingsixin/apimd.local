<?php

class ExpertTeamManager {

    public function loadAllIExpertTeams($query = null, $with = null, $options = null) {
        $imodelList = array();
        $modelList = $this->loadAllExpertTeams($query, $options, $with);
        if (arrayNotEmpty($modelList)) {
            foreach ($modelList as $model) {
                $imodelList[] = $this->convertToIExpertTeam($model, null, $with);
            }
        }
        return $imodelList;
    }

    public function loadAllExpertTeams($query = null, $options = null, $with = null) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        // building dynamic query string.
        if (isset($query['city']) && strIsEmpty($query['city']) === false) {
            if ($query['city'] == 'others') {
                $criteria->addNotInCondition("t.city_id", array(1,73,200,74,87));//不包括‘北上广南杭’
            } else {
                $criteria->compare("t.city_id", $query['city']);
            }
        }
        if (isset($with))
            $criteria->with = $with;
        if (isset($options['order']))
            $criteria->order = $options['order'];
        if (isset($options['limit']))
            $criteria->limit = $options['limit'];
        if (isset($options['offset']))
            $criteria->offset = $options['offset'];

        return ExpertTeam::model()->findAll($criteria);
    }

    /*
      public function loadAllIExperTeamsByCityJson($cityId = null, $options = null) {
      $teams = $this->loadAllIExpertTeamsByCity($cityId, $options);
      $output['expertTeams'] = $teams;

      return $output;
      }
     */

    public function loadAllIExpertTeamsByCity($cityId = null, $options = null) {
        $output = array();
        $with = array('expteamLeader', 'expteamHospital', 'expteamHpDept', 'expteamFaculty');
        $teamList = $this->loadAllExpertTeamsByCity($cityId, $with, $options);
        if (arrayNotEmpty($teamList)) {
            foreach ($teamList as $team) {
                $output[] = $this->convertToIExpertTeam($team, null, $with);
            }
        }
        return $output;
    }

    public function loadAllExpertTeamsByCity($cityId = null, $with = null, $options = null) {
        if (is_null($cityId)) {
            return ExpertTeam::model()->getAll($with, $options);
        } else {
            return ExpertTeam::model()->getAllByCityId($cityId, $with, $options);
        }
    }

    /*
      public function loadIExpertTeamByIdJson($id) {
      $output = $this->loadIExpertTeamById($id);
      if (is_null($output)) {
      return null;
      } else {
      return array('expertTeam' => $output);
      }
      }
     */

    public function loadIExpertTeamById($id, $with = null) {
        if (is_null($with)) {
            $with = array('expteamHospital', 'expteamHpDept', 'expteamCity', 'expteamMembers' => array('with' => 'doctorHospital'),);
        }
        $expteam = $this->loadExpertTeamById($id, $with);
        if (isset($expteam)) {
            $iexpteam = $this->convertToIExpertTeam($expteam, null, $with);
            return $iexpteam;
        } else {
            return null;
        }
    }

    public function loadExpertTeamById($id, $with = null) {
        return ExpertTeam::model()->getById($id, $with);
    }

    public function loadAllExpertTeamsByDiseaseId($diseaseId, $with = null, $options = null) {
        return ExpertTeam::model()->getAllByDiseaseId($diseaseId, $with, $options);
    }

    /*
      public function loadTeamByCode($code, $with = null) {
      $model = ExpertTeam::model()->getByCode($code, $with);
      if (is_null($model)) {
      throw new CHttpException(404, 'Record is not found.');
      }
      return $model;
      }
     * 
     */

    // loads all teams belonging to given faculty.
    public function loadTeamsByFacultyId($fid, $with = null, $options = null) {
        if (is_null($with)) {
            $with = array('expteamHospital', 'expteamLeader', 'expteamFaculty', 'expteamHpDept', 'expteamMembers', 'expteamCity');
        }
        return ExpertTeam::model()->getAllByFacultyId($fid, $with, $options);
    }

    // loads only ONE team belonging to given faculty.
    public function loadTeamByFacultyId($fid) {
        $model = ExpertTeam::model()->getByFacultyId($fid, $with);
        if (is_null($model)) {
            throw new CHttpException(404, 'Record is not found.');
        }
        return $model;
    }

    // loads all teams belonging to given city
    public function loadTeamsByCityId($cityId, $with = null, $options = null) {
        if (is_null($with)) {
            $with = array('expteamHospital', 'expteamFaculty', 'expteamHpDept', 'expteamMembers', 'expteamCity');
        }
        return ExpertTeam::model()->getAllByCityId($cityId, $with, $options);
    }

    public function convertToIExpertTeam(ExpertTeam $model, $attributes = null, $with = null) {
        if (isset($model)) {
            $imodel = new IExpertTeam();
            $imodel->initModel($model, $attributes);
            $imodel->addRelatedModel($model, $with);
            return $imodel;
        } else {
            return null;
        }
    }

    /**
     * @NOTE DO NOT REMOVE THIS METHOD.
     */
    /*
      public function loadTeamData() {
      $teamData = new ExpertTeamData();
      $teamList = $teamData->expertTeams;
      foreach ($teamList as $team) {
      // api url for viewing each team.
      $team->teamUrl = Yii::app()->createAbsoluteUrl('api/view', array('model' => 'expertteam', 'id' => $team->teamId));
      if ((isset($team->imageUrl) === false || $team->imageUrl == "") && isset($team->teamLeader)) {
      $team->imageUrl = $team->teamLeader->imageUrl;
      }
      unset($team->surgeries);
      unset($team->superiors);
      }

      return $teamList;
      }
     * 
     */
}
