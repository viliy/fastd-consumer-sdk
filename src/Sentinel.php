<?php
/**
 * @author: ZhaQiu <34485431@qq.com>
 * @time: 2018/9/3
 */

namespace Sdk\Signaller;

use FastD\Utils\ArrayObject;
use Sdk\Signaller\Contracts\SentinelInterface;

class Sentinel extends ArrayObject implements SentinelInterface
{

    /**
     * @var array
     */
    protected $nodes = [];

    /**
     * @var array
     */
    protected $node = [];

    /**
     * @var string
     */
    protected $path = '/tmp/services';

    /**
     * Sentinel constructor.
     * @param string $path
     */
    public function __construct(string $path = null)
    {
        !is_null($path) && $this->path = $path;
    }

    /**
     * 获取所有服务节点
     *
     * @return array
     */
    public function list() : array
    {
        $services = glob($this->path . '/*.php');

        foreach ($services as $service) {
            $serviceNames[] = basename($service, '.php');
        }

        return $serviceNames ?? [];
    }

    /**
     * 获取服务节点
     *
     * @param string $serviceName
     * @return array
     */
    public function node(string $serviceName): array
    {
        $node = $this->getNode($serviceName);

        return $node;
    }

    /**
     * @param string $serviceName
     * @return string
     */
    public function host(string $serviceName): string
    {
        $node = $this->getNode($serviceName);

        return $node['service_host'];
    }

    /**
     * @param string $serviceName
     * @return string
     */
    public function protocol(string $serviceName): string
    {
        $node = $this->getNode($serviceName);

        return $node['service_protocol'];
    }

    /**
     * @param string $serviceName
     * @return string
     */
    public function port(string $serviceName): string
    {
        $node = $this->getNode($serviceName);

        return $node['service_port'];
    }

    /**
     * 获取服务节点所有路由
     *
     * @param string $serviceName
     * @return array
     */
    public function routes(string $serviceName): array
    {
        $node = $this->getNode($serviceName);

        return $node['routes'] ?? [];
    }

    /**
     * 获取服务节点路由信息
     *
     * @param string $serviceName
     * @param string $path
     * @return array
     */
    public function route(string $serviceName, string $path) : array
    {
        $node = $this->getNode($serviceName);

        return $node['routes'][$path];
    }

    /**
     * @param string $serviceName
     * @return array
     */
    public function getNode(string $serviceName): array
    {
        if (!isset($this->nodes[$serviceName])) {
            $this->nodes[$serviceName] = include $this->path . '/' . $serviceName . '.php';
            $this->node = $this->setNode($this->nodes[$serviceName]);
        }

        return $this->node;
    }

    /**
     * @param array $nodes
     * @return array
     */
    protected function setNode(array $nodes)
    {
        // todo 暂时为随机数 服务状态 连接数 成功数 失败数 权重
        $count = count($nodes);

        return $nodes[mt_rand(0, $count - 1)];
    }

    /**
     * 获取节点状态
     *
     * @param string $serviceName
     * @param string $path
     * @return array
     */
    public function status(string $serviceName, string $path)
    {
        // TODO: Implement status() method.
    }

    public function __destruct()
    {
        $this->nodes = [];
        $this->node = [];
    }
}
