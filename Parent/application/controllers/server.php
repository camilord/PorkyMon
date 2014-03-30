<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Server extends CI_Controller {

    private function get_details() {
        $this->load->model('servers');
        $this->load->model('porky');
        $server_id = $this->secure->clean($this->uri->segment(3),'numeric');

        $server_details = $this->servers->load_server(array('id' => $server_id));
        $server_details['raw_data'] = $this->servers->server_last_log(array(
            'server_id' => $server_id
        ));
        $this->porky->set(json_decode($server_details['raw_data']['data'], true));

        $server_details['os'] = $this->porky->get_os();
        $server_details['kernel'] = $this->porky->get_kernel();
        $server_details['architecture'] = $this->porky->get_arc();
        $server_details['online'] = $this->servers->is_online($server_details['hostname'], $server_details['port_check']);
        $server_details['memory'] = $this->porky->get_memory();
        $server_details['hdd'] = $this->porky->get_hdd();

        return $server_details;
    }

    public function index()
	{
		$this->load->view('welcome_message');
	}

    public function stats() {
        $this->load->model('porky');
        $this->load->view('server_view', array(
            'page_title' => 'View Server',
            'server_details' => $this->get_details()
        ));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */