<?php

class ApiViewBookingList extends EApiViewService{
    private $BookingIds;  
    private $Bookings;
    private $bookingMgr;
    
    //初始化类的时候将参数注入
    public function __construct($BookingIds) {
        parent::__construct();
        $this->BookingIds = $BookingIds;        
        $this->bookingMgr = new BookingManager();
        $this->Bookings=array();
    }

    protected function loadData() {
        // load PatientBooking by creatorId.
        $this->loadBookings();        
    }
    //返回的参数
    protected function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'bookings' => $this->Bookings,                
            );
        }
    }
    
    //加载booking的数据
    private function loadBookings(){
        $attr = null;
        $with = array('bkFiles');
        $options = null;
        $models = $this->bookingMgr->loadAllBookingsByIds($this->BookingIds,$attr,$with,$options);
        //var_dump($models);        exit();
        if(arrayNotEmpty($models)){
            $this->setBookings($models);
        }
    }
    
    private function setBookings($models){
        foreach($models as $model){
            $data = new stdClass();
            $data->id = $model->getId();
//            $data->refNo = $model->getrefNo();
//            $data->UserId = $model->getUserId();
//            $data->mobile = $model->getMobile();
//            $data->contactName = $model->getContactName();
//            $data->contactEmail = $model->getContactEmail();
//            $data->bkStatus = $model->getBkStatus();
//            $data->bkType = $model->getBktype();
//            $data->doctorId = $model->getDoctorId();
//            $data->doctorName = $model->getDoctorName();
//            $data->expteamId = $model->getExpteamId();
//            $data->expteamName = $model->getExpteamName();
//            $data->cityId = $model->getCityId();
//            $data->hospitalId = $model->getHospitalId();
//            $data->hospitalName = $model->gethospitalName();
//            $data->expteamName = $model->getExpteamName();
//            $data->hpDeptId = $model->getHpDeptId();
//            $data->hpDeptName = $model->gethpDeptName();
//            $data->diseaseName = $model->getDiseaseName();
//            $data->diseaseDtail = $model->getDiseaseDtail();
//            $data->dateStart = $model->getDateStart();
//            $data->dateEnd = $model->getDateEnd();
//            $data->apptDate = $model->getApptDate();
//            $data->remark = $model->getRemark();
            
            $bookingFiles = $model->getBkFiles();
            
            if(arrayNotEmpty($bookingFiles)){
                foreach ($bookingFiles as $bookingFile){
                    $files = new stdClass();
                    $files->id = $bookingFile->getId();
                    $files->absFileUrl = $bookingFile->getAbsFileUrl();
                    $files->absThumbnailUrl = $bookingFile->getAbsThumbnailUrl();
                    $data->files[] = $files;
                }
            }else{
                 $data->files[] = NULL;
            }
           
            $this->Bookings[] = $data;
        }
    }
}
