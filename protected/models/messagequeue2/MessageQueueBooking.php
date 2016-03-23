<?php

/**
 * This is the model class for table "message_queue_booking".
 *
 * The followings are the available columns in table 'message_queue_booking':
 * @property integer $id
 * @property string $sender_name
 * @property string $sender_email
 * @property string $to_email
 * @property string $subject
 * @property integer $booking_id
 * @property integer $max_attempts
 * @property integer $attempts
 * @property integer $is_success
 * @property string $last_attempt
 * @property string $date_sent
 * @property string $date_read
 * @property string $job_start_time
 * @property string $job_stop_time
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class MessageQueueBooking extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'message_queue_booking';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('sender_name, sender_email, to_email', 'required'),
            array('booking_id, max_attempts, attempts, is_success', 'numerical', 'integerOnly' => true),
            array('sender_name', 'length', 'max' => 50),
            array('sender_email, to_email, subject', 'length', 'max' => 100),
            array('last_attempt, date_sent, date_read, job_start_time, job_stop_time, date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, sender_name, sender_email, to_email, subject, booking_id, max_attempts, attempts, is_success, last_attempt, date_sent, date_read, job_start_time, job_stop_time, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
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
            'sender_name' => 'Sender Name',
            'sender_email' => 'Sender Email',
            'to_email' => 'To Email',
            'subject' => 'Subject',
            'booking_id' => 'Booking',
            'max_attempts' => 'Max Attempts',
            'attempts' => 'Attempts',
            'is_success' => 'Is Success',
            'last_attempt' => 'Last Attempt',
            'date_sent' => 'Date Sent',
            'date_read' => 'Date Read',
            'job_start_time' => 'Job Start Time',
            'job_stop_time' => 'Job Stop Time',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
            'date_deleted' => 'Date Deleted',
        );
    }
    
      public function getMessage($isSuccess=0,$pageSize=100,$pageIndex=1){
        $with= null;
        $options=array('limit'=>$pageSize, 'offset'=>(($pageIndex-1)*$pageSize));
        return $this->getAllByAttributes(array('t.is_success'=>$isSuccess),$with,$options);
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
        $criteria->compare('sender_name', $this->sender_name, true);
        $criteria->compare('sender_email', $this->sender_email, true);
        $criteria->compare('to_email', $this->to_email, true);
        $criteria->compare('subject', $this->subject, true);
        $criteria->compare('booking_id', $this->booking_id);
        $criteria->compare('max_attempts', $this->max_attempts);
        $criteria->compare('attempts', $this->attempts);
        $criteria->compare('is_success', $this->is_success);
        $criteria->compare('last_attempt', $this->last_attempt, true);
        $criteria->compare('date_sent', $this->date_sent, true);
        $criteria->compare('date_read', $this->date_read, true);
        $criteria->compare('job_start_time', $this->job_start_time, true);
        $criteria->compare('job_stop_time', $this->job_stop_time, true);
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
     * @return MessageQueueBooking the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function getAttempts(){
        return $this->attempts;
    }
}
