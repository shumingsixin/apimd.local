<?php

class TripManager {

    public function createTrip(TripForm $tripForm) {
        if ($tripForm->validate()) {

            //Pass data to Trip model.
            $trip = new Trip();
            $trip->attributes = $tripForm->attributes;

            $trip->id = null;
            //Converts fields to db format.
            $trip->normalizeModel();
            $trip->date_created = new CDbExpression("NOW()");

            //TripDetail.
            $tripDetail = new TripDetail();
            $tripDetail->attributes = $tripForm->attributes;
            $tripDetail->normalizeModel();
            $trip->setTripDetail($tripDetail);

            // Save trip model.
            if ($tripForm->scenario == 'new' || $tripForm->scenario == 'publish') {
                $trip->save();
            } else {
                $trip->save(); //save without validation.
            }

            //Trip errors.
            if ($trip->hasErrors()) {
                $tripForm->addErrors($trip->getErrors());
                return false;
            }

            $tripForm->setId($trip->getId());

            // Save trip_detail model.
            $tripDetail->setTripId($trip->getId());
            if ($tripDetail->save() === false) {
                $tripForm->addErrors($tripDetail->getErrors());
                return false;
            }

            // Save trip images.
            // $this->saveTripImages($trip->getId());
        }
        return ($tripForm->hasErrors() === false);
    }

    public function updateTrip(TripForm $tripForm) {
        if ($tripForm->validate()) {
            $trip = Trip::model()->getById($tripForm->id, array('tripDetail'));
            if (isset($trip) === false) {
                $tripForm->addError('title', '操作失败-无法读取数据。请联系我们的客服。');
                return false;
            }
            //Pass data to Trip model.
            $trip->attributes = $tripForm->attributes;
            $trip->normalizeModel();
            /**
             * 2014-09-13 - QP
             * Remove checking on is_published && approval_status is $trip is already approved.

              $trip->is_published = 0;
              $trip->approval_status = Trip::APPROVAL_STATUS_NONE;
              $trip->date_approved = null;
              $trip->approved_by = null;
             */
            $trip->date_updated = new CDbExpression("NOW()");

            //TripDetail.
            $tripDetail = $trip->getTripDetail();
            if (isset($tripDetail) === false) {
                $tripDetail = new TripDetail();
                $tripDetail->setTripId($trip->getId());
                $trip->setTripDetail($tripDetail);
            }
            $tripDetail->attributes = $tripForm->attributes;
            $tripDetail->normalizeModel();


            //Save Trip.
            if ($tripForm->scenario == 'update' || $tripForm->scenario == 'publish') {
                $trip->save();
            } else {
                $trip->save();
            }
            //Trip errors.
            if ($trip->hasErrors()) {
                $tripForm->addErrors($trip->getErrors());
                return false;
            }

            // Save trip_detail model
            if ($tripDetail->save() === false) {
                $tripForm->addErrors($tripDetail->getErrors());
                return false;
            }

            // Save trip images.
            // return $this->saveTripImages($trip->getId());
        }
        return ($tripForm->hasErrors() === false);
    }

    public function deleteTrip(Trip $trip) {
        $tripDetail = $trip->getTripDetail();
        $tripImages = $trip->getTripImages();
        $trip->delete(false);
        if (isset($tripDetail)) {
            $tripDetail->delete(false);
        }
        if (emptyArray($tripImages) === false) {
            foreach ($tripImages as $image) {
                $image->deleteModel(true);
            }
        }
        return true;
    }

    public function doUnpublishTrip($trip) {
        $trip->is_published = 0;
        return $trip->updatePublished();
    }

    public function doApproveTrip($trip, $approverId) {
        $trip->approval_status = Trip::APPROVAL_STATUS_APPROVED;
        $trip->approved_by = $approverId;
        return $trip->updateApproved();
    }

    public function doDisapproveTrip($trip, $approverId) {
        $trip->approval_status = Trip::APPROVAL_STATUS_REJECTED;
        $trip->approved_by = $approverId;
        return $trip->updateApproved();
    }

    public function loadInterfaceModel($id, $with=null) {
        $itrip = ITrip::model()->getById($id, $with);
        if ($itrip === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        $itrip->initModel();

        return $itrip;
    }

    /*
     * Load trip data into trip form.
     */

    public function loadFormModel($id, $with=null) {
        $trip = $this->loadTripModelById($id, $with);
        $attributes = $trip->attributes;
        if (isset($trip->tripDetail)) {
            $attributes = array_merge($attributes, $trip->tripDetail->attributes);
        }

        $attributes['id'] = $trip->getId();
        $tripForm = new TripForm('update');
        $tripForm->initModel($attributes, $trip->scenario);
        $tripForm->setTripImages($trip->tripImages);

        return $tripForm;
    }

    public function loadTripModelById($id, $with=null) {
        $trip = Trip::model()->getById($id, $with);
        if ($trip === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $trip;
    }

    public function loadTripImageById($id, $with=null) {
        $tripImage = TripImage::model()->getById($id, $with);
        if ($tripImage === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $tripImage;
    }

    //TODO: re-implement saving image process.
    public function saveTripImages($tripId) {
        //TODO: implement saveImages() to return boolean.
        $tripImage = new TripImage();
        $tripImage->trip_id = $tripId;
        $ret = $tripImage->saveImages($tripId);

        return $ret;
    }

    /**
     * Delete item image based on item image id
     * @param int $id
     */
    public function deleteTripImage(TripImage $tripImage) {
        return $tripImage->deleteModel(true);
    }

    //used at home page.
    public function getFavTrips($limit=5) {
        //get ordered list of trip ids from TripRanking.
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.type', TripRanking::TYPE_FAV_TRIP);
        $criteria->order = 't.display_order';
        $criteria->limit = $limit;
        $tripRankings = TripRanking::model()->findAll($criteria);
        //get trips
        $tripIds = arrayExtractKeyValue($tripRankings, 'display_order', 'trip_id');
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->addInCondition('t.id', $tripIds);
        $criteria->with = array('tripDisplayPhoto', 'tripCreator', 'tripCity', 'tripDetail');
        $trips = ITrip::model()->findAll($criteria);
        $ret = array();
        foreach ($tripIds as $tId) {
            foreach ($trips as $trip) {
                if ($trip->id == $tId) {
                    $ret[] = $trip;
                    continue;
                }
            }
        }

        return $ret;
    }

    //used at home page.
    public function getNewTrips($limit=6) {
        $limit+=1;
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.is_published', 1);
        $criteria->compare('t.approval_status', ApprovalStatusModel::APPROVAL_STATUS_APPROVED);
        $criteria->order = 't.date_published DESC';
        $criteria->limit = $limit;
        $criteria->with = array(
            // 'tripDisplayPhoto', 
            'tripCreator',
            'tripCity',
            'tripDetail'
        );
        $criteria->together = true;
        $trips = ITrip::model()->findAll($criteria);

        return $trips;
    }

    public function getTripsByCategory($category, $limit=6, $offset=0) {
        $limit+=1;
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('t.is_published', 1);
        $criteria->compare('t.approval_status', ApprovalStatusModel::APPROVAL_STATUS_APPROVED);
        $criteria->addSearchCondition('category', $category);
        $criteria->order = 't.date_published DESC';
        $criteria->limit = $limit;
        // $criteria->offset=$offset;
        $criteria->with = array(
            //'tripDisplayPhoto', 
            'tripCreator', 'tripCity', 'tripDetail'
        );
        $trips = ITrip::model()->findAll($criteria);

        return $trips;
    }

    public function loadOptionsCategory($lang='cn') {
        if ($lang == 'cn')
            return arrayExtractKeyValue(TripSetting::model()->getAllCategory('display_order'), 'code', 'display_name_cn');
        else
            return arrayExtractKeyValue(TripSetting::model()->getAllCategory('display_order'), 'code', 'display_name');
    }

    /*     * ****** Trip Comments ******* */

    public function createTripComment(TripComment $tripComment) {
        return $tripComment->save();
    }

    public function deleteTripComment(TripComment $tripComment) {
        return $tripComment->delete(false);
    }

    public function loadTripComments($tripId, $offset=0, $limit=5) {
        if ($limit < 0)
            $limit = 5;
        $criteria = new CDbCriteria();
        $criteria->addCondition('t.date_deleted is NULL');
        $criteria->compare('trip_id', $tripId);
        $criteria->order = "t.date_created DESC";
        $criteria->offset = $offset;
        $criteria->limit = $limit;
        $criteria->with = array('creator');
        return TripComment::model()->findAll($criteria);
    }

    public function loadTripCommentById($commentId, $with=null) {
        $tripComment = TripComment::model()->getById($commentId, $with);
        if ($tripComment === null) {
            throw new CHttpException(404, 'The requested page does not exist.');
        }
        return $tripComment;
    }

    /*     * ****** Trip Rating ******* */

    public function addTripRating($tripId, $creatorId, $score) {
        $tripRating = TripRating::model()->getByTripIdAndCreatorId($tripId, $creatorId);
        if ($tripRating === null) {
            $tripRating = new TripRating();
            $tripRating->trip_id = $tripId;
            $tripRating->creator_id = $creatorId;
            $tripRating->score = $score;
            $tripRating->save();
        } else {
            $tripRating->score = $score;
            $tripRating->save(true, array('score', 'date_updated'));
        }
        return $tripRating->getErrors();
    }

    public function loadAvgTripRatingScore($tripId) {
        $data = TripRating::model()->getAvgScoreByTripId($tripId);
        $data['id'] = $data['trip_id'];
        unset($data['trip_id']);
        $score = roundToNearestN($data['score'], 0.5);
        if ($score == 0) {  //if not rated yet, set default score to 3.
            $score = 3;
        }
        $data['score'] = $score;
        return $data;
    }

    public function loadTripRatingScoreByUser($tripId, $userId) {
        $tripRating = TripRating::model()->getByTripIdAndCreatorId($tripId, $userId);
        $data = array('id' => null, 'score' => null, 'uid' => null);
        if ($tripRating !== null) {
            $data['id'] = $tripRating->getTripId();
            $data['score'] = $tripRating->getScore();
            $data['uid'] = $tripRating->getCreatorId();
        }

        return $data;
    }

}

