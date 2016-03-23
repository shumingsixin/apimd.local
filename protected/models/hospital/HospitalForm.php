<?php

class HospitalForm extends EFormModel {

    public $id;
    public $name;
    public $short_name;
    public $type;
    public $class;
    public $search_keywords;
    public $thumbnail_url;
    public $image_url;
    public $address;
    public $phone;
    public $description;
    public $website;
    public $disease_category;
    public $country_id;
    public $state_id;
    public $city_id;
    public $options_class;
    public $options_type;
    public $options_country;
    public $options_state;
    public $options_city;

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('name', 'required'),
            array('id, class, type, country_id, state_id, city_id', 'numerical', 'integerOnly' => true),
            array('name, search_keywords, thumbnail_url, image_url, address, website', 'length', 'max' => 100),
            array('short_name, phone', 'length', 'max' => 45),
            array('description', 'length', 'max' => 500),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array_merge(Hospital::model()->attributeLabels(), array(
        ));
    }

    public function beforeValidate() {
        return parent::beforeValidate();
    }

    public function initModel(Hospital $hospital = null) {
        if (isset($hospital)) {

            $attributes = $hospital->attributes;

            unset($attributes['date_created']);
            unset($attributes['date_updated']);
            unset($attributes['date_deleted']);

            $this->attributes = $attributes;

            $this->scenario = $hospital->scenario;
        } else {
            $this->scenario = 'new';
        }

        $this->loadOptions();
    }

    public function loadOptions() {
        $this->loadOptionsClass();
        $this->loadOptionsType();
        $this->loadOptionsCountry();
        $this->loadOptionsState();
        $this->loadOptionsCity();
    }

    public function loadOptionsClass() {
        $this->options_class = Hospital::model()->getOptionsClass();
    }

    public function loadOptionsType() {
        $this->options_type = Hospital::model()->getOptionsType();
    }

    public function loadOptionsCountry() {
        $this->options_country = CHtml::listData(RegionCountry::model()->getAll(), 'id', 'name_cn');
    }

    public function loadOptionsState() {
        if ($this->country_id === null) {
            $this->options_state = array();
        } else {
            $this->options_state = CHtml::listData(RegionState::model()->getAllByCountryId($this->country_id), 'id', 'name_cn');
        }
    }

    public function loadOptionsCity() {
        if ($this->state_id === null) {
            $this->options_city = array();
        } else {
            $this->options_city = CHtml::listData(RegionCity::model()->getAllByStateId($this->state_id), 'id', 'name_cn');
        }
    }

    public function getOptionsClass() {
        return $this->options_class;
    }

    public function getOptionsType() {
        return $this->options_type;
    }

    public function getOptionsCountry() {
        return $this->options_country;
    }

    public function getOptionsState() {
        return $this->options_state;
    }

    public function getOptionsCity() {
        return $this->options_city;
    }

}