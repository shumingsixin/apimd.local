<?php

/**
 * This is the model class for table "expert_team".
 *
 * The followings are the available columns in table 'expert_team':
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property integer $leader_id
 * @property integer $hospital_id
 * @property integer $hp_dept_id
 * @property integer $faculty_id
 * @property string $dis_tags
 * @property string $slogan
 * @property string $description
 * @property string $app_image_url
 * @property string $banner_url
 * @property string $detail_url
 * @property string $date_created
 * @property string $date_updated
 * @property string $date_deleted
 *
 * The followings are the available model relations:
 * @property Doctor $leader
 */
class ExpertTeam extends EActiveRecord {

    public $members;

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'expert_team';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, leader_id, date_created', 'required'),
            array('leader_id, hospital_id, hp_dept_id, faculty_id, city_id', 'numerical', 'integerOnly' => true),
            array('code, name, leader_name, hospital_name, hp_dept_name, slogan', 'length', 'max' => 50),
            array('dis_tags', 'length', 'max' => 1000),
            array('description', 'length', 'max' => 500),
            array('banner_url, detail_url, app_image_url', 'length', 'max' => 200),
            array('hospital_name, hp_dept_name,date_updated, date_deleted', 'safe'),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('id, code, name, leader_id, hospital_id, hp_dept_id, faculty_id, dis_tags, slogan, description, banner_url, detail_url, date_created, date_updated, date_deleted', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'expteamLeader' => array(self::BELONGS_TO, 'Doctor', 'leader_id'),
            'expteamMembers' => array(self::MANY_MANY, 'Doctor', 'expert_team_member_join(team_id, doctor_id)'),
            'expteamFaculty' => array(self::BELONGS_TO, 'Faculty', 'faculty_id'),
            'expteamHospital' => array(self::BELONGS_TO, 'Hospital', 'hospital_id'),
            'expteamHpDept' => array(self::BELONGS_TO, 'HospitalDepartment', 'hp_dept_id'),
            'expteamCity' => array(self::BELONGS_TO, 'RegionCity', 'city_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'id' => 'ID',
            'code' => 'Code',
            'name' => '团队名称',
            'city_id' => 'City_id',
            'leader_id' => 'Leader',
            'hospital_id' => 'Hospital',
            'hp_dept_id' => 'Hp Dept',
            'leader_name' => '队长医生',
            'hospital_name' => '医院',
            'hp_dept_name' => '科室',
            'faculty_id' => 'Faculty',
            'dis_tags' => '擅长手术',
            'slogan' => '团队标语',
            'description' => '团队描述',
            'banner_url' => 'Banner Url',
            'detail_url' => 'Detail Url',
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
        $criteria->compare('code', $this->code, true);
        $criteria->compare('name', $this->name, true);
        $criteria->compare('leader_id', $this->leader_id);
        $criteria->compare('hospital_id', $this->hospital_id);
        $criteria->compare('hp_dept_id', $this->hp_dept_id);
        $criteria->compare('faculty_id', $this->faculty_id);
        $criteria->compare('dis_tags', $this->dis_tags, true);
        $criteria->compare('slogan', $this->slogan, true);
        $criteria->compare('description', $this->description, true);
        $criteria->compare('banner_url', $this->banner_url, true);
        $criteria->compare('detail_url', $this->detail_url, true);
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
     * @return ExpertTeam the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /*     * ****** Query Methods ******* */

    public function getByCode($code, $with = null) {
        if (is_null($code))
            return null;
        else if (is_array($with)) {
            return $this->with($with)->findByAttributes(array('t.code' => $code, 't.date_deleted' => null));
        } else {
            return $this->findByAttributes(array('code' => $code, 'date_deleted' => null));
        }
    }

    public function getByFacultyId($fid, $with = null) {
        if (is_null($fid))
            return null;
        else if (is_array($with)) {
            return $this->with($with)->findByAttributes(array('t.faculty_id' => $fid, 't.date_deleted' => null));
        } else {
            return $this->findByAttributes(array('faculty_id' => $fid, 'date_deleted' => null));
        }
    }

    public function getAllByFacultyId($fid, $with = null, $options = null) {
        return $this->getAllByAttributes(array('t.faculty_id' => $fid), $with, $options);
    }

    public function getAllByCityId($cityId, $with = null, $options = null) {
        return $this->getAllByAttributes(array('t.city_id' => $cityId), $with, $options);
    }

    // @TODO
    /**
     * Loads all ExpertTeam models by Disease.id from db.
     * @param integer $did  Disease.id
     * @param array $with  array('expteamMembers','expteamHospital', 'expteamHospital', 'expteamHpDept)
     * @param array $options=array('order'=>$order,'limit'=>$limit, 'offset'=>$offset)
     * @return array ExpertTeam models.
     */
    public function getAllByDiseaseId($did, $with = null, $options = null) {
        $criteria = new CDbCriteria;
        $criteria->select = 't.*';
        $criteria->distinct = FALSE;
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->join = 'left join disease_expteam_join j on (t.`id`=j.`expteam_id` AND j.date_deleted is NULL)';
        if (isset($with)) {
            $criteria->with = $with;
        }
        $criteria->addCondition("j.disease_id=:disId");
        $criteria->params[":disId"] = $did;
        $criteria->order = "j.`display_order` ASC";
        if (isset($options['limit'])) {
            $criteria->limit = $options['limit'];
        }
        if (isset($options['offset'])) {
            $criteria->offset = $options['offset'];
        }
        return $this->findAll($criteria);
    }

    public function getMembers() {
        return $this->expteamMembers;
    }

    /*     * ****** Accessors ******* */

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getCode() {
        return $this->code;
    }

    public function getSlogan() {
        return $this->slogan;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getAppImageUrl() {
        return $this->app_image_url;
    }

    public function getBannerUrl() {
        return $this->banner_url;
    }

    public function getHospitalName() {
        return $this->hospital_name;
    }

    public function getExpteamLeader() {
        return $this->expteamLeader;
    }

    public function getHospital() {
        return $this->expteamHospital;
    }

    public function getExpteamFaculty() {
        return $this->expteamFaculty;
    }

    public function getHpDept() {
        return $this->expteamHpDept;
    }

    public function getLeaderId() {
        return $this->leader_id;
    }

    public function getHpDeptName() {
        return $this->hp_dept_name;
    }

    public function getFacultyName() {
        return $this->expteamFaculty->name;
    }

    public function getDisTags() {
        return $this->dis_tags;
    }

}
