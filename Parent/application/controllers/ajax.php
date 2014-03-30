<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function index()
	{
		$this->load->view('welcome_message');
	}

    private function get_stats() {
        $this->load->model('servers');
        $this->load->model('porky');

        $server_data = array();
        $server_id = $this->secure->clean($this->uri->segment(3),'numeric');

        $stats_data = $this->servers->stats_data($server_id);
        foreach ($stats_data as $data) {
            $raw_data = json_decode($data['data'], true);
            $raw_data['received'] = strtotime($data['created']);
            $server_data[] = $raw_data;
        }
        // set porky parser to parse...
        $this->porky->set($server_data);

        $memory_data = $this->porky->get_memory_data($server_data);
        $hdd_data = $this->porky->get_hdd_data($server_data);
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
                $server['memory'] = $this->porky->get_memory();
                $server['hdd'] = $this->porky->get_hdd();
                $servers[$key] = $server;
            }
        }
        return $servers;
    }

    public function server() {
        $this->load->model('porky');
        $opt = $this->secure->clean($this->uri->segment(3), 'text_strict');

        switch($opt) {
            case 'list':
                $servers = $this->get_servers();
                if (is_array($servers) && count($servers) > 0) {
                    foreach ($servers as $server) {
                        echo '<div class="col-xs-6 col-sm-3 placeholder">
                                <a href="/server/stats/'.$server['id'].'/'.md5(time()).'"><img src="/public/images/child_server_'.(($server['online'] >= 0) ? 'online' : 'offline').'.png" /></a>
                                <h4>'.$server['hostname'].'</h4>
                                <span>'.$server['os'].'</span><br />
                                <!-- span>'.$this->porky->bytes2size($server['memory']['total']).'</span><br />
                                <span>'.$this->porky->bytes2size($server['hdd']['total']).'</span><br / -->
                                <span class="text-muted">'.$server['ip'].'</span>
                            </div>';
                    }
                } else {
                    echo '<div class="alert alert-info text-left">There are no servers have been added yet.</div>';
                }
                break;
            default:
                echo 'Invalid AJAX Request!';
                break;
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */