<?php

/**
 * This is the model class for table "admin_task_join".
 *
 * The followings are the available columns in table 'admin_task_join':
 * @property integer $id
 * @property integer $admin_task_id
 * @property integer $admin_user_id
 * @property integer $work_type
 * @property integer $status
 * @property string $date_plan
 * @property string $date_done
 * @property integer $is_read
 * @property string $date_read
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AdminTaskJoin extends EActiveRecord {

    const NOT_READ = 0;
    const IS_READ = 1;
    const STATUS_NO = 0;
    const STATUS_OK = 1;
    const WORK_TYPE_TEL = 1;
    const TASK_TYPE_BK = 1;
    const TASK_TYPE_USER_DR = 2;
    const TASK_TYPE_ORDER = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'admin_task_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('admin_task_id, admin_user_id, date_created', 'required'),
            array('admin_task_id, admin_user_id, work_type, status, is_read', 'numerical', 'integerOnly' => true),
            array('date_plan, date_done, date_read, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, admin_task_id, admin_user_id, work_type, status, date_plan, date_done, is_read, date_read, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'adminTask' => array(self::BELONGS_TO, 'AdminTask', 'admin_task_id'),
        );
    }

    public function getAdminTask()
    {
        return $this->adminTask;
    }
    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'admin_task_id' => 'admin_msg.id',
            'admin_user_id' => 'admin_user.id',
            'work_type' => '跟单方式',
            'status' => '完成状态 0未完成 1已完成',
            'date_plan' => '计划跟单时间',
            'date_done' => '跟单完成时间',
            'is_read' => '是否已读',
            'date_read' => '阅读时间',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id);
        $criteria->compare('admin_task_id', $this->admin_task_id);
        $criteria->compare('admin_user_id', $this->admin_user_id);
        $criteria->compare('work_type', $this->work_type);
        $criteria->compare('status', $this->status);
        $criteria->compare('date_plan', $this->date_plan, true);
        $criteria->compare('date_done', $this->date_done, true);
        $criteria->compare('is_read', $this->is_read);
        $criteria->compare('date_read', $this->date_read, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return AdminTaskJoin the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * 获取新任务
     */
    public function getNewTask($adminUserId) {
        $models = $this->getAllByAttributes(array('admin_user_id' => $adminUserId, 'is_read' => 0));
        return $models;
    }

    /**
     * 获取未完成任务
     */
    public function getUndoneTask($adminUserId) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->addCondition("t.date_done is NULL");
        $criteria->addCondition("t.admin_user_id=:adminUserId");
        $criteria->params[":adminUserId"] = $adminUserId;

        return $this->findAll($criteria);
    }

    /**
     * 获取推送任务
     */
    public function getPlanTask($adminUserId) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->addCondition("t.admin_user_id=:adminUserId");
        $criteria->params[":adminUserId"] = $adminUserId;
        $criteria->addCondition("abs(UNIX_TIMESTAMP(now())- UNIX_TIMESTAMP(date_plan)) < 30");
        $criteria->join = 'left join admin_task a on (t.`admin_task_id`=a.`id`)';

        return $this->findAll($criteria);
    }

    public static function getOptionsWorkType() {
        return array(
            self::WORK_TYPE_TEL => '电话',
        );
    }

    public function getWorkType($v = true) {
        if ($v) {
            $options = self::getOptionsWorkType();
            if (isset($options[$this->work_type])) {
                return $options[$this->work_type];
            } else {
                return null;
            }
        }
        return $this->work_type;
    }

    public static function getOptionsType() {
        return array(
            self::TASK_TYPE_BK => '预约',
            self::TASK_TYPE_ORDER => '订单',
            self::TASK_TYPE_USER_DR => '医生用户',
        );
    }

    public static function getReadType() {
        return array(
            self::NOT_READ => '未阅读',
            self::IS_READ => '已阅读',
        );
    }

    public static function getStatusType() {
        return array(
            self::STATUS_OK => '已完成',
            self::STATUS_NO => '未完成',
        );
    }

    public function getType($v = true) {
        if ($v) {
            $options = self::getOptionsType();
            if (isset($options[$this->type])) {
                return $options[$this->type];
            } else {
                return null;
            }
        }
        return $this->type;
    }

    public function getStatus($v = true) {
        if ($v) {
            $options = self::getStatusType();
            if (isset($options[$this->status])) {
                return $options[$this->status];
            } else {
                return '';
            }
        }
        return $this->status;
    }

    public function getRead($v = true) {
        if ($v) {
            $options = self::getReadType();
            if (isset($options[$this->is_read])) {
                return $options[$this->is_read];
            } else {
                return '';
            }
        }
        return $this->is_read;
    }

    public function getDatePlan() {
        return $this->date_plan;
    }

    public function getDateDone() {
        return $this->date_done;
    }

}
