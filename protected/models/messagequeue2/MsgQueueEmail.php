<?php

/**
 * This is the model class for table "msg_queue_email".
 *
 * The followings are the available columns in table 'msg_queue_email':
 * @property integer $id
 * @property string $template_code
 * @property string $detail
 * @property string $from_name
 * @property string $from_email
 * @property string $to_email
 * @property string $subject
 * @property string $body
 * @property integer $is_sent
 * @property integer $attempts
 * @property string $last_attempt
 * @property integer $max_attempts
 * @property string $remark
 * @property string $date_read
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class MsgQueueEmail extends EActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'msg_queue_email';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('from_name, from_email, to_email', 'required'),
			array('is_sent, attempts, max_attempts', 'numerical', 'integerOnly'=>true),
			array('template_code, from_name', 'length', 'max'=>50),
			array('detail, from_email, to_email, subject', 'length', 'max'=>100),
			array('body', 'length', 'max'=>2000),
			array('remark', 'length', 'max'=>200),
			array('last_attempt, date_read, date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, template_code, detail, from_name, from_email, to_email, subject, body, is_sent, attempts, last_attempt, max_attempts, remark, date_read, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'template_code' => 'Template Code',
			'detail' => 'Detail',
			'from_name' => 'From Name',
			'from_email' => 'From Email',
			'to_email' => 'To Email',
			'subject' => 'Subject',
			'body' => 'Body',
			'is_sent' => 'Is Sent',
			'attempts' => 'Attempts',
			'last_attempt' => 'Last Attempt',
			'max_attempts' => 'Max Attempts',
			'remark' => 'Remark',
			'date_read' => 'Date Read',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('template_code',$this->template_code,true);
		$criteria->compare('detail',$this->detail,true);
		$criteria->compare('from_name',$this->from_name,true);
		$criteria->compare('from_email',$this->from_email,true);
		$criteria->compare('to_email',$this->to_email,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('body',$this->body,true);
		$criteria->compare('is_sent',$this->is_sent);
		$criteria->compare('attempts',$this->attempts);
		$criteria->compare('last_attempt',$this->last_attempt,true);
		$criteria->compare('max_attempts',$this->max_attempts);
		$criteria->compare('remark',$this->remark,true);
		$criteria->compare('date_read',$this->date_read,true);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('date_updated',$this->date_updated,true);
		$criteria->compare('date_deleted',$this->date_deleted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return MsgQueueEmail the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /*     * ****** Query Methods ******* */

    public function getNeedSendMail($limit=100) {
        $criteria = new CDbCriteria;
        $criteria->select = 't.*';
        $criteria->addCondition("t.is_sent=0");
        $criteria->addCondition("t.max_attempts>t.attempts");
        $criteria->limit = $limit;

        return $this->findAll($criteria);
    }

}
