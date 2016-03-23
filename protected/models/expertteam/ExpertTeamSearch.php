<?php


class ExpertTeamSearch extends ESearchModel{

    public function __construct($searchInputs, $with = null) {
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new ExpertTeam();
    }

    public function getQueryFields() {
        return array('city', 'disease', 'cate');
    }


    //@修改
    public function addQueryConditions() {
        $this->criteria->addCondition('t.date_deleted is NULL');

        if ($this->hasQueryParams()) {

            // City.
            if (isset($this->queryParams['city'])) {
                $cityId = $this->queryParams['city'];
                $this->criteria->compare('t.city_id', $cityId);
            }
            // Disease.
            if (isset($this->queryParams['disease'])) {
                $diseaseId = $this->queryParams['disease'];
                $this->criteria->join .= 'left join disease_expteam_join dej on (t.`id`=dej.`expteam_id`)';
                $this->criteria->compare("dej.disease_id", $diseaseId);
                $this->criteria->distinct = true;
            }
            // Cate.
            if (isset($this->queryParams['cate'])) {
                $cateId = $this->queryParams['cate'];
                $this->criteria->join = 'left join disease_expteam_join b on t.id=b.expteam_id left join disease c on c.id=b.disease_id left join disease_category d on c.category_id=d.sub_cat_id';
                $this->criteria->addCondition("d.cat_id=:cateId");
                $this->criteria->params[":cateId"] = $cateId;
                $this->criteria->distinct = true;
            }
        }
    }
}