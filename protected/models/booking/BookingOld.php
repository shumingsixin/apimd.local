<?php

/**
 * This is the model class for table "booking".
 *
 * The followings are the available columns in table 'booking':
 * @property integer $id
 * @property string $ref_no
 * @property integer $user_id
 * @property string $mobile
 * @property string $contact_name
 * @property string $booking_type
 * @property integer $faculty_id
 * @property integer $doctor_id
 * @property integer $expteam_id
 * @property integer $hospital_id
 * @property string $hospital_dept
 * @property string $booking_target
 * @property integer $status
 * @property string $patient_condition
 * @property string $appt_date
 * @property string $appt_date_str
 * @property string $contact_email
 * @property string $contact_weixin
 * @property string $remark
 * @property string $user_host_ip
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property Faculty $faculty
 * @property BookingFile[] $bookingFiles
 */
class BookingOld extends EActiveRecord {

    const STATUS_NEW = 1;
    const STATUS_CONFIRMED = 2;
    const STATUS_PAID = 3;
    const STATUS_CANCELLED = 9;
    const BOOKING_TYPE_FACULTY = 1;     // 预约科室
    const BOOKING_TYPE_DOCTOR = 2;      // 预约医生
    const BOOKING_TYPE_EXPERTTEAM = 3;  // 预约专家团队
    const BOOKING_TYPE_HOSPITAL = 4;    // 预约医院

    /**
     * @return string the associated database table name
     */

