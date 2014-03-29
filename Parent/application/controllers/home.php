<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct() {
        parent::__construct();
        $this->load->model('auth');
    }

    private function get_servers() {
        $this->load->model('servers');
        $this->load->model('porky');

        $servers = $this->servers->load_servers();
        if (is_array($servers) && count($servers) > 0) {
            foreach($servers as $key => $server) {
                $server['raw_data'] = $this->servers->server_last_log(array(
                    'server_id' => $server['id']
                ));

                //print_r(json_decode($server['raw_data']['data'], true));
                $this->porky->set(json_decode($server['raw_data']['data'], true));

                $server['os'] = $this->porky->get_os();
                $server['kernel'] = '';
                $server['architecture'] = '';
                $server['online'] = $this->servers->is_online($server['hostname'], $server['port_check']);
                $servers[$key] = $server;
            }
        }
        return $servers;
    }

    public function index()
    {
        if ((int)$this->session->userdata('id') > 0) {

            $this->load->view('dashboard', array(
                'page_title' => 'Dashboard',
                'servers' => $this->get_servers()
            ));
        } else {
            $this->load->view('login', array(
                'page_title' => 'Login'
            ));
        }
    }

    public function authenticate() {
        // disposition content
        header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
        header('Content-type: text/html');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sun, 29 Mar 1999 01:24:00 GMT");

        if ((int)$this->input->post('auth_time') >= time()) {
            if ($this->auth->verify($this->input->post('username'),$this->input->post('password'))) {
                $this->session->set_userdata($this->auth->get());
                redirect('/?h='.md5(time()).'&t='.time(), 'refresh');
            } else {
                $this->session->set_flashdata('error_login', 'Invalid Username or Password!');
                redirect('/?result=err&h='.md5(time()).'&t='.time(), 'refresh');
            }
        } else {
            $this->session->set_flashdata('error_login', 'Invalid Username or Password!');
            redirect('/?a=redir&h='.md5(time()).'&t='.time(), 'refresh');
        }
    }

    public function logout() {
        // disposition content
        header($_SERVER["SERVER_PROTOCOL"]." 200 OK");
        header('Content-type: text/html');
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Sun, 29 Mar 1999 01:24:00 GMT");
        $this->session->sess_destroy();
        redirect('/?a=login&h='.md5(time()).'&t='.time(), 'refresh');
    }

	public function test()
	{
        $data = $this->xsql->query("SELECT * FROM server_data WHERE server_id = 1 ORDER BY id DESC LIMIT 3", true);
        $log_data = json_decode($data['data'], true);
        echo '<pre>';
        print_r($log_data);
        print_r($data);
        echo '</pre>';
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */