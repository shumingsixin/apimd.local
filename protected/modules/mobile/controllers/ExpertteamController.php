<?php

class ExpertteamController extends MobileController {

    public function actionIndex() {
        $this->render("index");
    }

    public function actionView($id) {        
        $this->render('view');
    }

    public function actionDetail($code) {
        $viewFile = 'details/' . $code;
        $this->renderPartial($viewFile);
    }

}
