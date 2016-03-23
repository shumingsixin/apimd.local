<?php

/**
 * This is the model class for table "booking_patient".
 *
 * The followings are the available columns in table 'booking_patient':
 * @property integer $id
 * @property integer $booking_id
 * @property string $name
 * @property string $prc_ic
 * @property integer $gender
 * @property integer $age
 * @property integer $born_year
 * @property integer $from_state
 * @property integer $from_city
 * @property string $medical_condition
 * @property string $surgery_history
 * @property string $drug_history
 * @property string $disease_history
 * @property string $remark
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class BookingPatient extends EActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'booking_patient';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('booking_id, name, gender, age, born_year', 'required'),
			array('booking_id, gender, age, born_year, from_state, from_city', 'numerical', 'integerOnly'=>true),
			array('name', 'length', 'max'=>45),
			array('prc_ic', 'length', 'max'=>18),
			array('medical_condition, surgery_history, drug_history, disease_history', 'length', 'max'=>500),
			array('remark', 'length', 'max'=>200),
			array('date_created, date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, booking_id, name, prc_ic, gender, age, born_year, from_state, from_city, medical_condition, surgery_history, drug_history, disease_history, remark, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
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
			'booking_id' => 'Booking',
			'name' => 'Name',
			'prc_ic' => 'Prc Ic',
			'gender' => 'Gender',
			'age' => 'Age',
			'born_year' => 'Born Year',
			'from_state' => 'From State',
			'from_city' => 'From City',
			'medical_condition' => 'Medical Condition',
			'surgery_history' => 'Surgery History',
			'drug_history' => 'Drug History',
			'disease_history' => 'Disease History',
			'remark' => 'Remark',
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
		$criteria->compare('booking_id',$this->booking_id);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('prc_ic',$this->prc_ic,true);
		$criteria->compare('gender',$this->gender);
		$criteria->compare('age',$this->age);
		$criteria->compare('born_year',$this->born_year);
		$criteria->compare('from_state',$this->from_state);
		$criteria->compare('from_city',$this->from_city);
		$criteria->compare('medical_condition',$this->medical_condition,true);
		$criteria->compare('surgery_history',$this->surgery_history,true);
		$criteria->compare('drug_history',$this->drug_history,true);
		$criteria->compare('disease_history',$this->disease_history,true);
		$criteria->compare('remark',$this->remark,true);
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
	 * @return BookingPatient the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
