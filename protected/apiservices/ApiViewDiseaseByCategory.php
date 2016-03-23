<?php

class ApiViewDiseaseByCategory extends EApiViewService {
    private $cate_id;
    public function __construct($id) {
        parent::__construct();
        $this->cate_id = (int) $id;
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
        $models = CategoryDiseaseJoin::model()->getAllBySubCatId($this->cate_id);
        $this->setDisease($models);
    }
    private function setDisease($models){
        foreach ($models as $model) {
            $disease = $model->getDisease();
            $data = new stdClass();
            $data->id = $disease->getId();
            $data->name = $disease->getName();
            $this->results->disease[] = $data;
        }
    }

}
