<?php

/**
 * This is the model class for table "faculty_doctor_join".
 *
 * The followings are the available columns in table 'faculty_doctor_join':
 * @property integer $doctor_id
 * @property integer $faculty_id
 * @property integer $visible
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class FacultyDoctorJoin extends EActiveRecord {

    public $options_faculty;
    public $options_faculty_exist;
    public $options_doctor;
    public $options_doctor_exist;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'faculty_doctor_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('doctor_id, faculty_id', 'required'),
            array('doctor_id, faculty_id, visible, display_order', 'numerical', 'integerOnly' => true),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('doctor_id, faculty_id, visible ,display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'fdjFaculty' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
            'fdjDoctor' => array(self::BELONGS_TO, 'Doctor', 'doctor_id'),
            'faculty' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
            'doctor' => array(self::BELONGS_TO, 'Doctor', 'doctor_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'doctor_id' => Yii::t('hospital', '医生'),
            'faculty_id' => Yii::t('hospital', '科室'),
            'visible' => Yii::t('hospital', '展示（是/否）'),
            'display_order' => 'Display Order',
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

        $criteria->compare('doctor_id', $this->doctor_id);
        $criteria->compare('faculty_id', $this->faculty_id);
        $criteria->compare('visible', $this->visible);
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
     * @return HospitalFacultyJoin the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function loadOptionsFaculty($diff = false) {
        if (is_null($this->options_faculty)) {
            $listModel = Faculty::model()->getAll();
            $this->options_faculty = CHtml::listData($listModel, 'id', 'name');
        }

        if ($diff) {
            $this->options_faculty = array_diff($this->options_faculty, $this->options_faculty_exist);
        }
        return $this->options_faculty;
    }

    public function loadOptionsDoctor($diff = false) {
        if (is_null($this->options_doctor)) {
            $listModel = Doctor::model()->getAll(null, array('order' => 't.name ASC'));
            $this->options_doctor = CHtml::listData($listModel, 'id', 'fullname');
        }
        if ($diff) {
            $this->options_doctor = array_diff($this->options_doctor, $this->options_doctor_exist);
        }
        return $this->options_doctor;
    }

    /**
     *
     * @param array $facultyList array of Faculty model.
     */
    public function setExistingFacultyList($listModel) {
        if (is_array($listModel) && count($listModel) > 0) {
            $this->options_faculty_exist = CHtml::listData($listModel, 'id', 'name');
        } else {
            $this->options_faculty_exist = array();
        }
    }

    public function setExistingDoctorList($listModel) {
        if (is_array($listModel) && count($listModel) > 0) {
            $this->options_doctor_exist = CHtml::listData($listModel, 'id', 'fullname');
        } else {
            $this->options_doctor_exist = array();
        }
    }

    /*     * ****** Query Methods ******* */

    public function getByFacultyIdAndDoctorId($fid, $did) {
        return $this->getByAttributes(array('faculty_id' => $fid, 'doctor_id' => $did));
    }

    /*     * ****** Accessors ******* */

    public function getFaculty() {
        return $this->fdjFaculty;
    }

    public function getDoctor() {
        return $this->fdjDoctor;
    }

    public function getFacultyId() {
        return $this->faculty_id;
    }

    public function getDoctorId() {
        return $this->doctor_id;
    }

    public function isVisible() {
        if ($this->getBooleanAttribute($this->visible)) {
            return '是';
        } else {
            return '否';
        }
    }

}
