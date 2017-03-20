<?php

namespace ViewsBuilder;

abstract class AbstractView
{
    /**
     * @var array|mixed
     */
    protected $data;

    /**
     * @return mixed
     */
    abstract protected function getPathRoot();

    /**
     * @return mixed
     */
    abstract protected function getTemplateExtension();

    /**
     * @param $view
     * @return string
     */
    abstract protected function translateView($view);

    /**
     * AbstractView constructor.
     * @param null $data
     */
    public function __construct($data = null)
    {
        $this->data = $this->prepareData($data);
    }

    /**
     * @param $view
     * @param null $data
     * @return string
     */
    protected function render($view, $data = null)
    {
        return $this->createView($this->translateView($view), $data);
    }

    /**
     * @param $data
     * @param bool $asArray
     * @return array|mixed
     */
    protected function prepareData($data, $asArray = false)
    {
        if (!is_string($data)) {
            return [];
        }

        return json_decode($data, $asArray);

    }

    /**
     * @param $view
     * @param $data
     * @return string
     */
    private function createView($view, $data)
    {
        if ($data) {
            if (is_object($data)) {
                $data = (array)$data;
            }
            extract($data);
        }

        ob_start();
        include $this->getPathRoot() . '/' . $view . $this->getTemplateExtension();
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if(!method_exists($this, $name)){
            throw new \Exception("Method $name doesnt exist.");
        }
        return call_user_func_array([$this, $name], $arguments);
    }
}