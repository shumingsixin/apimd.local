<?php

/**
 * This is the model class for table "event_dandao".
 *
 * The followings are the available columns in table 'event_dandao':
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property integer $gender
 * @property string $nric
 * @property string $diagnosis
 * @property string $treatment 
 * @property string $other
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class EventDandao extends EActiveRecord {
    const GENDER_MALE=1;
    const GENDER_FEMALE=2;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'event_dandao';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, mobile, nric, diagnosis, treatment', 'required', 'message' => '请输入{attribute}'),
            array('gender', 'required', 'message' => '请选择{attribute}'),            
            array('gender', 'numerical', 'integerOnly' => true),
            array('name', 'length', 'max' => 45),
            array('mobile', 'numerical', 'integerOnly' => true, 'message' => '请输入正确的11位中国手机号码'),
            array('mobile', 'length', 'is' => 11, 'message' => '请输入正确的11位中国手机号码'),
            array('nric', 'numerical', 'integerOnly' => true, 'message' => '请输入正确的18位中国身份证号码'),
            array('nric', 'length', 'is' => 18, 'message' => '请输入正确的18位中国身份证号码'),
            array('diagnosis, treatment', 'length', 'max' => 200),
            array('other', 'length', 'max' => 100),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, name, mobile, gender, nric, diagnosis, treatment, other, user_ip, user_agent, access_agent, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'name' => '患者姓名',
            'mobile' => '手机号码',
            'gender' => '性别',
            'nric' => '身份证号码',
            'diagnosis' => '疾病诊断',
            'treatment' => '治疗经过',            
            'other' => '其它',
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
        $criteria->compare('name', $this->name, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('gender', $this->gender);
        $criteria->compare('nric', $this->nric, true);
        $criteria->compare('diagnosis', $this->diagnosis, true);
        $criteria->compare('treatment', $this->treatment, true);        
        $criteria->compare('other', $this->other, true);
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
     * @return EventDandao the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    public function getOptionsGender() {
        return array(self::GENDER_MALE => '男', self::GENDER_FEMALE => '女');
    }

    public function getOptionsIsSh() {
        return array(1 => '是', 0 => '否');
    }

    /*     * ****** Accessors ******* */

    public function getGender() {
        $options = $this->getOptionsGender();
        if (isset($options[$this->gender])) {
            return $options[$this->gender];
        } else {
            return null;
        }
    }

}
