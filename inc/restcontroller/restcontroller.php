<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require './inc/restcontroller/Curl.php';
require './inc/restcontroller/Cidr.php';

use Curl\Curl;

class restController {

    private $restUrl;
    private $curl;
    private $cidr;
    private $swData;

    public function __construct($resturl) {
        $this->restUrl = $resturl;
        try {
            $this->curl = new Curl();
        } catch (Exception $e) {
            echo $e->getMessage();
            echo "catched exception";
        }
        $this->cidr = new cidr();
    }

    public function refreshData() {
        $url = $this->restUrl . "/router/all";
        $this->curl->get($url);
        if ($this->curl->error) {
            echo "Curl error <br>";
            echo "Error: " . $curl->error_code . ': ' . $curl->error_message;
        } else {
            $this->swData = $this->curl->response;
        }
    }

    private function getRestAll() {
        return $this->swData;
    }

    function getSwitches() {
        $res = $this->getRestAll();
        $switches = array();
        foreach ($res as $entry) {
            $entry = (array) $entry;
            $switches[] = array('id' => $entry['switch_id'], 'label' => 'FW ' . substr($entry['switch_id'], -2),
                'group' => 'FW');
        }
        return $switches;
    }

    function getNetworks($edges) {
        $nonInterNetwork = array();
        foreach ($edges as $edge) {
            if (!isset($edge['label'])) {
                $nonInterNetwork[] = $edge['to'];
            }
        }
        $res = $this->getRestAll();
        $networks = array();
        foreach ($res as $node) {
            $arrobject = json_decode(json_encode($node), true);
            foreach ($arrobject['internal_network'][0]['address'] as $address) {
                $str = $address['address'];
                $data = explode('/', $str);
                $ip = $data[0];
                $mask = $data[1];
                $cidrip = $this->cidr->cidr2network($ip, $mask);
                $cidrip = $cidrip . "/" . $mask;
                if (in_array($cidrip, $nonInterNetwork)) {
                    $networks[$cidrip] = array('id' => $cidrip, 'label' => $cidrip, 'group' => 'NET');
                }
            }
        }
        $retarr = array();
        foreach ($networks as $net) {
            $retarr[] = $net;
        }
        return $retarr;
    }

    function getEdges() {
        $res = $this->getRestAll();
        $edges = array();
        foreach ($res as $node) {
            $sw = json_decode(json_encode($node), true);
            $swid = $sw['switch_id'];
            foreach ($sw['internal_network'][0]['address'] as $address) {
                $str = $address['address'];
                $data = explode('/', $str);
                $ip = $data[0];
                $mask = $data[1];
                $cidrip = $this->cidr->cidr2network($ip, $mask);
                $cidrip = $cidrip . "/" . $mask;
                $edges[] = array('from' => $swid, 'to' => $cidrip);
            }
        }
        $toarray = array();

        foreach ($edges as $edge) {
            if (isset($toarray[$edge['to']])) {
                $toarray[$edge['to']] += 1;
            } else {
                $toarray[$edge['to']] = 1;
            }
        }

        foreach ($toarray as $ip => $num) {
            if ($num == 2) {
                $joinarr = array();
                foreach ($edges as $key => $edge) {
                    if ($edge['to'] == $ip) {
                        $joinarr[] = $edge['from'];
                        unset($edges[$key]);
                    }
                }
                $edges[] = array('from' => $joinarr[0], 'to' => $joinarr[1], 'label' => $ip, 'length' => 250);
            }
        }
        $retarr = array();
        foreach ($edges as $ed) {
            $retarr[] = $ed;
        }
        return $retarr;
    }

    function getRoutingTables() {
        $res = $this->getRestAll();
        $res = json_decode(json_encode($res), true);
        $routing = array();

        foreach ($res as $router) {
            if (isset($router['internal_network'][0]['route'])) {
                $routing[$router['switch_id']] = $router['internal_network'][0]['route'];
            } else {
                $routing[$router['switch_id']] = null;
            }
        }
        return $routing;
    }
    
    function getCDNRouting() {
        $res = $this->getRestAll();
        $res = json_decode(json_encode($res), true);
        
        $routing = array();
        foreach ($res as $router) {
            if (isset($router['internal_network'][0]['cdn_routing'])) {
                $routing[$router['switch_id']] = $router['internal_network'][0]['cdn_routing'];
            } else {
                $routing[$router['switch_id']] = null;
            }
        }
        return $routing;
    }

    function getRequestRouters() {
        $res = $this->getRestAll();
        $res = json_decode(json_encode($res), true);
        $rrouters = array();

        foreach ($res as $router) {
            if (isset($router['internal_network'][0]['request_router'])) {
                $rrouters[$router['switch_id']] = $router['internal_network'][0]['request_router'];
            } else {
                $rrouters[$router['switch_id']] = null;
            }
        }
        return $rrouters;
    }

    function postRequestRouter($rrip, $switchid) {
        $url = $this->restUrl . '/cdn/rr/'. $switchid;
        
        $arr = array(
            'request_router'=>$rrip
        );
        
        $this->curl->post($url, json_encode($arr, JSON_UNESCAPED_SLASHES));
        return;
    }
    
    function postCDNRoute($prefix, $mask, $seip, $switchid) {
        $url = $this->restUrl . '/cdn/rr/' . $switchid;
        
        $mm = $this->cidr->netmask2cidr($mask);
        
        $arr = array(
            'destination' => $prefix.'/'.$mm,
            'service_engine' => $seip
        );
        
        $this->curl->post($url, json_encode($arr, JSON_UNESCAPED_SLASHES));
        return;
    }
    
    function getSessions($switchid) {
        $url = $this->restUrl. '/cdn/session/' . $switchid;
        $this->curl->get($url);
        if ($this->curl->error) {
            echo "Curl error <br>";
            echo "Error: " . $curl->error_code . ': ' . $curl->error_message;
        } else {
            
            $res = $this->curl->response;
            $res = json_decode(json_encode($res), true);
            
            $sessions = array();
            
            foreach ($res[0]['sessions'][0] as $sess) {
                $sessions[] = array_merge($sess, array('switchid' => $switchid));
            }
            return $sessions;
        }
    }
}
