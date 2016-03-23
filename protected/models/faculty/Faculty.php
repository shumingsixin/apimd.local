<?php

/**
 * This is the model class for table "faculty".
 *
 * The followings are the available columns in table 'faculty':
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $disease_list
 * @property string $description
 * @property string $is_active
 * @property integer $display_order
 * @property string $url_icon;
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class Faculty extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'faculty';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('code, name', 'required'),
            array('code, name', 'unique'),
            array('is_active, display_order', 'numerical', 'integerOnly' => true),
            array('code', 'length', 'max' => 20),
            array('name', 'length', 'max' => 45),
            array('disease_list', 'length', 'max' => 200),
            array('description, url_icon', 'length', 'max' => 255),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, code, name, disease_list, description, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            //'facultyHospitals' => array(self::MANY_MANY, 'Hospital', 'faculty_hospital_join(faculty_id, hospital_id)', 'order' => 'facultyHospitals_facultyHospitals.display_order ASC', 'condition' => 'facultyHospitals_facultyHospitals.visible=1'),
            'facultyHospitals' => array(self::MANY_MANY, 'Hospital', 'faculty_hospital_join(faculty_id, hospital_id)', 'order' => 'facultyHospitals_facultyHospitals.display_order ASC'),
            'facultyDoctors' => array(self::MANY_MANY, 'Doctor', 'faculty_doctor_join(faculty_id, doctor_id)', 'order' => 'facultyDoctors_facultyDoctors.display_order ASC'),
            'facultyExpertTeams' => array(self::HAS_MANY, 'ExpertTeam', 'faculty_id'),
            'facultyHospitalJoins' => array(self::HAS_MANY, 'FacultyHospitalJoin', 'faculty_id', 'order' => 'facultyHospitalJoins.display_order ASC'),
            'facultyDoctorJoins' => array(self::HAS_MANY, 'FacultyDoctorJoin', 'faculty_id', 'order' => 'facultyDoctorJoins.display_order ASC'),
            'facultyHospitalJoinsVisible' => array(self::HAS_MANY, 'FacultyHospitalJoin', 'faculty_id', 'condition' => 'facultyHospitalJoinsVisible.visible=1', 'order' => 'facultyHospitalJoinsVisible.display_order ASC'),
            'facultyDoctorJoinsVisible' => array(self::HAS_MANY, 'FacultyDoctorJoin', 'faculty_id', 'condition' => 'facultyDoctorJoinsVisible.visible=1', 'order' => 'facultyDoctorJoinsVisible.display_order ASC'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
            'disease_list' => '疾病包括',
            'description' => '描述',
            'is_active' => '在主页显示（是/否）',
            'display_order' => '显示顺序',
            'url_icon' => 'Url Icon',
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
        $criteria->compare('code', $this->code, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('disease_list', $this->disease_list, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('is_active', $this->is_active, true);
        $criteria->compare('display_order', $this->display_order);
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
     * @return Faculty the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Query Methods ******* */

    public function getByCode($code, $with = null) {
        return $this->getByAttributes(array('code' => $code), $with);
    }

    public function getActiveRecordByName($name, $with = null) {
        return $this->getByAttributes(array('is_active' => 1, 'name' => $name), $with);
    }

    public function getVisibleHospitals() {
        return $this->facultyHospitals(array('condition' => 'facultyHospitals_facultyHospitals.visible=1', 'order' => 'facultyHospitals_facultyHospitals.display_order ASC'));
    }

    public function getVisibleDoctors() {
        return $this->facultyDoctors(array('condition' => 'facultyDoctors_facultyDoctors.visible=1', 'order' => 'facultyDoctors_facultyDoctors.display_order ASC'));
    }

    public function getByIdWithDoctors($id, $visible = true) {
        if ($visible) {
            $model = $this->getByAttributes(array('id' => $id, 'is_active' => 1), array('facultyDoctorJoinsVisible'));
        } else {
            $model = $this->getByAttributes(array('id' => $id, 'is_active' => 1), array('facultyDoctorJoins'));
        }
        return $model;
    }

    public function getByIdWithHospitalsAndDoctors($id, $visible = true) {
        if ($visible) {
            $model = $this->getByAttributes(array('id' => $id, 'is_active' => 1), array('facultyHospitalJoinsVisible', 'facultyDoctorJoinsVisible'));
        } else {
            $model = $this->getByAttributes(array('id' => $id, 'is_active' => 1), array('facultyHospitalJoins', 'facultyDoctorJoins'));
        }
        return $model;
    }

    public function getAllActiveRecords() {
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('is_active', 1);
        $criteria->order = 't.display_order ASC';
        $criteria->select = "t.id, t.code, t.name, t.disease_list, t.url_icon";
        return $this->findAll($criteria);
    }

    /*     * ****** Accessors ******* */

    public function getHospitals() {
        return $this->facultyHospitals;
    }

    public function getDoctors() {
        return $this->facultyDoctors;
    }

    public function getHospitalJoins() {
        return $this->facultyHospitalJoins;
    }

    public function getDoctorJoins() {
        return $this->facultyDoctorJoins;
    }

    public function getHospitalJoinsVisible() {
        return $this->facultyHospitalJoinsVisible;
    }

    public function getDoctorJoinsVisible() {
        return $this->facultyDoctorJoinsVisible;
    }

    public function getName() {
        return $this->name;
    }

    public function getCode() {
        return $this->code;
    }

    public function getDiseaseList() {
        return explode(',', $this->disease_list);
    }

    public function getDescription($ntext = false) {
        return $this->getTextAttribute($this->description, $ntext);
    }

    public function isActive() {
        if ($this->getBooleanAttribute($this->is_active)) {
            return '是';
        } else {
            return '否';
        }
    }

    public function getAbsUrlIcon() {
        return $this->url_icon;
    }

}
