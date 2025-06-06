<?php

// No direct access
defined('ABSPATH') or die('No script kiddies please!');

JLoader::import('adapter.mvc.controllers.admin');

/**
 * VikRentCar plugin Shortcode controller.
 *
 * @since 	1.0
 * @see 	JControllerAdmin
 */
class VikRentCarControllerShortcode extends JControllerAdmin
{
	public function savenew()
	{
		$this->save(2);
	}

	public function saveclose()
	{
		$this->save(1);
	}

	public function save($close = 0)
	{
		$app = JFactory::getApplication();

		// get return URL
		$encoded = $app->input->getBase64('return', '');

		// set up redirect url in case of error
		$this->setRedirect('admin.php?page=vikrentcar&view=shortcodes' . ($encoded ? '&return=' . $encoded : ''));

		/**
		 * Added token validation.
		 *
		 * @since 1.4
		 */
		if (!JSession::checkToken() && !JSession::checkToken('get'))
		{
			if (wp_doing_ajax())
			{
				VRCHttpDocument::getInstance($app)->close(403, JText::_('JINVALID_TOKEN'));
			}
			
			// missing CSRF token
			$app->enqueueMessage(JText::_('JINVALID_TOKEN'), 'error');
			return false;
		}

		// make sure the user is authorised to change shortcodes
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrentcar'))
		{
			if (wp_doing_ajax())
			{
				VRCHttpDocument::getInstance($app)->close(403, JText::_('JERROR_ALERTNOAUTHOR'));
			}

			// not authorized
			return false;
		}

		// get item from request
		$data = $this->model->getFormData();

		// dispatch model to save the item
		$id = $this->model->save($data);

		if (!$id)
		{
			// get string error
			$error = $this->model->getError(null, true);
			$error = JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $error);

			if (wp_doing_ajax())
			{
				VRCHttpDocument::getInstance($app)->close(500, $error);
			}

			// display error message
			$app->enqueueMessage($error, 'error');

			$url = 'admin.php?page=vikrentcar&view=shortcode';

			if ($data->id)
			{
				$url .= '&cid[]=' . $data->id;
			}

			// redirect to new/edit page
			$this->setRedirect($url);
			return false;
		}

		if (wp_doing_ajax())
		{
			VRCHttpDocument::getInstance($app)->json($this->model->getItem($id));
		}

		if ($close == 2)
		{
			// save and new
			$return = 'admin.php?option=com_vikrentcar&task=shortcodes.create&return=' . $encoded;
		}
		else if ($close == 1)
		{
			// save and close
			$return = 'admin.php?option=com_vikrentcar&view=shortcodes&return=' . $encoded;
		}
		else
		{
			// save and stay in edit page
			$return = 'admin.php?option=com_vikrentcar&task=shortcodes.edit&cid[]=' . $id . '&return=' . $encoded;
		}

		$app->redirect($return);
	}

	public function params()
	{
		$input = JFactory::getApplication()->input;

		$id 	= $input->getInt('id', 0);
		$type 	= $input->getString('type', '');

		// dispatch model to get the item (an empty ITEM if not exists)
		$item = $this->model->getItem($id);

		// inject the type to load the right form
		$item->type = $type;

		// obtain the type form
		$form = $this->model->getTypeForm($item);

		// if the form doesn't exist, the type is probably empty
		if (!$form)
		{
			// return an empty HTML
			echo "";
		}
		// render the form and encode the response
		else
		{
			$args = json_decode($item->json);
			echo json_encode($form->renderForm($args));
		}
		
		exit;
	}

	/**
	 * This task will create a page on WordPress with the requested Shortcode inside it.
	 * This is useful to automatically link Shortcodes in pages with no manual actions.
	 * 
	 * @since 	1.4.0
	 */
	public function add_to_page()
	{
		$app 	= JFactory::getApplication();
		$input 	= $app->input;

		// get return URL
		$encoded = $input->getBase64('return', '');
		
		// always redirect to the shortcodes list
		$this->setRedirect('admin.php?option=com_vikrentcar&view=shortcodes&return=' . $encoded);

		// make sure the user is authorised to change shortcodes
		if (!JFactory::getUser()->authorise('core.admin', 'com_vikrentcar'))
		{
			return;
		}

		// get selected shortcodes
		$cid = $input->getUint('cid', array());

		// attempt to assign the shortcodes to a page
		if ($this->model->addPage($cid))
		{
			// add success message and redirect
			$app->enqueueMessage(JText::_('VRC_SC_ADDTOPAGE_OK'));
		}

		// fetch all registered errors (if any)
		$errors = $this->model->getErrors();

		foreach ($errors as $error)
		{
			if ($error instanceof Exception)
			{
				$error = $error->getMessage();
			}

			// enqueue error message
			$app->enqueueMessage($error, 'error');
		}
	}
}
