<?php

class IMedicalRecordFile extends EViewModel {

    public $id;
    public $uid;
    public $mrid;
    public $reportType;
    public $fileUrl;
    public $fileSize;
    public $thumbnailUrl;
    public $desc;
    public $dateTaken;

    public function initModel(MedicalRecordFile $model) {
        $this->id = $model->getId();
        $this->mrid = $model->getMrId();
        $this->reportType = $model->getReportType();
        $this->fileUrl = $model->getAbsFileUrl();
        $this->thumbnailUrl = $model->getAbsThumbnailUrl();
        $this->desc = $model->getDescription();
        $this->dateTaken = $model->getDateTaken();
        $this->fileSize = $model->getFileSize();
    }

    public function getMrId() {
        return $this->mrid;
    }

    public function getFileUrl() {
        return $this->fileUrl;
    }

    public function getThumbnailUrl() {
        return $this->thumbnailUrl;
    }

    public function getFileSize() {
        return $this->fileSize;
    }

    public function getDescription($ntext=false) {
        return $this->getTextAttribute($this->desc,$ntext);        
    }

    public function getDateTaken() {
        return $this->dateTalen;
    }

}
