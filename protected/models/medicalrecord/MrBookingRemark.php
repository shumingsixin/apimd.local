<?php

/**
 * This is the model class for table "mr_booking_remark".
 *
 * The followings are the available columns in table 'mr_booking_remark':
 * @property integer $id
 * @property integer $booking_id
 * @property string $remark_1
 * @property string $remark_2
 * @property string $remark_3
 * @property string $remark_4
 * @property string $remark_5
 * @property string $remark_6
 * @property string $remark_7
 * @property string $remark_8
 * @property string $remark_9
 * @property string $remark_10
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property MedicalRecordBooking $booking
 */
class MrBookingRemark extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'mr_booking_remark';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('booking_id', 'required'),
            array('booking_id', 'numerical', 'integerOnly' => true),
            array('remark_1, remark_2, remark_3, remark_4, remark_5, remark_6, remark_7, remark_8, remark_9, remark_10', 'length', 'max' => 200),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, booking_id, remark_1, remark_2, remark_3, remark_4, remark_5, remark_6, remark_7, remark_8, remark_9, remark_10, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'booking' => array(self::BELONGS_TO, 'MedicalRecordBooking', 'booking_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'booking_id' => 'Booking',
            'remark_1' => 'Remark 1',
            'remark_2' => 'Remark 2',
            'remark_3' => 'Remark 3',
            'remark_4' => 'Remark 4',
            'remark_5' => 'Remark 5',
            'remark_6' => 'Remark 6',
            'remark_7' => 'Remark 7',
            'remark_8' => 'Remark 8',
            'remark_9' => 'Remark 9',
            'remark_10' => 'Remark 10',
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
        $criteria->compare('booking_id', $this->booking_id);
        $criteria->compare('remark_1', $this->remark_1, true);
        $criteria->compare('remark_2', $this->remark_2, true);
        $criteria->compare('remark_3', $this->remark_3, true);
        $criteria->compare('remark_4', $this->remark_4, true);
        $criteria->compare('remark_5', $this->remark_5, true);
        $criteria->compare('remark_6', $this->remark_6, true);
        $criteria->compare('remark_7', $this->remark_7, true);
        $criteria->compare('remark_8', $this->remark_8, true);
        $criteria->compare('remark_9', $this->remark_9, true);
        $criteria->compare('remark_10', $this->remark_10, true);
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
     * @return MrBookingRemark the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Query Methods ******* */

    public function getByBookingId($bookingId) {
        return $this->getByAttributes(array('booking_id' => $bookingId));
    }

    /*     * ****** Accessors ******* */

    public function getRemarks() {
        $output = array();
        $attributes = $this->attributes;
        foreach ($attributes as $attr => $value) {
            if (strpos($attr, 'remark') === 0) {
                $output[$attr] = $value;
            }
        }
        return $output;
    }

    public function getRemarkByField($field) {
        return $this->getTextAttribute($this->{$field});
    }

}
