<?php

/**
 * Description of TaskManager
 *
 * @author haley
 */
class TaskManager {

    const CRM_URL = 'http://admin.mingyizhudao.com';
    public function createTaskBooking(AdminBooking $model) {
        $adminTask = new AdminTask();

        $adminTask->subject = '您有一条新的任务，预约编号：' . $model->ref_no;
        $adminTask->content = $model->disease_detail;
        $adminTask->url = self::CRM_URL . "/admin/adminBooking/view/id/".$model->getId();

        $dbTran = Yii::app()->db->beginTransaction();
        try {
            if ($adminTask->save() === false) {

                throw new CException("Error saving adminTask");
            }
            $adminTaskJoin = new AdminTaskJoin();
            $adminTaskJoin->admin_task_id = $adminTask->getId();
            $adminTaskJoin->admin_user_id = $model->admin_user_id;
            $adminTaskJoin->work_type = AdminTaskJoin::WORK_TYPE_TEL;
            $adminTaskJoin->type = AdminTaskJoin::TASK_TYPE_BK;
            if ($adminTaskJoin->save() === false) {
                throw new CException("Error saving adminTask");
            }
            $adminTaskBkJoin = new AdminTaskBkJoin();
            $adminTaskBkJoin->admin_task_join_id = $adminTaskJoin->getId();
            $adminTaskBkJoin->admin_booking_id = $model->getId();
            if ($adminTaskBkJoin->save() === false) {
                throw new CException("Error saving adminTaskBkJoin");
            }
            $dbTran->commit();
        } catch (CDbException $cdbex) {
            $dbTran->rollback();
            return false;
        } catch (CException $cex) {
            $dbTran->rollback();
            return false;
        }

        return true;
    }

    public function createTaskPlan(AdminBooking $model, $values) {
        $adminTask = new AdminTask();

        $adminTask->subject = '您有一条新的任务，预约编号：' . $model->ref_no;
        $adminTask->content = $values['content'];
        $adminTask->url = self::CRM_URL . "/admin/adminBooking/view/id/".$model->getId();

        $dbTran = Yii::app()->db->beginTransaction();
        try {
            if ($adminTask->save() === false) {

                throw new CException("Error saving adminTask");
            }
            $adminTaskJoin = new AdminTaskJoin();
            $adminTaskJoin->date_plan = $values['date_plan'];
            $adminTaskJoin->admin_task_id = $adminTask->getId();
            $adminTaskJoin->admin_user_id = $values['admin_user_id'];
            $adminTaskJoin->work_type = $values['work_type'];
            $adminTaskJoin->type = AdminTaskJoin::TASK_TYPE_BK;

            if ($adminTaskJoin->save() === false) {

                throw new CException("Error saving adminTask");
            }

            $adminTaskBkJoin = new AdminTaskBkJoin();
            $adminTaskBkJoin->admin_task_join_id = $adminTaskJoin->getId();
            $adminTaskBkJoin->admin_booking_id = $model->getId();
            if ($adminTaskBkJoin->save() === false) {
                throw new CException("Error saving adminTaskBkJoin");
            }
            $dbTran->commit();
        } catch (CDbException $cdbex) {
            $dbTran->rollback();
            return false;
        } catch (CException $cex) {
            $dbTran->rollback();
            return false;
        }

        return true;
    }

    /**
     * 付款完成
     */
    public function createTaskOrder(SalesOrder $model) {
        $adminTask = new AdminTask();

        $adminTask->subject = $model->subject;
        $adminTask->content = $model->description . '已支付完成';
        $adminTask->url = self::CRM_URL . "/admin/order/view/id/".$model->getId();

        $dbTran = Yii::app()->db->beginTransaction();
        try {
            if ($adminTask->save() === false) {
                throw new CException("Error saving adminTask");
            }

            $adminTaskJoin = new AdminTaskJoin();
            $adminTaskJoin->admin_task_id = $adminTask->getId();
            $adminBooking = AdminBooking::model()->getByAttributes(array('booking_id'=>$model->bk_id, 'booking_type'=>$model->bk_type));
            if($adminBooking){
                $adminTaskJoin->admin_user_id = $adminBooking->admin_user_id;
            }

            $adminTaskJoin->work_type = AdminTaskJoin::WORK_TYPE_TEL;
            $adminTaskJoin->type = AdminTaskJoin::TASK_TYPE_ORDER;
            if ($adminTaskJoin->save() === false) {
                throw new CException("Error saving adminTask");
            }

            $adminTaskOrderJoin = new AdminTaskOrderJoin();
            $adminTaskOrderJoin->admin_task_join_id = $adminTaskJoin->getId();
            $adminTaskOrderJoin->order_id = $model->getId();
            if ($adminTaskOrderJoin->save() === false) {
                throw new CException("Error saving adminTaskBkJoin");
            }
            $dbTran->commit();
        } catch (CDbException $cdbex) {
            $dbTran->rollback();
            return false;
        } catch (CException $cex) {
            $dbTran->rollback();
            return false;
        }

        return true;
    }

