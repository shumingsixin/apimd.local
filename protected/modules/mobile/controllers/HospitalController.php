<?php

class HospitalController extends MobileController {

    private $_model;

    public function actionIndex() {
        $this->render('index');
    }

    public function actionView($id) {
        $this->render('view');
    }

    public function actionDept($id) {
        $this->render('dept');
    }

    public function actionFacility() {
        $this->render('facility');
    }

    public function getHospitalList() {
        return array(1 => 'shrjyy', 2 => '', 3 => '', 4 => '', 5 => '', 6 => '', 7 => '', 8 => '', 9 => '', 10 => '');
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return Hospital the loaded model
     * @throws CHttpException
     */
    public function loadModel($id) {
        if ($this->_model === null) {
            $this->_model = Hospital::model()->getById($id);
            if ($this->_model === null)
                throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $this->_model;
    }

    private function parseQueryOptions($request) {
        $options = array();
        $options['limit'] = $request->getParam('limit', null);
        $options['offset'] = $request->getParam('offset', null);
        $options['order'] = $request->getParam('order', null);

        return $options;
    }

}
