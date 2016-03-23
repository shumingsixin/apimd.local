<?php

/**
 * This is the model class for table "admin_user_region_join".
 *
 * The followings are the available columns in table 'admin_user_region_join':
 * @property integer $id
 * @property integer $admin_user_role
 * @property integer $booking_type
 * @property integer $admin_user_id
 * @property string $state_id
 * @property string $city_id
 * @property integer $default
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property AdminUser $adminUser
 */
class AdminUserRegionJoin extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'admin_user_region_join';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array(' date_created', 'required'),
            array('admin_user_role, booking_type, admin_user_id, state_id, city_id, default', 'numerical', 'integerOnly' => true),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, admin_user_role, booking_type, admin_user_id, state_id, city_id, default, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'adminUser' => array(self::BELONGS_TO, 'AdminUser', 'admin_user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'admin_user_role' => 'Admin User Role',
            'booking_type' => 'booking=1,patientbooking=2',
            'admin_user_id' => 'Admin User',
            'state_id' => 'State',
            'city_id' => 'City',
            'default' => '1=yes 0=no',
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
        $criteria->compare('admin_user_role', $this->admin_user_role);
        $criteria->compare('booking_type', $this->booking_type);
        $criteria->compare('admin_user_id', $this->admin_user_id);
        $criteria->compare('state_id', $this->state_id, true);
        $criteria->compare('city_id', $this->city_id, true);
        $criteria->compare('default', $this->default);
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
     * @return AdminUserRegionJoin the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getByStateIdAndBookingTypeAndRole($stateId, $bookingType, $role) {
        return $this->getByAttributes(array('state_id' => $stateId, 'booking_type' => $bookingType, 'admin_user_role' => $role));
    }

    public function getByCityIdAndBookingTypeAndRole($cityId, $bookingType, $role) {
        return $this->getByAttributes(array('city_id' => $cityId, 'booking_type' => $bookingType, 'admin_user_role' => $role));
    }

    public function getDefaultUser($bookingType, $role) {
        return $this->getByAttributes(array('booking_type' => $bookingType, 'admin_user_role' => $role, 'default' => 1));
    }

}
