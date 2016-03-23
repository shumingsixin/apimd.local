<?php

/**
 * This is the model class for table "medical_record_booking".
 *
 * The followings are the available columns in table 'medical_record_booking':
 * @property integer $id
 * @property string $ref_no;
 * @property integer $user_id
 * @property integer $mr_id
 * @property integer $faculty_id
 * @property string $mobile
 * @property integer $status
 * @property string $appt_date
 * @property string $buffer_days
 * @property string $patient_intention
 * @property string $email
 * @property string $wechat
 * @property string $subject
 * @property string $total_price
 * @property string $currency 
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class MedicalRecordBooking extends EActiveRecord {

    const BUFFER_THREE_DAYS = 3;  //3 days.
    const BUFFER_ONE_WEEK = 7;    //7 days.
    const BUFFER_TWO_WEEKS = 14;  //14 days.
    const STATUS_NEW = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_CANCELLED = 9;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'medical_record_booking';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('ref_no, user_id, mr_id, faculty_id, mobile, status, appt_date, buffer_days, patient_intention', 'required', 'message' => '请输入{attribute}'),
            array('user_id, mr_id, faculty_id, status, buffer_days', 'numerical', 'integerOnly' => true),
            array('ref_no', 'length', 'is' => 8),
            array('mobile', 'length', 'max' => 20),
            array('appt_date', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('wechat', 'length', 'max' => 45),
            array('patient_intention, subject, email', 'length', 'max' => 100),
            array('total_price', 'length', 'max' => 13),
            array('currency', 'length', 'is' => 3),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, ref_no, user_id, mr_id, faculty_id, mobile, status, appt_date, buffer_days, patient_intention, email, wechat, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mrbUser' => array(self::BELONGS_TO, 'User', 'user_id'),
            'mrbMedicalRecord' => array(self::BELONGS_TO, 'MedicalRecord', 'mr_id'),
            'mrbFaculty' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no' => Yii::t('mr', '预约号'),
            'user_id' => Yii::t('user', '用户名'),
            'mr_id' => Yii::t('mr', '相关病历'),
            'faculty_id' => Yii::t('mr', '科室'),
            'mobile' => Yii::t('user', '手机号码'),
            'status' => Yii::t('mr', '状态'),
            'appt_date' => Yii::t('mr', '期望就诊日期'),
            'buffer_days' => Yii::t('mr', '前后几天'),
            'patient_intention' => Yii::t('mr', '具体需求'),
            'email' => Yii::t('user', '邮箱'),
            'wechat' => Yii::t('user', '微信'),
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
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('mr_id', $this->mr_id);
        $criteria->compare('faculty_id', $this->faculty_id);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('appt_date', $this->appt_date, true);
        $criteria->compare('buffer_days', $this->buffer_days, true);
        $criteria->compare('patient_intention', $this->patient_intention, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('wechat', $this->wechat, true);
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
     * @return MedicalRecordBooking the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeValidate() {
        $this->createRefNumber();
        return parent::beforeValidate();
    }

    /*     * ****** Query Methods ******* */

    public function getByRefNo($refno) {
        return $this->getByAttributes(array('ref_no' => $refno));
    }

    public function getOptionsBufferDays() {
        return array(
            self::BUFFER_THREE_DAYS => Yii::t('mr', '前后三天'),
            self::BUFFER_ONE_WEEK => Yii::t('mr', '前后一周'),
            self::BUFFER_TWO_WEEKS => Yii::t('mr', '前后两周')
        );
    }

    public function getOptionsStatus() {
        return array(
            self::STATUS_NEW => Yii::t('mr', '新'),
            self::STATUS_CONFIRMED => Yii::t('mr', '已确认'),
            self::STATUS_CANCELLED => Yii::t('mr', '已取消'),
        );
    }

    /*     * ****** Accessors ******* */

    public function getOwnerUsername() {
        if (isset($this->mrbUser)) {
            return $this->mrbUser->getUsername();
        } else {
            return null;
        }
    }

    public function getFacultyName() {
        if (isset($this->mrbFaculty)) {
            return $this->mrbFaculty->getName();
        } else {
            return null;
        }
    }

    public function getUser() {
        return $this->mrbUser;
    }

    public function getFaculty() {
        return $this->mrbFaculty;
    }

    public function getMedicalRecord() {
        return $this->mrbMedicalRecord;
    }

    public function getRefNumber() {
        return $this->ref_no;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getFacultyId() {
        return $this->faculty_id;
    }

    public function getMrId() {
        return $this->mr_id;
    }

    public function getApptDate() {
        return $this->getDateAttribute($this->appt_date);
    }

    public function getBufferDays() {
        $options = $this->getOptionsBufferDays();
        if (isset($options[$this->buffer_days])) {
            return $options[$this->buffer_days];
        } else
            return '未知';
    }

    public function getPatientIntention($ntext = false) {
        return $this->getTextAttribute($this->patient_intention, $ntext);
    }

    public function getStatus() {
        $options = $this->getOptionsStatus();
        if (isset($options[$this->status])) {
            return $options[$this->status];
        } else {
            return '未知';
        }
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getWechat() {
        return $this->wechat;
    }

    public function getSubject() {
        //return '预约 - ' . $this->getFacultyName();
        return $this->subject;
    }

    public function getTotalPrice() {
        return $this->total_price;
    }

    public function getCurrency() {
        return $this->currency;
    }

    /*     * ****** Private Methods ******* */

    private function createRefNumber() {
        if (is_null($this->ref_no)) {
            $this->ref_no = mt_rand(10000000, 99999999);
        }
    }

}
