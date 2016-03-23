<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of AdminTaskSearch
 *
 * @author shuming
 */
class AdminTaskSearch extends ESearchModel {

    public function __construct($searchInputs, $with = null) {
        parent::__construct($searchInputs, $with);
    }

    public function model() {
        $this->model = new AdminTaskJoin();
    }

    public function getQueryFields() {
        return array('admin_user_id', 'status', 'is_read', 'type', 'date_plan', 'date_done', 'subject', 'content');
    }

    public function addQueryConditions() {

        if ($this->hasQueryParams()) {
            if (isset($this->queryParams['subject'])) {
                $userId = $this->queryParams['subject'];
                $this->criteria->compare('t.subject', $userId, false);
            }
            if (isset($this->queryParams['status'])) {
                $status = $this->queryParams['status'];
                $this->criteria->compare('t.status', $status, false);
            }
            if (isset($this->queryParams['is_read'])) {
                $isRead = $this->queryParams['is_read'];
                $this->criteria->compare('t.is_read', $isRead, false);
            }

            if (isset($this->queryParams['type'])) {
                $type = $this->queryParams['type'];
                $this->criteria->compare('t.type', $type, false);
            }

            if (isset($this->queryParams['date_plan'])) {
                $datePlan = $this->queryParams['date_plan'];
                $this->criteria->compare("t.date_plan", $datePlan, true);
            }

            if (isset($this->queryParams['date_done'])) {
                $dateDone = $this->queryParams['date_done'];
                $this->criteria->compare('t.date_done', $dateDone, true);
            }

            if (isset($this->queryParams['subject'])) {
                $subject = $this->queryParams['subject'];
                $this->criteria->compare('adminTask.subject', $subject, true);
            }

            if (isset($this->queryParams['content'])) {
                $content = $this->queryParams['content'];
                $this->criteria->compare('adminTask.content', $content, true);
            }
        }
    }

}
