<?php

class ApiViewBooking extends EApiViewService {

    private $bookingId;
    private $booking;
    private $owner;
    private $expertBooked;      // ExpertTeam or Doctor model.
    private $files;
//    private $hospital;
//    private $hpDept;
    private $bookingMgr;
    private $modelBooking;      // Booking model.

    //初始化类的时候将参数注入

    public function __construct($bookingId) {
        parent::__construct();
        $this->bookingId = $bookingId;
        $this->files = array();
        $this->bookingMgr = new BookingManager();
    }

    protected function loadData() {
        // load Booking by id.
        $this->loadBooking();
        $this->loadOwner();
        $this->loadExpertBooked();
        $this->loadBookingFiles();
    }

    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'booking' => $this->booking,
                'owner' => $this->owner,
                'expertBooked' => $this->expertBooked,
                'files' => $this->files
            );
        }
    }

    private function loadBooking() {
        if (is_null($this->booking)) {
            $with = array('bkOwner', 'bkDoctor', 'bkExpertTeam', 'bkFiles', 'bkFiles');
            $this->modelBooking = $this->bookingMgr->loadBookingById($this->bookingId, $with);
            if (isset($this->modelBooking)) {
                $this->modelBooking = $this->modelBooking;
                $this->setBooking($this->modelBooking);
            } else {
                $this->throwNoDataException();
            }
        }
    }

    private function loadOwner() {
        if (is_null($this->owner)) {
            if (isset($this->modelBooking) && $this->modelBooking->getOwner() !== null) {
                $owner = $this->modelBooking->getOwner();
                $this->setOwner($owner);
            }
        }
    }

    private function loadExpertBooked() {
        if (is_null($this->expertBooked)) {
            if (isset($this->modelBooking) && $this->modelBooking->getExpertBooked() !== null) {
                $expert = $this->modelBooking->getExpertBooked();
                $this->setExpertBooked($expert);
            }
        }
    }

    private function loadBookingFiles() {
        if (arrayNotEmpty($this->files) === false) {
            if (isset($this->modelBooking)) {
                $bookingFiles = $this->modelBooking->getBkFiles();
                if (arrayNotEmpty($bookingFiles)) {
                    $this->setBookingFiles($bookingFiles);
                }
            }
        }
    }

    private function setBooking(Booking $model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->refNo = $model->getRefNo();
        $data->userId = $model->getUserId();
        $data->bkStatus = $model->getBkStatus();
        $data->expertName = $model->getExpertNameBooked();
        $data->mobile = $model->getMobile();
        $data->hospitalName = $model->getHospitalName();
        $data->hpDeptName = $model->getHpDeptName();
        $data->patientName = $model->getContactName();
        $data->diseaseName = $model->getDiseaseName();
        $data->diseaseDetail = $model->getDiseaseDetail();
        $data->dateCreated = $model->getDateCreated();

        $this->booking = $data;
    }

    private function setOwner(User $owner) {
        $data = new stdClass();
        $data->id = $owner->getId();
        $data->mobile = $owner->getMobile();

        $this->owner = $data;
    }

    private function setExpertBooked($model) {
        $data = new stdClass();
        $data->id = $model->getId();
        $data->name = $model->getName();
        $this->expertBooked = $data;
    }

    private function setBookingFiles(array $models) {
        foreach ($models as $model) {
            $data = new stdClass();
            $data->id = $model->getId();
            $data->url = $model->getAbsFileUrl(); // @TODO: delete.
            $data->fileUrl = $model->getAbsFileUrl();
            $data->tnUrl = $model->getAbsThumbnailUrl();
            $this->files[] = $data;
        }
    }

}
