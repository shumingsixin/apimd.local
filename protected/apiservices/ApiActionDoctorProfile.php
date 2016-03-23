<?php

class ApiActionDoctorProfile extends EApiActionService {

    private $userMgr;

    public function __construct($requestedValues) {
        parent::__construct($requestedValues);
        $this->userMgr = new UserManager();
    }

    public function run() {
        $this->formModel = $this->userMgr->createDoctorProfileForm($this->requestvalues);
        $this->formModel->validate();
        if ($this->formModel->hasErrors()) {
            $this->errors = $this->formModel->getErrors();
        } else {
            $this->model = $this->userMgr->createDoctorProfile($this->formModel->attributes);
            if ($this->model->hasErrors()) {
                $this->errors = $this->model->getErrors();
            }
        }
    }
    
    

}
