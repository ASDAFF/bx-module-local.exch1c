<?php

namespace Local\Exch1c;

use \altayalp\FtpClient\Servers\FtpServer;

class FtpClient
{
    private $_filePath = '';
    private $_ftpServer;
    private $_host;
    private $_user;
    private $_pass;

    public function __construct($host, $user, $pass) {
        $this->_host = $host;
        $this->_user = $user;
        $this->_pass = $pass;

        $this->_ftpServer = new FtpServer($this->_host);
    }

    public function test() {
        $this->_ftpServer->login($this->_user, $this->_pass);
        return 'ok';
    }


}