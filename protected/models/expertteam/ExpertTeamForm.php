
<?php

class ExpertTeamForm extends EFormModel {

    public $name;
    public $leader_id;
    public $leader_name;
    public $hospital_id;
    public $hospital_name;
    public $hp_dept_id;
    public $hp_dept_name;
    public $city_id;
    public $dis_tags;
    public $description;
    public $app_image_url;

    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name, leader_id, dis_tags, description, date_created', 'required'),
            array('leader_id, hospital_id, hp_dept_id, city_id', 'numerical', 'integerOnly' => true),
            array('name, leader_name', 'length', 'max' => 50),
            array('dis_tags', 'length', 'max' => 1000),
            array('description', 'length', 'max' => 500),
            array('app_image_url', 'length', 'max' => 200),
            array('hospital_name, hp_dept_name, date_updated, date_deleted', 'safe'),
        );
    }

    public function attributeLabels() {
        return array(
            'dis_tags' => '擅长手术',
            'description' => '团队描述',
        );
    }

    public function initModel(Doctor $model = null) {
        if (isset($model)) {
            $this->name = $model->getName() . '专家团队';
            $this->leader_id = $model->getId();
            $this->leader_name = $model->getName();
            $this->hospital_id = $model->getHospitalId();
            $this->hospital_name = $model->getHospitalName();
            $this->hp_dept_id = $model->getHpDeptId();
            $this->hp_dept_name = $model->getHpDeptName();
            $this->city_id = $model->getCityId();
            $this->app_image_url = $model->getAbsUrlAvatar();
        }
    }

    public function initExpertTeam(ExpertTeam $model = null) {
        if (isset($model)) {
            $this->dis_tags = $model->getDisTags();
            $this->description = $model->getDescription();
        }
    }

}
