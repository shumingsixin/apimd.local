<?php

class OverseasManager {

    public function loadHospitalsJson() {
        $hospitals = $this->loadHospitals();
        $values = array_values($hospitals);
        $output = array('topImageUrl' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_cover.jpg', 'osHospitals' => $values);

        return $output;
    }

    public function loadHospitals() {
        $hospitals = array(
            'sg-elizabeth' => array(
                'id' => 'sg-elizabeth',
                'name' => '新加坡伊丽莎白医院',
                'url' => Yii::app()->createAbsoluteUrl('mobile/overseas/hospital', array('id' => 'sg-elizabeth', 'header' => 0, 'addBackBtn' => 1)),
                'imageUrl' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_elizabeth.jpg'
            ),
            'sg-ktph' => array(
                'id' => 'sg-ktph',
                'name' => '新加坡邱德拔医院',
                'url' => Yii::app()->createAbsoluteUrl('mobile/overseas/hospital', array('id' => 'sg-ktph', 'header' => 0, 'addBackBtn' => 1)),
                'imageUrl' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_ktph.jpg'
            ),
            'sg-sgh' => array(
                'id' => 'sg-sgh',
                'name' => '新加坡中央医院',
                'url' => Yii::app()->createAbsoluteUrl('mobile/overseas/hospital', array('id' => 'sg-sgh', 'header' => 0, 'addBackBtn' => 1)),
                'imageUrl' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_sgh.jpg'
            ),
            'sg-nuh' => array(
                'id' => 'sg-nuh',
                'name' => '新加坡国立大学医院',
                'url' => Yii::app()->createAbsoluteUrl('mobile/overseas/hospital', array('id' => 'sg-nuh', 'header' => 0, 'addBackBtn' => 1)),
                'imageUrl' => 'http://mingyihz.oss-cn-hangzhou.aliyuncs.com/static%2Foverseas_sg_nuh.jpg',
            )
        );

        $output = array();
        foreach ($hospitals as $key1 => $hospital) {
            $obj = new stdClass();
            foreach ($hospital as $key2 => $value) {
                $obj->{$key2} = $value;
            }
            $output[$key1] = $obj;
        }

        return $output;
    }

}
