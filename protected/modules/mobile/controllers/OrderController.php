<?php

class OrderController extends MobileController {

    public function actionView($refNo) {
        $apiSvc = new ApiViewSalesOrder($refNo);
        $output = $apiSvc->loadApiViewData();
        $this->render('view', array(
            'data' => $output
        ));
    }

}
