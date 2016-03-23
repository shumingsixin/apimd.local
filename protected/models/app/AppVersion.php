<?php

/**
 * This is the model class for table "app_version".
 *
 * The followings are the available columns in table 'app_version':
 * @property integer $id
 * @property string $os
 * @property string $os_version
 * @property string $device
 * @property string $app_version
 * @property string $app_dl_url
 * @property integer $is_force_update
 * @property string $change_log
 * @property string $date_active
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AppVersion extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'app_version';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('app_name, os, os_version, app_version, date_active', 'required'),
            array('is_force_update', 'numerical', 'integerOnly' => true),
            array('os, os_version, device, app_version, app_dl_url', 'length', 'max' => 45),
            array('app_name', 'length', 'max'=>20),
            array('change_log, date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, os, os_version, device, app_version, app_dl_url, is_force_update, change_log, date_active, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
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
            'os' => 'Os',
            'os_version' => 'Os Version',
            'device' => 'Device',
            'app_version' => 'App Version',
            'app_dl_url' => 'App Dl Url',
            'is_force_update' => 'Is Force Update',
            'change_log' => 'Change Log',
            'date_active' => 'Date Active',
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
        $criteria->compare('os', $this->os, true);
        $criteria->compare('os_version', $this->os_version, true);
        $criteria->compare('device', $this->device, true);
        $criteria->compare('app_version', $this->app_version, true);
        $criteria->compare('app_dl_url', $this->app_dl_url, true);
        $criteria->compare('is_force_update', $this->is_force_update);
        $criteria->compare('change_log', $this->change_log, true);
        $criteria->compare('date_active', $this->date_active, true);
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
     * @return AppVersion the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Query Methods ******* */

    public function getLastestVersionByOSAndAppName($os, $app_name) {
        // TODO: add 'date_active'. $now > $date_active.
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.app_name', $app_name);
        $criteria->compare('t.os', $os);
        $criteria->order = 't.app_version DESC';
        $criteria->limit = 1;

        $ret = $this->findAll($criteria);
        $model = array_shift($ret);

        return $model;
    }

    public function getLatestActiveVersionByOS($os) {
        // TODO: add 'date_active'. $now > $date_active.
        $now = new CDbExpression("NOW()");
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.os', $os);
        $criteria->addCondition('t.date_active<=:now');
        $criteria->params[':now'] = $now;
        $criteria->order = 't.app_version DESC';
        $criteria->limit = 1;

        $ret = $this->findAll($criteria);
        $model = array_shift($ret);

        return $model;
    }

    /*     * ****** Accessors ******* */

    public function getOS() {
        return $this->os;
    }

    public function getOSVersion() {
        return $this->os_version;
    }

    public function getDevice() {
        return $this->device;
    }

    public function getAppVersion() {
        return $this->app_version;
    }

    public function getAppDownloadUrl() {
        return $this->app_dl_url;
    }

    public function getIsForceUpdate() {
        if ($this->is_force_update == 1) {
            return "1";
        } else {
            return "0";
        }
    }

    public function getChangeLog() {
        return $this->change_log;
    }

    public function getDateActive() {
        return $this->date_active;
    }

}
