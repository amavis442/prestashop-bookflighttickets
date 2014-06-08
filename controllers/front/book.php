<?php
/**
 * @since 1.5.0
 */
class BookModuleFrontController extends ModuleFrontController
{

	public function __construct()
	{
		parent::__construct();

		$this->context = Context::getContext();
	}


	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		//$cart = $this->context->cart;
		echo 'Is ok';
		parent::initContent();

		/*
		if (Tools::isSubmit('action'))
		{
			switch(Tools::getValue('action'))
			{
				case 'add_comment':
					$this->ajaxProcessAddComment();
					break;
				case 'report_abuse':
					$this->ajaxProcessReportAbuse();
					break;
				case 'comment_is_usefull':
					$this->ajaxProcessCommentIsUsefull();
					break;
		}
		*/
	}
}
