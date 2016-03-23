<?php

class DiseaseManager {

    public function loadDiseaseById($id, $with = null) {
        return Disease::model()->getById($id, $with);
    }

    public function loadListDisease() {
        $output = array();
        $dcList = DiseaseCategory::model()->getAll(array('dcDiseases'), array('order' => 't.id'));
        if (empty($dcList)) {
            return $output;
        }
        foreach ($dcList as $dc) {
            $catName = $dc->cat_name;
            $subCatName = $dc->sub_cat_name;
            $diseaseList = $dc->dcDiseases;
            $subCatDisList = array();
            if (arrayNotEmpty($diseaseList)) {
                foreach ($diseaseList as $disease) {
                    $dataDisease = new stdClass();
                    $dataDisease->id = $disease->id;
                    $dataDisease->name = $disease->name;
                    $subCatDisList[] = $dataDisease;
                }
            }
            $output[$catName][$subCatName] = $subCatDisList;
        }
        return $output;
    }

    public function loadDiseaseCategoryListV7() {
        $models = DiseaseCategory::model()->getAllByInCondition('t.app_version', 7);
        return $models;
    }
    public function loadDiseaseCategoryList() {
        $models = DiseaseCategory::model()->getAllByInCondition('t.app_version', null, array('dcDiseases'));

        return $models;
    }

    public function loadAllDiseasesByDoctorId($doctorId) {
        $output = new stdClass();
        $list = DiseaseDoctorJoin::model()->getAllByDoctorId($doctorId);
        $output->doctorId = $doctorId;
        $output->diseaseIds = arrayExtractValue($list, 'disease_id');
        //$output->diseaseIds = CHtml::listData($list, 'disease_id');
        return $output;
    }

    public function updateDoctorDiseaseJoin(DoctorDiseaseJoinForm $form) {
        if ($form->validate()) {
            $listDelete = $form->getDiseaseListDelete();
            $doctorId = $form->getDoctorId();
            //Start a new db transaction.
            $dbTran = Yii::app()->db->beginTransaction();
            try {
                if (arrayNotEmpty($listDelete)) {
                    DiseaseDoctorJoin::model()->deleteAllByDoctorIdAndDiseaseIds($doctorId, $listDelete);
                }
                $listInsert = $form->getDiseaseListInsert();
                if (arrayNotEmpty($listInsert)) {
                    foreach ($listInsert as $v) {
                        $diseaseDoctorJoin = new DiseaseDoctorJoin();
                        $diseaseDoctorJoin->disease_id = $v;
                        $diseaseDoctorJoin->doctor_id = $doctorId;
                        $diseaseDoctorJoin->save();
                    }
                }
                //操作成功 数据库提交
                $dbTran->commit();
            } catch (CDbException $e) {
                $form->addError('数据库操作失败');
                $dbTran->rollback();
                throw new CHttpException($e->getMessage());
            } catch (CException $e) {
                $form->addError('操作失败');
                $dbTran->rollback();
                Yii::log("database table disease_doctor_join jdbc: " . $e->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
                throw new CHttpException($e->getMessage());
            }
        }
    }

}
