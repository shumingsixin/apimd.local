<?php

class ApiViewUserBooking extends EApiViewService {

    private $user;
    private $queryOptions;
    private $bookings;
    private $limitBookings = 10;

    public function __construct($user, $queryOptions = null) {
        parent::__construct();
        $this->user = $user;
        $this->queryOptions = $queryOptions;
    }

    /**
     * loads data by the given $user (current user).
     * @param User $user     
     */
    protected function loadData() {
        // load bookings by user.
        $this->loadBooking($this->user, $this->queryOptions);
    }

    private function loadBooking($user, $options) {
        $with = array('doctorBooked', 'expertTeamBooked');
        $bookingList = Booking::model()->getAllByUserIdOrMobile($user->getId(), $user->getMobile(), $with, $options);
        if (arrayNotEmpty($bookingList)) {
            $this->setBookings($bookingList);
        }
    }

    /**
     * @param array $models array of Booking models.
     */
    private function setBookings(array $models) {
        foreach ($models as $model) {
            $booking = new stdClass();
            $booking->ref_no = $model->getRefNumber();
            $booking->contact_name = $model->getContactName();
            $targetName = "";
            if (isset($model->doctorBooked)) {
                $targetName = $model->doctorBooked->name;
            } else if (isset($model->expertTeamBooked)) {
                $targetName = $model->expertTeamBooked->name;
            }
            $booking->targetName = $targetName;
            $booking->patient_condition = $model->getPatientCondition();


            $this->bookings[] = $booking;
        }
    }

    // create output.
    public function createOutput() {
        if (is_null($this->output)) {
            $this->output = array(
                'status' => self::RESPONSE_OK,
                'bookings' => $this->bookings,
            );
        }
    }

}
