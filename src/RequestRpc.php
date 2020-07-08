<?php

namespace JsonRpcRequest;

use GuzzleHttp\Client;

class RequestRpc
{

    private $consul;
    private $client;

    public function __construct(){
        $this->consul = env('CONSUL_ADDRESS').':'.env('CONSUL_PORT');
        $this->client = $this->client = new Client();
    }

    public function send($name, $method, $params){
        try{
            //consul健康检查和获取服务
            $check = $this->client->request('GET',$this->consul.'/v1/agent/checks');
            $service = $this->client->request('GET', $this->consul.'/v1/agent/services');
            $checks = json_decode((string) $check->getBody(),true);
            $services = json_decode((string) $service->getBody(),true);

            //获取consul服务地址
            if (array_key_exists('service:'.$name.'-0',$checks)){
                if ($checks['service:'.$name.'-0']['Status'] == 'passing'){
                    $consuladdr = $services[$name.'-0']['Address'].':'.$services[$name.'-0']['Port'];
                }
            }else if (array_key_exists('service:'.$name,$checks)){
                if ($checks['service:'.$name]['Status'] == 'passing'){
                    $consuladdr = $services[$name]['Address'].':'.$services[$name]['Port'];
                }
            }
            $addr = $consuladdr;
        }catch (\Exception $exception){
            //获取node节点服务地址
            $addr = env('HYPERF_SERVICE_URI');
        }

        //连接服务和发送数据
        $fp = stream_socket_client($addr, $errno, $errstr);
        if (!$fp) {
            throw new \Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
        }
        $data = [
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
            'id' => NULL,
            'context' => []
        ];

        $data = json_encode($data, JSON_UNESCAPED_UNICODE)."\r\n";
        fwrite($fp,$data);
        $result = fread($fp, 1024);
        fclose($fp);
        return $result;
    }
}