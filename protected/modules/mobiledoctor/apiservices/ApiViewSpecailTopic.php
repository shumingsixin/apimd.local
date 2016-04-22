<?php

class ApiViewSpecailTopic extends EApiViewService {

    private $topicMgr;
    private $topicList;
    
    public function __construct() {
        parent::__construct();
        $this->results = new stdClass();
        $this->topicMgr = new TopicManager();
        $this->topicList = array();
    }

    protected function loadData() {
        $this->loadTopic();
    }

    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'errorCode' => 0,
                'errorMsg' => 'success',
                'results' => $this->results,
            );
        }
    }

    public function loadTopic() {
        $attributes=null;
        $with = array('specialTopics');
        $options = null;
        $models = $this->topicMgr->loadAllTopic($attributes, $with, $options);
        if (arrayNotEmpty($models)) {
            $this->setTopicList($models);
        } else {
            $this->topicList = null;
        }
    }
    
    public function setTopicList($models){
        $data = new stdClass();
        foreach($models as $model){
            $array= $model->attributes;
            $data->id = $array['id'];
            $data->topic = $array['topic'];
            $data->content_url = $array['content_url'];
            $data->banner_url = $array['banner_url'];
            $data->like_count = $array['like_count'];
            $data->date_published = $array['date_published'];
            $data->display_order = $array['display_order'];
            $data->date_created = $array['date_created'];
            $this->results->topicList[] = $data;
        }
    }

}
?>