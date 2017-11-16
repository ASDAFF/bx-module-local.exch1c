<?php

namespace Local\Exch1c;

use \altayalp\FtpClient\Servers\FtpServer;
use altayalp\FtpClient\FileFactory;

class FtpClient
{
    /**
     * Ftp Server instance
     *
     * @access protected
     * @var FtpServer
     */
    private $_ftpServer;

    /**
     * Ftp host
     *
     * @access protected
     * @var string
     */
    private $_host;

    /**
     * Ftp login
     *
     * @access protected
     * @var string
     */
    private $_user;

    /**
     * Ftp pass
     *
     * @access protected
     * @var string
     */
    private $_pass;

    /**
     * Ftp dir
     *
     * @access protected
     * @var string
     */
    private $_dirFtp;

    /**
     * Server dir
     *
     * @access protected
     * @var string
     */
    private $_dirServer;

    /**
     * Parser instance
     *
     * @access protected
     * @var IParser
     */
    private $_parser;

    public function __construct($host, $user, $pass, $dirFtp, $dirServer, $port = 21)
    {
        $this->_host = $host;
        $this->_port = $port;
        $this->_user = $user;
        $this->_pass = $pass;
        $this->_dirFtp = trim($dirFtp, DIRECTORY_SEPARATOR);
        $this->_dirServer = trim($dirServer, DIRECTORY_SEPARATOR);

        $this->_ftpServer = new FtpServer($this->_host);
        $this->_ftpServer->login($this->_user, $this->_pass);
        $this->_ftpServer->turnPassive();
        ftp_raw($this->_ftpServer->getSession(), 'OPTS UTF8 ON');

    }

    public function setParser(IParser $parser)
    {
        $this->_parser = $parser;
    }

    /**
     * @return IParser
     */
    public function getParser()
    {
        return $this->_parser;
    }

    public function getServerDir()
    {
        return $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $this->_dirServer . DIRECTORY_SEPARATOR;
    }

    public function getFtpDir()
    {
        return $this->_dirFtp . DIRECTORY_SEPARATOR;
    }

    private function _hasServerDir()
    {
        return is_dir($this->getServerDir());
    }

    private function _createServerDir()
    {
        return mkdir($this->getServerDir(), '755');
    }

    public function syncFile()
    {
        if(!$this->_parser) {
            throw new \Exception('Определите парсер через setParser');
        }

        $fileName = $this->_parser->getFileNameImport();

        if($this->ftpFileExists($fileName)) {
            return false;
        }

        // получение файла на сервер
        if (!$this->_hasServerDir()) {
            $this->_createServerDir();
        }

        $filePathRemote = $this->_dirFtp . DIRECTORY_SEPARATOR . $fileName;
        $filePathLocal = $this->getServerDir() . $fileName;

        $fileFactory = FileFactory::build($this->_ftpServer);
        $fileFactory->download($filePathRemote, $filePathLocal);

        // получение информации из файла
        $this->_parser->setDir($this->getServerDir());
        $arData = $this->_parser->getArray();

        return $arData;
    }

    public function ftpFileExists($fileName)
    {
        $fileFactory = FileFactory::build($this->_ftpServer);
        $arFiles = $fileFactory->ls($this->_dirFtp);

        if (in_array($fileName, $arFiles)) {
            return true;
        }

        return false;
    }

    public function uploadFile()
    {
        $fileName = $this->_parser->getFileNameExport();

        if($this->ftpFileExists($fileName)) {
            return false;
        }

        $filePathRemote = $this->_dirFtp . DIRECTORY_SEPARATOR . $fileName;
        $filePathLocal = $this->getServerDir() . $fileName;

        $fileFactory = FileFactory::build($this->_ftpServer);

        return $fileFactory->upload($filePathLocal, $filePathRemote);

    }
}