    /**
     * md端 医生上传照片
     */
    public function createTaskDoctor(UserDoctorProfile $model) {
        $adminTask = new AdminTask();

        $adminTask->subject = '上传照片';
        $adminTask->content = $model->name . ':' .  $model->hospital_name . '-' . $model->hp_dept_name;
        $adminTask->url = self::CRM_URL . "/admin/user/view/id/".$model->getId();

        $dbTran = Yii::app()->db->beginTransaction();
        try {
            if ($adminTask->save() === false) {
                throw new CException("Error saving adminTask");
            }

            $adminTaskJoin = new AdminTaskJoin();
            $adminTaskJoin->admin_task_id = $adminTask->getId();
            $adminUser = $this->getAdminUser($model->city_id, $model->state_id, AdminBooking::BK_TYPE_PB, AdminUser::ROLE_CS);
            $adminTaskJoin->admin_user_id = $adminUser->admin_user_id;
            $adminTaskJoin->work_type = AdminTaskJoin::WORK_TYPE_TEL;
            $adminTaskJoin->type = AdminTaskJoin::TASK_TYPE_USER_DR;
            if ($adminTaskJoin->save() === false) {
                throw new CException("Error saving adminTask");
            }

            $adminTaskDoctorJoin = new AdminTaskDoctorJoin();
            $adminTaskDoctorJoin->admin_task_join_id = $adminTaskJoin->getId();
            $adminTaskDoctorJoin->doctor_id = $model->getId();
            if ($adminTaskDoctorJoin->save() === false) {
                throw new CException("Error saving adminTaskBkJoin");
            }
            $dbTran->commit();
        } catch (CDbException $cdbex) {
            $dbTran->rollback();
            return false;
        } catch (CException $cex) {
            $dbTran->rollback();
            return false;
        }

        return true;

    }

    /*
     * 获取adminbooking相关的追单任务
     */

    public function loadAdminTaskByAdminBookingId($adminBooingId, $isDone) {
        $adminTaskBkJoin = AdminTaskBkJoin::model()->loadllAdminBkJoinByAdminBookingId($adminBooingId, $isDone);
        $data = array();
        foreach ($adminTaskBkJoin as $v) {
            $adminTaskJoin = $v->getAdminTaskJoin();
            $adminTask = AdminTask::model()->getById($adminTaskJoin->admin_task_id);
            $taskPlan = new stdClass();
            $taskPlan->id = $v->id;
            $taskPlan->taskJoinId = $adminTaskJoin->id;
            $taskPlan->date_plan = $adminTaskJoin->date_plan;
            $taskPlan->admin_user = AdminUser::model()->getById($adminTaskJoin->admin_user_id)->username;
            $taskPlan->content = $adminTask->content;
            $taskPlan->date_done = $adminTaskJoin->date_done;
            $data[] = $taskPlan;
        }
        return $data;
    }

    public function getAdminUser($cityId, $stateId, $bkType, $role) {
        //若城市和省会为空 则找默认人员 因地推无默认 所以无需判断
        if (strIsEmpty($cityId) && strIsEmpty($stateId)) {
            return AdminUserRegionJoin::model()->getDefaultUser($bkType, $role);
        }
        //若城市和省会不为空的情况  查找顺序依次为城市 省会 默认
        $cityUser = AdminUserRegionJoin::model()->getByCityIdAndBookingTypeAndRole($cityId, $bkType, $role);

        if (isset($cityUser)) {
            return $cityUser;
        } else {

            $stateUser = AdminUserRegionJoin::model()->getByStateIdAndBookingTypeAndRole($stateId, $bkType, $role);

            if (isset($stateUser)) {
                return $stateUser;
            } else {
                return AdminUserRegionJoin::model()->getDefaultUser($bkType, $role);
            }
        }
    }



}
