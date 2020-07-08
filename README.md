## swoftJsonRpcRequest

**description : swoft客户端调用hyperf-jsonrpc服务**



***.env 配置信息***

```
# hyperf service
HYPERF_SERVICE_URI=tcp://127.0.0.1:9503
```



***调用方法***

```
//引入命名空间
use JsonRpcRequest\RequestRpc;
```

```
$client = new RequestRpc();
/**
* $name consul服务名（或者hypefjsonrpc服务名） e.g. 'CalculatorService'
* $method 方法名称 e.g. '/calculator/add'
* $params e.g. [1,2]
*/
$result = $client->send($name,$method, $params);
```



