<?php

class BookingSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new Booking();
    }

    public function getQueryFields() {
        return array('bookingRefNo', 'contactName', 'diseaseName', 'bkStatus', 'bkType', 'orderRefNo', 'orderType', 'finalAmount', 'dateOpen', 'dateClosed', 'bookingType', 'userAgent');
    }

    public function addQueryConditions() {
        $udpAlias = 's';
        $this->criteria->join = 'LEFT JOIN sales_order s ON (t.`id` = s.`bk_id` AND s.`bk_type` =1)';
        $this->criteria->distinct = true;
        if ($this->hasQueryParams()) {
            //patientBooking的参数
            if (isset($this->queryParams['bookingRefNo'])) {
                $bookingRefNo = $this->queryParams['bookingRefNo'];
                $this->criteria->compare('t.ref_no', $bookingRefNo, true);
            }
            if (isset($this->queryParams['contactName'])) {
                $contactName = $this->queryParams['contactName'];
                $this->criteria->compare('t.contact_name', $contactName, true);
            }
            if (isset($this->queryParams['diseaseName'])) {
                $diseaseName = $this->queryParams['diseaseName'];
                $this->criteria->compare('t.disease_name', $diseaseName, true);
            }
            if (isset($this->queryParams['bkStatus'])) {
                $bkStatus = $this->queryParams['bkStatus'];
                $this->criteria->compare("t.bk_status", $bkStatus);
            }
            if (isset($this->queryParams['bkType'])) {
                $bkType = $this->queryParams['bkType'];
                $this->criteria->compare("t.bk_type", $bkType);
            }
            if (isset($this->queryParams['userAgent'])) {
                $userAgent = $this->queryParams['userAgent'];
                $this->criteria->compare("t.user_agent", $userAgent);
            }
            if (isset($this->queryParams['orderRefNo'])) {
                $orderRefNo = $this->queryParams['orderRefNo'];
                $this->criteria->compare($udpAlias . ".ref_no", $orderRefNo, true);
            }
            if (isset($this->queryParams['orderType'])) {
                $orderType = $this->queryParams['orderType'];
                $this->criteria->compare($udpAlias . ".order_type", $orderType);
            }
            if (isset($this->queryParams['finalAmount'])) {
                $finalAmount = $this->queryParams['finalAmount'];
                $this->criteria->compare($udpAlias . ".final_amount", $finalAmount); // sql like condition
            }
            if (isset($this->queryParams['dateOpen'])) {
                $dateOpen = $this->queryParams['dateOpen'];
                $this->criteria->compare($udpAlias . ".date_open", $dateOpen, true); // sql like condition
            }
            if (isset($this->queryParams['dateClosed'])) {
                $dateClosed = $this->queryParams['dateClosed'];
                $this->criteria->compare($udpAlias . ".date_closed", $dateClosed, true); // sql like condition
            }
        }
    }

}
