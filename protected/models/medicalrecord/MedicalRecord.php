<?php

/**
 * This is the model class for table "medical_record".
 *
 * The followings are the available columns in table 'medical_record':
 * @property integer $id
 * @property integer $user_id
 * @property string $name
 * @property integer $gender
 * @property string $dob
 * @property integer $state
 * @property string $city
 * @property integer $occupation
 * @property string $patient_condition 
 * @property string $drug_allergy
 * @property string $surgery_history
 * @property string $drug_history
 * @property string $disease_history
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property User $user
 */
class MedicalRecord extends EActiveRecord {

    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'medical_record';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('user_id, name, nric, gender, dob, state, city, occupation, patient_condition', 'required', 'message' => '请输入{attribute}'),
            array('user_id, gender, state, city, occupation', 'numerical', 'integerOnly' => true),
            array('dob', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('name', 'length', 'max' => 45),
            //  array('nric', 'numerical', 'integerOnly' => true, 'message' => '请输入正确的18位数中国身份证号码'),
            //   array('nric', 'length', 'is' => 18, 'message' => '请输入正确的18位数中国身份证号码'),
            array('patient_condition, surgery_history, drug_history, disease_history', 'length', 'max' => 500),
            array('drug_allergy, remark', 'length', 'max' => 200),
            //  array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, user_id, name, nric, gender, dob, state, city, occupation, patient_condition, drug_allergy, surgery_history, drug_history, disease_history, remark, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mrUser' => array(self::BELONGS_TO, 'User', 'user_id'),
            'mrState' => array(self::BELONGS_TO, 'RegionState', 'state'),
            'mrCity' => array(self::BELONGS_TO, 'RegionCity', 'city'),
            'mrFiles' => array(self::HAS_MANY, 'MedicalRecordFile', 'mr_id'),
            'mrBookings' => array(self::HAS_ONE, 'MedicalRecordBooking', 'mr_id'), //TODO: change HAS_ONE to HAS_MANY.           
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'user_id' => Yii::t('user', '用户名'),
            'name' => Yii::t('mr', '患者姓名'),
            'nric' => Yii::t('mr', '身份证号码'),
            'gender' => Yii::t('mr', '患者性别'),
            'dob' => Yii::t('mr', '出生日期'),
            'state' => Yii::t('mr', '所在省份'),
            'city' => Yii::t('mr', '所在城市'),
            'occupation' => Yii::t('mr', '患者的职业'),
            'patient_condition' => Yii::t('mr', '目前的病情'),
            'drug_allergy' => Yii::t('mr', '药物过敏'),
            'surgery_history' => Yii::t('mr', '以往的手术经历'),
            'drug_history' => Yii::t('mr', '以往的用药经历'),
            'disease_history' => Yii::t('mr', '以往的患病史'),
            'remark' => Yii::t('mr', '备注'),
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('gender', $this->gender);
        $criteria->compare('state', $this->state);
        $criteria->compare('city', $this->city);
        $criteria->compare('occupation', $this->occupation);
        $criteria->compare('patient_condition', $this->patient_condition, true);
        $criteria->compare('drug_allergy', $this->drug_allergy, true);
        $criteria->compare('surgery_history', $this->surgery_history, true);
        $criteria->compare('drug_history', $this->drug_history, true);
        $criteria->compare('disease_history', $this->disease_history, true);
        $criteria->compare('remark', $this->remark, true);
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
     * @return MedicalRecord the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Query Methods ******* */

    public function getAllFilesByReportType($rt) {
        return $this->mrFiles(array('condition' => 'date_deleted is NULL AND report_type=:rt', 'params' => array(':rt' => $rt), 'order' => 'mrFiles.display_order ASC'));
    }

    public function getOptionsGender() {
        return array(self::GENDER_MALE => '男', self::GENDER_FEMALE => '女');
    }

    public function getOptionsOccupation() {
        return CHtml::listData(CommonCode::model()->getAllOccupation(), 'id', 'name');
    }

    /*     * ****** Accessors ******* */

    public function getMrBookings() {
        return $this->mrBookings;
    }

    public function getStateName() {
        if (isset($this->mrState)) {
            return $this->mrState->getName();
        } else {
            return null;
        }
    }

    public function getCityName() {
        if (isset($this->mrCity)) {
            return $this->mrCity->getName();
        } else {
            return null;
        }
    }

    public function getOwnerUsername() {
        if (isset($this->mrUser)) {
            return $this->mrUser->getUsername();
        } else {
            return null;
        }
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getNRIC() {
        return $this->nric;
    }

    public function getGender() {
        $options = $this->getOptionsGender();
        if (isset($options[$this->gender])) {
            return $options[$this->gender];
        } else {
            return null;
        }
    }

    public function getPatientAge() {
        return calYearsFromDatetime($this->dob);    //from Helper.php
    }

    public function getDob() {
        return $this->getDateAttribute($this->dob);
    }

    public function getOccupation() {
        $occupation = CommonCode::model()->getOccupationById($this->occupation);
        if (isset($occupation)) {
            return $occupation->getName();
        } else {
            return null;
        }
    }

    public function getPatientCondition($ntext = false) {
        return $this->getTextAttribute($this->patient_condition, $ntext);
    }

    public function getDrugAllergy($ntext = false) {
        return $this->getTextAttribute($this->drug_allergy, $ntext);
    }

    public function getSurgeryHistory($ntext = false) {
        return $this->getTextAttribute($this->surgery_history, $ntext);
    }

    public function getDrugHistory($ntext = false) {
        return $this->getTextAttribute($this->drug_history, $ntext);
    }

    public function getDiseaseHistory($ntext = false) {
        return $this->getTextAttribute($this->disease_history, $ntext);
    }

    public function getRemark($ntext = false) {
        return $this->getTextAttribute($this->remark, $ntext);
    }

}
