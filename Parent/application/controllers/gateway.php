<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gateway extends CI_Controller {

	public function index()
	{
        redirect('/','refresh');
	}

    public function receive() {
        $this->load->model('crypt');
        $this->load->model('logger');
        $this->load->model('auth');

        if ($this->config->item('data_receiver_method') == 'get') {
            $child_auth = unserialize($this->crypt->decode($this->input->get('child_auth')));
            $child_data = unserialize($this->crypt->decode($this->input->get('child_data')));
        } else {
            $child_auth = unserialize($this->crypt->decode($this->input->post('child_auth')));
            $child_data = unserialize($this->crypt->decode($this->input->post('child_data')));
        }

        // disposition content
        header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
        header('Content-type: application/json');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sun, 29 Mar 1999 01:24:00 GMT");

        if (is_array($child_auth) && is_array($child_data)) {
            if ($this->auth->identify($child_auth['child_server'], $child_auth['secret_key'])) {
                $server_details = $this->auth->get();
                $xid = $this->xsql->insert('server_data', array(
                    'server_id' => $server_details['id'],
                    'hostname' => $server_details['hostname'],
                    'data' => json_encode($child_data),
                    'created' => date('Y-m-d H:i:s')
                ));
                echo json_encode(array('result' => true, 'message' => 'Success!', 'id' => $xid, 'timestamp' => time()));
            } else {
                echo json_encode(array('result' => false, 'message' => 'Invalid child server credentials.', 'timestamp' => time()));
            }
            // temporary...
            $this->logger->log(array('auth' => $child_auth, 'data' => $child_data));
        } else {
            echo json_encode(array('result' => false, 'message' => 'Invalid submitted data.', 'timestamp' => time()));
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */