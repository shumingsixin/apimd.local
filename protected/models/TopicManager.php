<?php

class TopicManager {

    public function loadAllTopic($query = null, $with = null, $options = null) {      
        $criteria = new CDbCriteria;
        $criteria->select = 's.id,s.topic,s.content_url,s.banner_url,s.like_count';
        $criteria->addCondition('t.`date_deleted` IS NULL');
        $criteria->order="display_order ASC";
        return SpecialTopic::model()->getAll($criteria);
        //$this->results->topicList = $topic;
    }
}
?>
