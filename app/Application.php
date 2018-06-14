<?php

namespace IP;

class Application extends \Illuminate\Foundation\Application
{
    public function publicPath()
    {
        return $this->basePath;
    }
}