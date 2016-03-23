<?php

class ApiViewExpertTeam extends EApiViewService {

    private $id;
    private $expertTeam;

    public function __construct($id) {
        parent::__construct();
        $this->id = $id;
    }

    protected function loadData() {
        $this->loadExpertTeam();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'expertTeam' => $this->expertTeam,
            );
        }
    }

    private function loadExpertTeam() {
        if (is_null($this->expertTeam)) {
            $teamMgr = new ExpertTeamManager();
            $with = array('expteamHospital', 'expteamHpDept', 'expteamCity', 'expteamMembers' => array('with' => 'doctorHospital'),);
            //@TODO: Do not user IExpertTeam. Re-do this method.
            $expteam = $teamMgr->loadIExpertTeamById($this->id, $with);
            if (is_null($expteam)) {
                $this->throwNoDataException();
            }
            $this->setExpertTeam($expteam);
        }
    }

    private function setExpertTeam(IExpertTeam $model) {
        //$model->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/home/enquiry', array('expteam' => $model->id));
        $model->bookingUrl = Yii::app()->createAbsoluteUrl('/mobile/booking/create', array('tid' => $model->id, 'header' => 0, 'footer' => 0));
        $this->expertTeam = $model;
    }

}
