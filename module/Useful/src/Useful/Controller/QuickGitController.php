<?php
namespace Useful\Controller;

class QuickGitController
{

    private $version;

    function __construct()
    {
        try {
            exec('git describe --always', $version_mini_hash);
            exec('git rev-list HEAD | wc -l', $version_number);
            $version = isset($version_number[0]) ? $version_number[0] : 'Err';
            $hash = isset($version_mini_hash[0]) ? $version_mini_hash[0] : 'Err';
            $this->version['short'] = "v1." . trim($version) . "." . $hash;
        } catch (\Exception $e) {
            $this->version['short'] = "v1.Err";
        }
    }

    public function short()
    {
        return $this->version['short'];
    }

    public function output()
    {
        return $this->version;
    }

    public function show()
    {
        echo $this->version;
    }
}