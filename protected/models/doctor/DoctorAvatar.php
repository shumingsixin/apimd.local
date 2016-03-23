<?php

/**
 * This is the model class for table "doctor_avatar".
 *
 * The followings are the available columns in table 'doctor_avatar':
 * @property integer $id 
 * @property string $uid
 * @property integer $doctor_id
 * @property string $file_ext
 * @property string $mime_type
 * @property string $file_name
 * @property string $file_url
 * @property integer $file_size
 * @property string $thumbnail_name
 * @property string $thumbnail_url
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property Doctor $daDoctor
 */
class DoctorAvatar extends EFileModel {
    
    public $file_upload_field = 'DoctorAvatar'; // $_FILE['upload'].    
    
    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'doctor_avatar';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('uid, doctor_id, file_ext, file_name, file_url', 'required'),
            array('doctor_id, file_size, display_order', 'numerical', 'integerOnly' => true),
            array('uid', 'length', 'is' => 32),
            array('file_ext', 'length', 'max' => 10),
            array('mime_type', 'length', 'max' => 20),
            array('file_name, thumbnail_name', 'length', 'max' => 40),
            array('file_url, thumbnail_url', 'length', 'max' => 255),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, uid, doctor_id, file_ext, mime_type, file_name, file_url, file_size, thumbnail_name, thumbnail_url, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'daDoctor' => array(self::BELONGS_TO, 'Doctor', 'doctor_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'uid' => 'Uid',
            'doctor_id' => 'Doctor',
            'file_ext' => 'File Ext',
            'mime_type' => 'Mime Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
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

        $criteria->compare('id', $this->id);
        $criteria->compare('uid', $this->uid, true);
        $criteria->compare('doctor_id', $this->doctor_id);
        $criteria->compare('file_ext', $this->file_ext, true);
        $criteria->compare('mime_type', $this->mime_type, true);
        $criteria->compare('file_name', $this->file_name, true);
        $criteria->compare('file_url', $this->file_url, true);
        $criteria->compare('file_size', $this->file_size);
        $criteria->compare('thumbnail_name', $this->thumbnail_name, true);
        $criteria->compare('thumbnail_url', $this->thumbnail_url, true);
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
     * @return DoctorAvatar the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function initModel($doctorId, $file) {
        $this->setDoctorId($doctorId);
        $this->setFileAttributes($file);
    }

    public function saveModel() {
        try {
            $fileSysDir = $this->getFileSystemUploadPath();

            //Thumbnail.
            $thumbImage = Yii::app()->image->load($this->file->getTempName());
            // $image->resize(400, 100)->rotate(-45)->quality(75)->sharpen(20);
            $thumbImage->resize($this->thumbnail_width, $this->thumbnail_height);
            if ($thumbImage->save($fileSysDir . '/' . $this->getThumbnailName()) === false) {
                throw new CException('Error saving file thumbnail.');
            }
            if ($this->file->saveAs($fileSysDir . '/' . $this->getFileName()) === false) {
                throw new CException('Error saving file.');
            }

            return $this->save();
        } catch (CException $e) {
            $this->addError('file', $e->getMessage());
            return false;
        }
    }

    //Overwrites parent::getFileUploadRootPath().
    public function getFileUploadRootPath() {
        return Yii::app()->params['doctorFilePath'];
    }

    public function getFileSystemUploadPath($folderName = null) {
        return parent::getFileSystemUploadPath($folderName);
    }

    public function getFileUploadPath($folderName = null) {
        return parent::getFileUploadPath($folderName);
    }

    public function deleteModel($absolute = true) {
        return parent::deleteModel($absolute);
    }

    /*     * ****** Accessors ****** */

    public function getDoctor() {
        return $this->doctor;
    }

    public function getDoctorId() {
        return $this->doctor_id;
    }

    public function setDoctorId($v) {
        $this->doctor_id = $v;
    }

}
