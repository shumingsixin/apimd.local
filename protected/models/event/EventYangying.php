<?php

/**
 * This is the model class for table "event_yangying".
 *
 * The followings are the available columns in table 'event_yangying':
 * @property integer $id
 * @property string $author
 * @property string $comment
 * @property integer $visible
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 */
class EventYangying extends EActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'event_yangying';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('comment', 'required', 'message' => '请输入{attribute}'),
            array('visible', 'numerical', 'integerOnly' => true),
            array('author', 'length', 'max' => 45),
            array('comment', 'length', 'max' => 200),
            array('date_created, date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, author, comment, visible, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
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
            'author' => Yii::t('event', '昵称'),
            'comment' => Yii::t('event', '祝福语'),
            'visible' => 'Visible',
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
        $criteria->compare('author', $this->author, true);
        $criteria->compare('comment', $this->comment, true);
        $criteria->compare('visible', $this->visible);
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
     * @return EventYangying the static model class
     */
    public static function model($className=__CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Accessors ******* */

    public function getAuthor() {
        return $this->author;
    }

    public function getComment() {
        return $this->getTextAttribute($this->comment);
    }

    public function getDateCreated($format='m月d日 h:i') {
        return $this->getDatetimeAttribute($this->date_created, $format);
    }

}
