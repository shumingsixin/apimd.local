<?php

class ApiViewDiseaseName extends EApiViewService {
    public function __construct($values) {
        parent::__construct();
        $this->disease_name = isset($values['disease_name']) ? $values['disease_name'] : null;
    }

    protected function loadData() {
        $this->loadDisease();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }
    public function loadDisease(){
        $disease = new Disease();
        $model = $disease->getByName($this->disease_name);
        $data = new stdClass();
        if (isset($model)) {
            $data->id = $model->getId();
            $data->name = $model->getName();
        }
        $this->setDisease($data);
    }
    private function setDisease($data){
        $this->results = $data;
    }

}
