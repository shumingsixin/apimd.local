<?php

/**
 * This is the model class for table "admin_user".
 *
 * The followings are the available columns in table 'admin_user':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $password_raw
 * @property string $password_salt
 * @property string $role
 * @property string $fullname
 * @property string $mobile
 * @property string $email
 * @property string $wechat
 * @property string $qq
 * @property integer $state_id
 * @property string $state_name
 * @property integer $city_id
 * @property string $city_name
 * @property integer $is_active
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AdminUser extends EActiveRecord {

    public $errorMsg;

    const ROLE_CS = 1;
    const ROLE_BD = 2;
    const ROLE_ACCOUNTING = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'admin_user';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('username, password, date_created', 'required'),
            array('state_id, city_id, is_active', 'numerical', 'integerOnly' => true),
            array('username, password_raw, fullname, email, wechat, qq', 'length', 'max' => 50),
            array('password', 'length', 'max' => 64),
            array('password_salt', 'length', 'max' => 40),
            array('role', 'length', 'max' => 20),
            array('mobile', 'length', 'max' => 11),
            array('state_name, city_name', 'length', 'max' => 10),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, username, password, password_raw, password_salt, role, fullname, mobile, email, wechat, qq, state_id, state_name, city_id, city_name, is_active, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
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
            'username' => 'Username',
            'password' => 'Password',
            'password_raw' => 'Password Raw',
            'password_salt' => 'Password Salt',
            'role' => 'Role',
            'fullname' => 'Fullname',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'wechat' => 'Wechat',
            'qq' => 'Qq',
            'state_id' => 'State',
            'state_name' => 'State Name',
            'city_id' => 'City',
            'city_name' => 'City Name',
            'is_active' => 'Is Active',
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
        $criteria->compare('username', $this->username, true);
        $criteria->compare('password', $this->password, true);
        $criteria->compare('password_raw', $this->password_raw, true);
        $criteria->compare('password_salt', $this->password_salt, true);
        $criteria->compare('role', $this->role, true);
        $criteria->compare('fullname', $this->fullname, true);
        $criteria->compare('mobile', $this->mobile, true);
        $criteria->compare('email', $this->email, true);
        $criteria->compare('wechat', $this->wechat, true);
        $criteria->compare('qq', $this->qq, true);
        $criteria->compare('state_id', $this->state_id);
        $criteria->compare('state_name', $this->state_name, true);
        $criteria->compare('city_id', $this->city_id);
        $criteria->compare('city_name', $this->city_name, true);
        $criteria->compare('is_active', $this->is_active);
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
     * @return AdminUser the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getUserByUsername($username) {
        return $this->getByAttributes(array('username' => $username));
    }

}
