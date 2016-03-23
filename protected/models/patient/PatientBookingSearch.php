<?php

class PatientBookingSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new PatientBooking();
    }

    public function getQueryFields() {
        return array('bookingRefNo', 'patientName', 'creatorName', 'doctorName', 'status', 'travelType', 'detail', 'isDepositPaid', 'orderRefNo', 'orderType', 'finalAmount', 'dateOpen', 'dateClosed', 'bkType', 'userAgent');
    }

    public function addQueryConditions() {
        $udpAlias = 's';
        $this->criteria->join = 'LEFT JOIN sales_order s ON (t.`id` = s.`bk_id` AND s.`bk_type` =2)';
        $this->criteria->distinct = true;
        if ($this->hasQueryParams()) {
            //patientBooking的参数
            if (isset($this->queryParams['bookingRefNo'])) {
                $bookingRefNo = $this->queryParams['bookingRefNo'];
                $this->criteria->compare('t.ref_no', $bookingRefNo, true);
            }
            if (isset($this->queryParams['patientName'])) {
                $patientName = $this->queryParams['patientName'];
                $this->criteria->compare('t.patient_name', $patientName, true);
            }
            if (isset($this->queryParams['creatorName'])) {
                $creatorName = $this->queryParams['creatorName'];
                $this->criteria->compare('t.creator_name', $creatorName, true);
            }
            if (isset($this->queryParams['doctorName'])) {
                $doctorName = $this->queryParams['doctorName'];
                $this->criteria->compare('t.doctor_name', $doctorName, true);
            }
            if (isset($this->queryParams['status'])) {
                $status = $this->queryParams['status'];
                $this->criteria->compare("t.status", $status);
            }
            if (isset($this->queryParams['travelType'])) {
                $travelType = $this->queryParams['travelType'];
                $this->criteria->compare("t.travel_type", $travelType);
            }
            if (isset($this->queryParams['detail'])) {
                $detail = $this->queryParams['detail'];
                $this->criteria->compare("t.detail", $detail, true);
            }

            if (isset($this->queryParams['isDepositPaid'])) {
                $isDepositPaid = $this->queryParams['isDepositPaid'];
                $this->criteria->compare("t.is_deposit_paid", $isDepositPaid);
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
