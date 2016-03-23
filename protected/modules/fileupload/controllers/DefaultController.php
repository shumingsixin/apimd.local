<?php

class DefaultController extends FileuploadController
{
	public function actionIndex()
	{
		$this->render('index');
	}
}