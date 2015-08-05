<?php

Class View {
	private $params = array();
    private $js = ''
    private $css = '';

    public function __construct() {
		$this->addJS(array(
			'https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js',
			'/static/scripts/base.js',
		));
		
		$this->addCSS(array(
			'/static/styles/base.css',
		));
    }

    public function addParams($params) {
        $this->params = $params + $this->params;
        return $this;
    }

    public function addJS($urls) {
		if (!is_array($urls)) {
			$urls = array($urls);
		}
		foreach($urls as $url) {
			$this->js .= '<script type="text/javascript" src="' . $url . '"></script>' . "\n";
		}
        return $this;
    }

    public function addCSS($urls) {
		if (!is_array($urls)) {
			$urls = array($urls);
		}
		foreach($urls as $url) {
			$this->css .= '<link rel="stylesheet" href="' . $url . '" />' . "\n";
		}
        return $this;
    }

    public function render($file) {
        $this->addParams(array(
            'js' => $this->js,
            'css' => $this->css
        ));
        extract($this->params);
        require BASE_PATH . '/views/' . $file . '.php';
    }
}