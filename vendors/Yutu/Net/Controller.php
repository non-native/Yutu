<?php
/**
 * Controller.php.
 * User: Hodge.Yuan@hotmail.com
 * Date: 2019/5/12 0012
 * Time: 10:58
 */

namespace Yutu\Net;


use Yutu\Type\CoroutineExitException;

class Controller
{
    use Request;
    use Response;

    /**
     * @var array
     */
    protected $get = [];

    /**
     * @var array
     */
    protected $post = [];

    /**
     * Controller constructor.
     * @param \Swoole\Http\Request $request
     * @param \Swoole\Http\Response $response
     * @throws CoroutineExitException
     */
    public function __construct(\Swoole\Http\Request &$request, \Swoole\Http\Response &$response)
    {
        $this->request  = $request;
        $this->response = $response;

        // CORS option
        if ($this->request->server['request_method'] == "OPTIONS") {
            $this->echoCORS(); $this->goExit();
        }

        // application/json
        if ($this->request->header['content-type'] == "application/json") {
            $this->request->post = json_decode($this->request->rawContent(), true);
        }

        $this->get  = $this->request->get;
        $this->post = $this->request->post;

        $this->SetHeader("server", "Yutu");
    }

    /**
     * @param string $domain
     */
    protected function echoCORS($domain = "")
    {
        if (!empty($domain)) {
            $this->SetHeader("Access-Control-Allow-Headers", "Content-Type");
        }

        $this->SetHeader("Access-Control-Allow-Origin", $domain ? $domain : "*");
        $this->SetHeader("Access-Control-Allow-Credentials", "true");
    }

    /**
     * @param string $msg
     * @throws CoroutineExitException
     */
    protected function goExit($msg = "")
    {
        throw new CoroutineExitException($msg);
    }

}