    public function tableName() {
        return 'booking_old';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('ref_no, mobile, booking_type, contact_name, status', 'required', 'message' => '请输入{attribute}'),
            array('user_id, booking_type, faculty_id, doctor_id, expteam_id, hospital_id, hospital_dept, status', 'numerical', 'integerOnly' => true),
            array('ref_no', 'length', 'is' => 12),
            array('mobile', 'length', 'is' => 11),
            array('appt_date', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('contact_name, booking_target', 'length', 'max' => 45),
            array('patient_condition, remark', 'length', 'max' => 500),
            array('contact_email, contact_weixin, appt_date_str', 'length', 'max' => 100),
            array('user_host_ip', 'length', 'max' => 15),
            array('appt_date, date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, ref_no, user_id, mobile, booking_type, faculty_id, doctor_id, expteam_id, hospital_id, hospital_dept, booking_target, status, patient_condition, appt_date, contact_email, contact_weixin, user_host_ip, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'owner' => array(self::BELONGS_TO, 'User', 'user_id'),
            'bookingFiles' => array(self::HAS_MANY, 'BookingFile', 'booking_id'),
            'countFiles' => array(self::STAT, "BookingFile", 'booking_id'),
            'facultyBooked' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
            'doctorBooked' => array(self::BELONGS_TO, 'Doctor', 'doctor_id'),
            'expertTeamBooked' => array(self::BELONGS_TO, 'ExpertTeam', 'expteam_id'),
            'hospitalBooked' => array(self::BELONGS_TO, 'Hospital', 'hospital_id'),
            "hospitalDeptBooked" => array(self::BELONGS_TO, 'HospitalDepartment', 'hp_dept_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'ref_no' => '预约号',
            'user_id' => '用户 ID',
            'mobile' => '手机',
            "booking_type" => "预约分类",
            'contact_name' => '称呼',
            'faculty_id' => '科室',
            "doctor_id" => "医生",
            "expteam_id" => "专家团队",
            "hospital_id" => "医院",
            "hospital_dept" => "医院科室",
            'booking_target' => '预约对象',
            'status' => '状态',
            'patient_condition' => '病情',
            'appt_date' => '就诊日期',
            'appt_date_str' => Yii::t('booking', '就诊日期'),
            'contact_email' => '邮箱',
            'contact_weixin' => '微信',
            'remark' => '备注',
            'user_host_ip' => 'User Host Ip',
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
        $criteria->addCondition("date_deleted is NULL");
        $criteria->compare('id', $this->id);
        $criteria->compare('ref_no', $this->ref_no, true);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('contact_name', $this->contact_name, true);
        $criteria->compare('faculty_id', $this->faculty_id);
        $criteria->compare('expteam_id', $this->expteam_id, true);
        $criteria->compare('doctor_id', $this->doctor_id, true);
        $criteria->compare('hospital_id', $this->hospital_id, true);
        $criteria->compare('hospital_dept', $this->hospital_dept, true);
        $criteria->compare('booking_type', $this->booking_type, true);
        $criteria->compare('status', $this->status);
        $criteria->compare('patient_condition', $this->patient_condition, true);
        $criteria->compare('appt_date', $this->appt_date, true);
        $criteria->compare('contact_email', $this->contact_email, true);
        $criteria->compare('contact_weixin', $this->contact_weixin, true);
        $criteria->compare('user_host_ip', $this->user_host_ip, true);
        $criteria->compare('date_created', $this->date_created, true);
        $criteria->compare('date_updated', $this->date_updated, true);
        $criteria->compare('date_deleted', $this->date_deleted, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 20, //设置每页显示20条
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Booking the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function beforeValidate() {
        $this->createRefNumber();
        return parent::beforeValidate();
    }

    public function beforeSave() {

        return parent::beforeSave();
    }

    /*     * ****** Query Methods ******* */

    public function getByRefNo($refno) {
        return $this->getByAttributes(array('ref_no' => $refno));
    }

    public function getByIdAndUserId($id, $userId, $with = null) {
        return $this->getByAttributes(array('id' => $id, 'user_id' => $userId), $with);
    }

    public function getByIdAndUser($id, $userId, $mobile, $with = null) {
        $criteria = new CDbCriteria();
        $criteria->addCondition("t.date_deleted is NULL");
        $criteria->compare('t.id', $id);
        $criteria->compare("t.mobile", $mobile, false, 'OR');
        $criteria->compare("t.user_id", $userId, false, 'AND');
        $criteria->distinct=true;
        if (isset($with) && is_array($with))
            $criteria->with = $with;
        return $this->find($criteria);
    }

    public function getAllByUserIdOrMobile($userId, $mobile, $with = null, $options = null) {
        $criteria = new CDbCriteria();
        $criteria->compare("t.user_id", $userId, false, 'AND');
        $criteria->compare("t.mobile", $mobile, false, 'OR');
        $criteria->addCondition("t.date_deleted is NULL");
        if (isset($with) && is_array($with))
            $criteria->with = $with;
        if (isset($options['offset']))
            $criteria->offset = $options['offset'];
        if (isset($options['limit']))
            $criteria->limit = $options['limit'];
        if (isset($options['order']))
            $criteria->order = $options['order'];

        return $this->findAll($criteria);
    }

    public function getOptionsStatus() {
        return array(
            self::STATUS_NEW => Yii::t('booking', '新'),
            self::STATUS_CONFIRMED => Yii::t('booking', '已确认'),
            self::STATUS_PAID => Yii::t('booking', '已付款'),
            self::STATUS_CANCELLED => Yii::t('booking', '已取消'),
        );
    }

    public function getStatusText() {
        $options = $this->getOptionsStatus();
        if (isset($options[$this->status])) {
            return $options[$this->status];
        } else {
            return '未知';
        }
    }

    public function getOptionsBookingType() {
        return array(
            self::BOOKING_TYPE_FACULTY => "科室",
            self::BOOKING_TYPE_DOCTOR => "医生",
            self::BOOKING_TYPE_EXPERTTEAM => "专家团队",
            self::BOOKING_TYPE_HOSPITAL => "医院"
        );
    }

    public function getBookingTypeText() {
        $options = $this->getOptionsBookingType();
        if (isset($options[$this->booking_type])) {
            return $options[$this->booking_type];
        } else {
            return "未知";
        }
    }

    public function hasBookingTarget() {
        return (empty($this->booking_target) === false);
    }

    /*     * ****** Private Methods ******* */

    private function createRefNumber() {
        if ($this->isNewRecord) {
            $flag = true;
            while ($flag) {
                $refNumber = $this->getRefNumberPrefix() . date("ymd") . str_pad(mt_rand(0, 9999), 4, "0", STR_PAD_LEFT);
                if ($this->exists('t.ref_no =:refno', array(':refno' => $refNumber)) == false) {
                    $this->ref_no = $refNumber;
                    $flag = false;
                }
            }
        }
    }

    /**
     * Return ref_no prefix charactor based on booking_type
     * default 'AA' is an eception charactor
     * @return string
     */
    private function getRefNumberPrefix() {
        switch ($this->booking_type) {
            case self::BOOKING_TYPE_FACULTY :
                return "FC";
            case self::BOOKING_TYPE_DOCTOR :
                return "DR";
            case self::BOOKING_TYPE_EXPERTTEAM :
                return "ET";
            case self::BOOKING_TYPE_HOSPITAL :
                return "HP";
            default:
                return "AA";
        }
    }

    /*     * ****** Accessors ******* */

    public function getOwnerUsername() {
        if (isset($this->owner)) {
            return $this->owner->getUsername();
        } else {
            return null;
        }
    }

    public function getFacultyName() {
        if (isset($this->facultyBooked)) {
            return $this->facultyBooked->getName();
        } else {
            return "";
        }
    }

    public function getOwner() {
        return $this->owner;
    }

    public function getBookingFiles() {
        return $this->bookingFiles;
    }

    public function getCountFiles() {
        return $this->countFiles;
    }

    public function getFacultyBooked() {
        return $this->facultyBooked;
    }

    public function getDoctorBooked() {
        return $this->doctorBooked;
    }

    public function getExpertTeamBooked() {
        return $this->expertTeamBooked;
    }

    public function getHospitalBooked() {
        return $this->hospitalBooked;
    }

    public function getHospitalDeptBooked() {
        return $this->hospitalDeptBooked;
    }

    public function getRefNumber() {
        return $this->ref_no;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getBookingType() {
        return $this->booking_type;
    }

    public function getContactName() {
        return $this->contact_name;
    }

    public function getFacultyId() {
        return $this->faculty_id;
    }

    public function getDoctorId() {
        return $this->doctor_id;
    }

    public function getExpertTeamId() {
        return $this->expteam_id;
    }

    public function getHospitalId() {
        return $this->hospital_id;
    }

    public function getHospitalDeptId() {
        return $this->hospital_dept;
    }

    public function getApptDate() {
        return $this->getDateAttribute($this->appt_date);
    }

    public function getApptDateStr() {
        return $this->appt_date_str;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getContactEmail() {
        return $this->contact_email;
    }

    public function getContactWeixin() {
        return $this->contact_weixin;
    }

    public function getTotalPrice() {
        return $this->total_price;
    }

    public function getBookingTarget() {
        return $this->booking_target;
    }

    public function getPatientCondition($ntext = false) {
        return $this->getTextAttribute($this->patient_condition, $ntext);
    }

    public function getRemark($ntext = false) {
        return $this->getTextAttribute($this->remark, $ntext);
    }

}
