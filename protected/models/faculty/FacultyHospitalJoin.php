<?php

/**
 * This is the model class for table "faculty_hospital_join".
 *
 * The followings are the available columns in table 'faculty_hospital_join':
 * @property integer $hospital_id
 * @property integer $faculty_id
 * @property string $description
 * @property integer $visible
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class FacultyHospitalJoin extends EActiveRecord {

    public $options_faculty;
    public $options_faculty_exist;
    public $options_hospital;
    public $options_hospital_exist;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'faculty_hospital_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('hospital_id, faculty_id', 'required'),
            array('hospital_id, faculty_id, visible, display_order', 'numerical', 'integerOnly' => true),
            array('description', 'length', 'max' => 500),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('hospital_id, faculty_id, description, visible, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'fhjFaculty' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
            'fhjHospital' => array(self::BELONGS_TO, 'Hospital', 'hospital_id'),
            'faculty' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
            'hospital' => array(self::BELONGS_TO, 'Hospital', 'hospital_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'hospital_id' => Yii::t('hospital', '医院'),
            'faculty_id' => Yii::t('hospital', '科室'),
            'description' => Yii::t('hospital', '描述'),
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

        $criteria->compare('hospital_id', $this->hospital_id);
        $criteria->compare('faculty_id', $this->faculty_id);
        $criteria->compare('description', $this->description);
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
     * @return FacultyHospitalJoin the static model class
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

    public function loadOptionsHospital($diff = false) {
        if (is_null($this->options_hospital)) {
            $listModel = Hospital::model()->getAll(null, array('order' => 't.name ASC'));
            $this->options_hospital = CHtml::listData($listModel, 'id', 'name');
        }
        if ($diff) {
            $this->options_hospital = array_diff($this->options_hospital, $this->options_hospital_exist);
        }
        return $this->options_hospital;
    }

    /**
     *
     * @param array $list array of Faculty model.
     */
    public function setExistingFacultyList($listModel) {
        if (is_array($listModel) && count($listModel) > 0) {
            $this->options_faculty_exist = CHtml::listData($listModel, 'id', 'name');
        } else {
            $this->options_faculty_exist = array();
        }
    }

    /**
     *
     * @param type $list array Hospital model.
     */
    public function setExistingHospitalList($listModel) {
        if (is_array($listModel) && count($listModel) > 0) {
            $this->options_hospital_exist = CHtml::listData($listModel, 'id', 'name');
        } else {
            $this->options_hospital_exist = array();
        }
    }

    /*     * ****** Query Methods ******* */

    public function getByFacultyIdAndHospitalId($fid, $hid, $with = null) {
        return $this->getByAttributes(array('faculty_id' => $fid, 'hospital_id' => $hid), $with);
    }

    /*     * ****** Accessors ******* */

    public function getFaculty() {
        return $this->fhjFaculty;
    }

    public function getHospital() {
        return $this->fhjHospital;
    }

    public function getFacultyId() {
        return $this->faculty_id;
    }

    public function getHospitalId() {
        return $this->hospital_id;
    }

    public function getDescription($ntext = false) {
        return $this->getTextAttribute($this->description, $ntext);
    }

    public function setDescription($v) {
        $this->description = $v;
    }

    public function isVisible() {
        if ($this->getBooleanAttribute($this->visible)) {
            return '是';
        } else {
            return '否';
        }
    }

}
