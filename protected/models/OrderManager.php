<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of OrderManager
 *
 * @author shuming
 */
class OrderManager {

    public function createSalesOrder($model) {
        $order = new SalesOrder();
        $order->order_type = SalesOrder::ORDER_TYPE_DEPOSIT;
        if ($model instanceof PatientBooking) {
            $order->createRefNo($model->ref_no, $model->id, StatCode::TRANS_TYPE_PB);
            $order->user_id = $model->creator_id;
            $order->travel_type = $model->travel_type;
            //根据patient_id查询其资料
            if (strIsEmpty($model->patient_id) === false) {
                $patient = PatientInfo::model()->getById($model->patient_id);
                if (isset($patient)) {
                    $order->patient_mobile = $patient->mobile;
                    $order->patient_age = $patient->age . '岁' . strIsEmpty($patient->age_month) ? 0 : $patient->age_month . '月';
                    $order->patient_name = $patient->name;
                    $order->patient_state = $patient->state_name;
                    $order->patient_city = $patient->city_name;
                    $order->disease_name = $patient->disease_name;
                    $order->disease_detail = $patient->disease_detail;
                }
            }
            //根据creator_id 查询其所在地址
            $userCreator = UserDoctorProfile::model()->getByUserId($model->creator_id);
            if (isset($userCreator)) {
                $order->pb_creator_name = $userCreator->name;
                $stateName = $userCreator->getStateName();
                if ($stateName == '北京' || $stateName == '天津' || $stateName == '上海' || $stateName == '重庆') {
                    $bdCode = $stateName;
                } else {
                    $bdCode = $stateName . $userCreator->getCityName();
                }
                $order->bd_code = $bdCode;
            }
            if (strIsEmpty($model->doctor_id) === false) {
                $userDoctor = UserDoctorProfile::model()->getByUserId($model->doctor_id);
                if (isset($userDoctor)) {
                    $order->creator_doctor_name = $userDoctor->name;
                    $order->creator_hospital_name = $userDoctor->hospital_name;
                    $order->creator_dept_name = $userDoctor->hp_dept_name;
                }
            }
            $order->bk_type = StatCode::TRANS_TYPE_PB;
            if ($model->travel_type == StatCode::BK_TRAVELTYPE_DOCTOR_COME) {
                $order->order_type = SalesOrder::ORDER_TYPE_SERVICE;
            }
            $order->subject = $order->getOrderType(true) . '-' . $model->getPatientName();
            $order->description = '预约号:' . $model->getRefNo() . '。' . $model->getTravelType(true) . '所支付的' . $order->subject . '! 订单号:' . $order->ref_no;
        } elseif ($model instanceof Booking) {
            $order->createRefNo($model->ref_no, $model->id, StatCode::TRANS_TYPE_BK);
            $order->user_id = $model->getUserId();
            $order->bk_type = StatCode::TRANS_TYPE_BK;
            $order->subject = $order->getOrderType(true) . '-' . $model->getContactName();
            $order->disease_name = $model->disease_name;
            $order->disease_detail = $model->disease_detail;
            $order->expected_doctor_id = $model->doctor_id;
            $order->expected_doctor_name = $model->doctor_name;
            $order->expected_hospital_name = $model->hospital_name;
            $order->expected_dept_name = $model->hp_dept_name;
            $order->patient_name = $model->contact_name;
            $order->patient_mobile = $model->mobile;
            if (strIsEmpty($model->getExpertNameBooked())) {
                $description = $model->getDiseaseDetail();
            } else {
                $description = '支付给"' . $model->getExpertNameBooked() . '"的预约金';
            }
            $order->description = '预约号:' . $model->getRefNo() . '。' . $description . '!  订单号:' . $order->ref_no;
        } elseif ($model instanceof AdminBooking) {
            $order->createRefNo($model->ref_no, $model->id, AdminBooking::BK_TYPE_CRM);
            $order->bk_type = AdminBooking::BK_TYPE_CRM;
            $order->patient_mobile = $model->patient_mobile;
            $order->patient_age = $model->patient_age;
            $order->patient_name = $model->patient_name;
            $order->patient_state = $model->patient_state;
            $order->patient_city = $model->patient_city;
            $order->disease_name = $model->disease_name;
            $order->disease_detail = $model->disease_detail;
            $order->expected_doctor_name = $model->expected_doctor_name;
            $order->expected_hospital_name = $model->expected_hospital_name;
            $order->expected_hp_dept_name = $model->expected_hp_dept_name;
            $order->creator_doctor_name = $model->creator_doctor_name;
            $order->creator_hospital_name = $model->creator_hospital_name;
            $order->creator_dept_name = $model->creator_dept_name;
            $order->admin_user_name = $model->admin_user_name;
            $order->bd_code = $model->bd_user_name;
            $order->subject = $order->getOrderType(true) . '-' . $model->patient_name;
            $order->description = '预约号:' . $model->ref_no . '。' . $model->getTravelType(true) . '所支付的' . $order->subject . '! 订单号:' . $order->ref_no;
        } else {
            throw new CException('Unknown class');
        }
        $order->bk_ref_no = $model->ref_no;
        $order->is_paid = SalesOrder::ORDER_UNPAIDED;
        $order->bk_id = $model->getId();
        $order->created_by = Yii::app()->user->id;
        $order->date_open = date('Y-m-d H:i:s');
        $order->setAmount($order->getOrderTypeDefaultAmount());
        $order->save();
        return $order;
    }

    //查询预约单的支付情况
    public function loadSalesOrderByBkIdAndBkTypeAndOrderType($bkId, $bkType = StatCode::TRANS_TYPE_PB, $orderType = SalesOrder::ORDER_TYPE_DEPOSIT, $attributes = '*', $with = null, $options = null) {
        return SalesOrder::model()->getByBkIdAndBkTypeAndOrderType($bkId, $bkType, $orderType, $attributes, $with, $options);
    }

    //查询预约单的所有支付情况
    public function loadSalesOrderByBkIdAndBkType($bkId, $bkType = StatCode::TRANS_TYPE_PB, $attributes = '*', $with = null, $options = null) {
        return SalesOrder::model()->getByBkIdAndBkType($bkId, $bkType, $attributes, $with, $options);
    }

}
