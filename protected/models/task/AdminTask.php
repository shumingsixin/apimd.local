<?php

/**
 * This is the model class for table "admin_task".
 *
 * The followings are the available columns in table 'admin_task':
 * @property integer $id
 * @property string $subject
 * @property string $content
 * @property string $url
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property AdminTaskJoin[] $adminTaskJoins
 */
class AdminTask extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'admin_task';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('content, url, date_created', 'required'),
            array('subject, url', 'length', 'max' => 100),
            array('content', 'length', 'max' => 500),
            array('date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, subject, content, url, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'adminTaskJoins' => array(self::HAS_MANY, 'AdminTaskJoin', 'admin_task_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'subject' => '标题',
            'content' => '内容',
            'url' => '操作链接',
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
        $criteria->compare('subject', $this->subject, true);
        $criteria->compare('content', $this->content, true);
        $criteria->compare('url', $this->url, true);
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
     * @return AdminTask the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
