<?php

/**
 * This is the model class for table "patient_mr".
 *
 * The followings are the available columns in table 'patient_mr':
 * @property integer $id
 * @property integer $patient_id
 * @property integer $creator_id
 * @property string $disease_name
 * @property string $disease_detail
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class PatientMR extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'patient_mr';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('patient_id, creator_id, disease_name, disease_detail', 'required'),
            array('patient_id, creator_id', 'numerical', 'integerOnly' => true),
            array('disease_name', 'length', 'max' => 50),
            array('disease_detail', 'length', 'max' => 1000),
            array('remark', 'length', 'max' => 500),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, patient_id, creator_id, disease_name, disease_detail, remark, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'pmrPatient' => array(self::BELONGS_TO, 'PatientInfo', 'patient_id'),
            'pmrCreator' => array(self::BELONGS_TO, 'User', 'creator_id'),
            'pmrFiles' => array(self::HAS_MANY, 'PatientMRFile', 'mr_id')
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'patient_id' => '患者',
            'creator_id' => '创建者',
            'disease_name' => '疾病诊断',
            'disease_detail' => '病史描述',
            'remark' => '备注',
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
        $criteria->compare('patient_id', $this->patient_id);
        $criteria->compare('creator_id', $this->creator_id);
        $criteria->compare('disease_name', $this->disease_name, true);
        $criteria->compare('disease_detail', $this->disease_detail, true);
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
     * @return PatientMR the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getByIdAndCreatorId($id, $creatorId, $attributes = '*', $with = null) {
        return $this->getByAttributes(array('id' => $id, 'creator_id' => $creatorId), $with);
    }

    
    public function getByPatientId($patientId, $attributes = null, $with = null) {
        return $this->getByAttributes(array('patient_id' => $patientId), $with);
    }

    /*     * ****** Accessors ******* */

    public function getMRFiles() {
        return $this->pmrFiles;
    }

    public function getPatient() {
        return $this->pmrPatient;
    }

    public function getPatientId() {
        return $this->patient_id;
    }

    public function getCreatorId() {
        return $this->creator_id;
    }

    public function getDiseaseName() {
        return $this->disease_name;
    }

    public function getDiseaseDetail($ntext = true) {
        return $this->getTextAttribute($this->disease_detail, $ntext);
    }

    public function getRemark($ntext = true) {
        return $this->getTextAttribute($this->remark, $ntext);
    }

}
