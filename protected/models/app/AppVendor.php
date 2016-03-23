<?php

/**
 * This is the model class for table "app_vendor".
 *
 * The followings are the available columns in table 'app_vendor':
 * @property integer $id
 * @property string $app_id
 * @property string $vendor_name
 * @property string $app_secret
 * @property string $encrypt_method
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class AppVendor extends EActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'app_vendor';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('app_id, vendor_name, app_secret, date_created', 'required'),
			array('app_id', 'length', 'max'=>16),
			array('vendor_name', 'length', 'max'=>100),
			array('app_secret', 'length', 'max'=>32),
			array('encrypt_method', 'length', 'max'=>10),
			array('date_updated, date_deleted', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, app_id, vendor_name, app_secret, encrypt_method, date_created, date_updated, date_deleted', 'safe', 'on'=>'search'),
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
			'app_id' => 'App',
			'vendor_name' => 'Vendor Name',
			'app_secret' => 'App Secret',
			'encrypt_method' => 'Encrypt Method',
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
		$criteria->compare('app_id',$this->app_id,true);
		$criteria->compare('vendor_name',$this->vendor_name,true);
		$criteria->compare('app_secret',$this->app_secret,true);
		$criteria->compare('encrypt_method',$this->encrypt_method,true);
		$criteria->compare('date_created',$this->date_created,true);
		$criteria->compare('date_updated',$this->date_updated,true);
		$criteria->compare('date_deleted',$this->date_deleted,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getByAppId($app_id){
        $criteria = new CDbCriteria;
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('app_id', $app_id);
        return $this->find($criteria);
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return AppVendor the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
