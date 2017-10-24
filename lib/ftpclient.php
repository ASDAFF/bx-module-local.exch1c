<?php

namespace Local\Exch1c;

use \altayalp\FtpClient\Servers\FtpServer;
use altayalp\FtpClient\FileFactory;

class FtpClient
{
    private $_filePath = '';
    private $_ftpServer;
    private $_host;
    private $_port;
    private $_user;
    private $_pass;

    public function __construct($host, $user, $pass, $port = 21) {
        $this->_host = $host;
        $this->_port = $port;
        $this->_user = $user;
        $this->_pass = $pass;

        $this->_ftpServer = new FtpServer($this->_host, $this->_port);
    }

    public function test() {
        $this->_ftpServer->login($this->_user, $this->_pass);
        $file = FileFactory::build($this->_ftpServer);
        $list = $file->ls('.');
        print_r($list);
        return 'ok';
    }


}