<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gateway extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

    public function receive() {
        $this->load->model('crypt');
        $this->load->model('logger');

        $child_auth = unserialize($this->crypt->decode($this->input->post('child_auth')));
        $child_data = unserialize($this->crypt->decode($this->input->post('child_data')));

        if (is_array($child_auth) && is_array($child_data)) {
            $this->logger->log(array('auth' => $child_auth, 'data' => $child_data));
        }

        echo 'test: '.time();
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */