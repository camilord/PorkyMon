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
                //$server['kernel'] = $this->poyk->get_kernel();
                //$server['architecture'] = $this->poyk->get_arc();
                $server['online'] = $this->servers->is_online($server['hostname'], $server['port_check']);
                //$server['memory'] = $this->porky->get_memory();
                //$server['hdd'] = $this->porky->get_hdd();
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

    public function updates() {
        $this->load->model('updates');
        $this->load->model('servers');
        $opt = $this->secure->clean($this->uri->segment(3), 'text_strict');

        switch($opt) {
            case 'all':
                $updates_data = $this->updates->get_all();
                break;
            case 'sms':
                $updates_data = $this->updates->get_sms();
                break;
            default:
                // show recent unresolved updates...
                $updates_data = $this->updates->get_recent();
                break;
        }

        $level_notice = array(
            'error' => 'danger',
            'warning' => 'warning',
            'info' => 'info'
        );

        if (is_array($updates_data) && count($updates_data) > 0) {
            echo '<table class="table table-striped table-bordered table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>ID</th><th>Server</th><th>Message</th><th>Resolved</th><th>SMS</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>';
            foreach ($updates_data as $item) {
                echo '<tr class="'.$level_notice[$item['report_type']].'">
                        <td>'.$item['id'].'</td>
                        <td>'.$this->servers->get_name($item['server_id']).'</td>
                        <td>'.$item['report'].'</td>
                        <td>'.(($item['resolved'] == 'y') ? 'Yes' : 'No').'</td>
                        <td>'.(($item['sms'] == 'y') ? 'Yes' : 'No').'</td>
                        <td width="100">
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary btn-xs dropdown-toggle" data-toggle="dropdown">
                                    Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><a href="#">Action</a></li>
                                    <li><a href="#">Another action</a></li>
                                    <li><a href="#">Something else here</a></li>
                                    <li class="divider"></li>
                                    <li><a href="#">Separated link</a></li>
                                </ul>
                            </div>
                        </td>
                      </tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<div class="alert alert-info text-left">There are no server updates as of the moment...</div>';
        }
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */