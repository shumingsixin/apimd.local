<?php
/* @var $this BookingController */
/* @var $model Booking */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'booking-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ref_no'); ?>
		<?php echo $form->textField($model,'ref_no',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'ref_no'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_id'); ?>
		<?php echo $form->textField($model,'user_id'); ?>
		<?php echo $form->error($model,'user_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'mobile'); ?>
		<?php echo $form->textField($model,'mobile',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'mobile'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'faculty_id'); ?>
		<?php echo $form->textField($model,'faculty_id'); ?>
		<?php echo $form->error($model,'faculty_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'booking_target'); ?>
		<?php echo $form->textField($model,'booking_target',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'booking_target'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'status'); ?>
		<?php echo $form->textField($model,'status'); ?>
		<?php echo $form->error($model,'status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'patient_condition'); ?>
		<?php echo $form->textField($model,'patient_condition',array('size'=>60,'maxlength'=>200)); ?>
		<?php echo $form->error($model,'patient_condition'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'appt_date'); ?>
		<?php echo $form->textField($model,'appt_date'); ?>
		<?php echo $form->error($model,'appt_date'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_email'); ?>
		<?php echo $form->textField($model,'contact_email',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'contact_email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'contact_weixin'); ?>
		<?php echo $form->textField($model,'contact_weixin',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'contact_weixin'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'user_host_ip'); ?>
		<?php echo $form->textField($model,'user_host_ip',array('size'=>15,'maxlength'=>15)); ?>
		<?php echo $form->error($model,'user_host_ip'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_created'); ?>
		<?php echo $form->textField($model,'date_created'); ?>
		<?php echo $form->error($model,'date_created'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_updated'); ?>
		<?php echo $form->textField($model,'date_updated'); ?>
		<?php echo $form->error($model,'date_updated'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'date_deleted'); ?>
		<?php echo $form->textField($model,'date_deleted'); ?>
		<?php echo $form->error($model,'date_deleted'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->