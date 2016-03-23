<?php

/**
 * This is the model class for table "medical_record_file".
 *
 * The followings are the available columns in table 'medical_record_file':
 * @property integer $id
 * @property string $uid
 * @property integer $mr_id
 * @property integer $user_id
 * @property integer $report_type
 * @property string $file_ext
 * @property string $mime_type
 * @property string $file_name
 * @property string $file_url
 * @property integer $file_size
 * @property string $thumbnail_name
 * @property string $thumbnail_url
 * @property string $description
 * @property string $date_taken
 * @property integer $display_order
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class MedicalRecordFile extends EFileModel {

    const REPORT_LAB = 1; // 化验报告：验血，验尿 etc.
    const REPORT_IMAGE = 2;   // x-ray, ct etc.
    const REPORT_WRITTEN = 3; // 出院小结 etc.

    public $file_upload_field = 'mr_files'; // $_FILE['mr_files'].
    public $file;   // CUploadedFile.
    // protected $standard_image_width = 1024;
    //protected $standard_image_height = 1024;
    protected $thumbnail_width = 160;
    protected $thumbnail_height = 160;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'medical_record_file';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('uid, mr_id, user_id, report_type, file_name, file_url', 'required'),
            array('mr_id, user_id, report_type, file_size, display_order', 'numerical', 'integerOnly' => true),
            array('uid', 'length', 'is' => 32),
            array('file_name, thumbnail_name', 'length', 'max' => 40),
            array('file_ext, mime_type', 'length', 'max' => 10),
            array('file_url, thumbnail_url', 'length', 'max' => 255),
            array('description', 'length', 'max' => 100),
            array('date_taken', 'type', 'dateFormat' => 'yyyy-mm-dd', 'type' => 'date'),
            array('date_taken, date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, mr_id, user_id, uid, report_type, file_ext, file_url, thumbnail_url, description date_taken, display_order, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'mrfMedicalRecord' => array(self::BELONGS_TO, 'MedicalRecord', 'mr_id'),
            'mrfUser' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'mr_id' => 'Medical Record',
            'uid' => 'UID',
            'report_type' => 'Report Type',
            'file_ext' => 'File Extention',
            'mime_type' => 'MIME Type',
            'file_name' => 'File Name',
            'file_url' => 'File Url',
            'file_size' => 'File Size',
            'thumbnail_name' => 'Thumbnail Name',
            'thumbnail_url' => 'Thumbnail Url',
            'description' => 'Description',
            'date_taken' => 'Date Taken',
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
        $criteria->compare('mr_id', $this->mr_id);
        $criteria->compare('user_id', $this->user_id);
        $criteria->compare('report_type', $this->report_type);
        $criteria->compare('file_ext', $this->file_ext, true);
        $criteria->compare('file_name', $this->file_name, true);
        $criteria->compare('file_url', $this->file_url, true);
        $criteria->compare('file_size', $this->file_size, true);
        $criteria->compare('thumbnail_name', $this->thumbnail_name, true);
        $criteria->compare('thumbnail_url', $this->thumbnail_url, true);
        $criteria->compare('description', $this->description, true);
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
     * @return MedicalRecordFile the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function initModel($mrid, $rt,$userid, $file) {        
        $this->file = $file;
        $this->setMrId($mrid);
        $this->setReportType($rt);
        $this->setUserId($userid);
        $this->setFileExtension($file->extensionName);
        $this->setFileSize($file->size);
        $this->setMimeType($file->type); //Since this MIME type is not checked on the server side, do not take this value for granted. Instead, use CFileHelper::getMimeType to determine the exact MIME type.
        $this->createUID();
        $this->setFileName($this->uid . '.' . $this->getFileExtension());
        $this->setThumbnailName($this->uid . 'tn.' . $this->getFileExtension());
        //URL Path.
        $fileUploadDir = $this->getFileUploadPath();
        $this->setFileUrl($fileUploadDir . '/' . $this->getFileName());
        $this->setThumbnailUrl($fileUploadDir . '/' . $this->getThumbnailName());
    }

    public function saveModel() {
        $hasError = false;
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
        return Yii::app()->params['medicalRecordFilePath'];
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

    /*     * ****** Query Methods ******* */

    public function getAllByMrId($mrid) {
        $criteria = new CDbCriteria(array('order' => 't.display_order'));
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('mr_id', $mrid);
        return $this->findAll($criteria);
    }

    public function getAllByMrIdAndReportType($mrid, $reporttype) {
        $criteria = new CDbCriteria(array('order' => 't.display_order'));
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('mr_id', $mrid);
        $criteria->compare('report_type', $reporttype);
        return $this->findAll($criteria);
    }

    /*     * ****** Accessors ****** */

    public function getMedicalRecord() {
        return $this->mrfMedicalRecord;
    }

    public function getMrId() {
        return $this->mr_id;
    }

    public function setMrId($v) {
        $this->mr_id = $v;
    }

    public function getUserId() {
        return $this->user_id;
    }

    public function setUserId($v) {
        $this->user_id = $v;
    }

    public function getReportType() {
        return $this->report_type;
    }

    public function setReportType($v) {
        $this->report_type = $v;
    }

    public function getDescription($ntext = false) {
        return $this->getTextAttribute($this->description, $ntext);
    }

    public function setDescription($v) {
        $this->description = $v;
    }

    public function getDateTaken() {
        return $this->getDateAttribute($this->date_taken, 'Y-m-d');
    }

    public function setDateTaken($v) {
        $this->date_taken = $this->setDateAttribute($v);
    }

}
