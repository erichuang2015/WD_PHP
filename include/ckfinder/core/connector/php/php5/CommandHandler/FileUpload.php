<?php
/**
 * CKFinder
 * ========
 * http://cksource.com/ckfinder
 * Copyright (C) 2007-2015, CKSource - Frederico Knabben. All rights reserved.
 *
 * The software, this file and its contents are subject to the CKFinder
 * License. Please read the license.txt file before using, installing, copying,
 * modifying or distribute this file or part of its contents. The contents of
 * this file is part of the Source Code of CKFinder.
 */
if (!defined('IN_CKFINDER')) exit;

/**
 * @package CKFinder
 * @subpackage CommandHandlers
 * @copyright CKSource - Frederico Knabben
 */

/**
 * Handle FileUpload command
 *
 * @package CKFinder
 * @subpackage CommandHandlers
 * @copyright CKSource - Frederico Knabben
 */
class CKFinder_Connector_CommandHandler_FileUpload extends CKFinder_Connector_CommandHandler_CommandHandlerBase
{
    /**
     * Command name
     *
     * @access protected
     * @var string
     */
    protected $command = "FileUpload";

    /**
     * send response (save uploaded file, resize if required)
     * @access public
     *
     */
    public function sendResponse()
    {
		global $_SESSION;
		
		$dirtemp_name =  explode('ckfinder',dirname(__FILE__));
		$dirtemp_name = $dirtemp_name[0];
		$ini_webset = parse_ini_file($dirtemp_name."includes/config/web_set.ini",true);
		
        $iErrorNumber = CKFINDER_CONNECTOR_ERROR_NONE;

        $_config =& CKFinder_Connector_Core_Factory::getInstance("Core_Config");
        $oRegistry =& CKFinder_Connector_Core_Factory::getInstance("Core_Registry");
        $oRegistry->set("FileUpload_fileName", "unknown file");

        $uploadedFile = array_shift($_FILES);

        if (!isset($uploadedFile['name'])) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_INVALID);
        }

        $sUnsafeFileName = CKFinder_Connector_Utils_FileSystem::convertToFilesystemEncoding(CKFinder_Connector_Utils_Misc::mbBasename($uploadedFile['name']));
		//-隨機檔案命名
		//--判斷參數是否重新命名
		if ($ini_webset["web_set"]["uploadfile_rename"]==='0'){
		}else{
			$sExtension = CKFinder_Connector_Utils_FileSystem::getExtension($sUnsafeFileName);
			$sUnsafeFileName=date('YmdHis').'.'.$sExtension;
		}
        $sFileName = CKFinder_Connector_Utils_FileSystem::secureFileName($sUnsafeFileName);

        if ($sFileName != $sUnsafeFileName) {
          $iErrorNumber = CKFINDER_CONNECTOR_ERROR_UPLOADED_INVALID_NAME_RENAMED;
        }
        $oRegistry->set("FileUpload_fileName", $sFileName);

        $this->checkConnector();
        $this->checkRequest();

        if (!$this->_currentFolder->checkAcl(CKFINDER_CONNECTOR_ACL_FILE_UPLOAD)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UNAUTHORIZED);
        }

        $_resourceTypeConfig = $this->_currentFolder->getResourceTypeConfig();
        if (!CKFinder_Connector_Utils_FileSystem::checkFileName($sFileName) || $_resourceTypeConfig->checkIsHiddenFile($sFileName)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_NAME);
        }

        $resourceTypeInfo = $this->_currentFolder->getResourceTypeConfig();
        if (!$resourceTypeInfo->checkExtension($sFileName)) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_INVALID_EXTENSION);
        }

        $oRegistry->set("FileUpload_fileName", $sFileName);
        $oRegistry->set("FileUpload_url", $this->_currentFolder->getUrl());

        $maxSize = $resourceTypeInfo->getMaxSize();
        if (!$_config->checkSizeAfterScaling() && $maxSize && $uploadedFile['size']>$maxSize) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_TOO_BIG);
        }

        $htmlExtensions = $_config->getHtmlExtensions();
        $sExtension = CKFinder_Connector_Utils_FileSystem::getExtension($sFileName);

        if ($htmlExtensions
        && !CKFinder_Connector_Utils_Misc::inArrayCaseInsensitive($sExtension, $htmlExtensions)
        && ($detectHtml = CKFinder_Connector_Utils_FileSystem::detectHtml($uploadedFile['tmp_name'])) === true ) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_WRONG_HTML_FILE);
        }

        $secureImageUploads = $_config->getSecureImageUploads();
        if ($secureImageUploads
        && ($isImageValid = CKFinder_Connector_Utils_FileSystem::isImageValid($uploadedFile['tmp_name'], $sExtension)) === false ) {
            $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_CORRUPT);
        }

        switch ($uploadedFile['error']) {
            case UPLOAD_ERR_OK:
                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_TOO_BIG);
                break;

            case UPLOAD_ERR_PARTIAL:
            case UPLOAD_ERR_NO_FILE:
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_CORRUPT);
                break;

            case UPLOAD_ERR_NO_TMP_DIR:
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_NO_TMP_DIR);
                break;

            case UPLOAD_ERR_CANT_WRITE:
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
                break;

            case UPLOAD_ERR_EXTENSION:
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED);
                break;
        }

        $sServerDir = $this->_currentFolder->getServerPath();

        while (true)
        {
            $sFilePath = CKFinder_Connector_Utils_FileSystem::combinePaths($sServerDir, $sFileName);

            if (file_exists($sFilePath)) {
                $sFileName = CKFinder_Connector_Utils_FileSystem::autoRename($sServerDir, $sFileName);
                $oRegistry->set("FileUpload_fileName", $sFileName);

                $iErrorNumber = CKFINDER_CONNECTOR_ERROR_UPLOADED_FILE_RENAMED;
            } else {
                if (false === move_uploaded_file($uploadedFile['tmp_name'], $sFilePath)) {
                    $iErrorNumber = CKFINDER_CONNECTOR_ERROR_ACCESS_DENIED;
                }
                else {
                    if (isset($detectHtml) && $detectHtml === -1 && CKFinder_Connector_Utils_FileSystem::detectHtml($sFilePath) === true) {
                        @unlink($sFilePath);
                        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_WRONG_HTML_FILE);
                    }
                    else if (isset($isImageValid) && $isImageValid === -1 && CKFinder_Connector_Utils_FileSystem::isImageValid($sFilePath, $sExtension) === false) {
                        @unlink($sFilePath);
                        $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_CORRUPT);
                    }
                }
                if (is_file($sFilePath) && ($perms = $_config->getChmodFiles())) {
                    $oldumask = umask(0);
                    chmod($sFilePath, $perms);
                    umask($oldumask);
                }
                break;
            }
        }

        if (!$_config->checkSizeAfterScaling()) {
            $this->_errorHandler->throwError($iErrorNumber, true, false);
        }

        //resize image if required
        require_once CKFINDER_CONNECTOR_LIB_DIR . "/CommandHandler/Thumbnail.php";
        $_imagesConfig = $_config->getImagesConfig();

        if ($_imagesConfig->getMaxWidth()>0 && $_imagesConfig->getMaxHeight()>0 && $_imagesConfig->getQuality()>0) {
            CKFinder_Connector_CommandHandler_Thumbnail::createThumb($sFilePath, $sFilePath, $_imagesConfig->getMaxWidth(), $_imagesConfig->getMaxHeight(), $_imagesConfig->getQuality(), true) ;
        }

        if ($_config->checkSizeAfterScaling()) {
            //check file size after scaling, attempt to delete if too big
            clearstatcache();
            if ($maxSize && filesize($sFilePath)>$maxSize) {
                @unlink($sFilePath);
                $this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_UPLOADED_TOO_BIG);
            }
            else {
                $this->_errorHandler->throwError($iErrorNumber, true, false);
            }
        }

		
		//* 用以判斷 檔案上傳是否超過限制大小 add by Jones*/
		if (
		isset($ini_webset["web_set"]["upload_max_size"]) && isset($ini_webset["web_set"]["now_file"]) &&
		$ini_webset["web_set"]["upload_max_size"]*1<$ini_webset["web_set"]["now_file"]*1+filesize($sFilePath)*1
		) {
            clearstatcache();
			@unlink($sFilePath);
			$this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_WD_DESK_FULL);
        }
		
		/* 後台驗證登入 */
		/*
		if (!isset($_SESSION["admin_info"]["id"])){
            clearstatcache();
			@unlink($sFilePath);
			$this->_errorHandler->throwError(CKFINDER_CONNECTOR_ERROR_WD_TOKEN);
		}
		*/
		
		$check_file = explode('.',$sFilePath);
		$check_file = $check_file[count($check_file)-1];
		if (strtolower($check_file)=='jpeg' || strtolower($check_file)=='jpg') $this->jpeg_jwork($sFilePath);
		if (strtolower($check_file)=='png') $this->png_jwork($sFilePath);
        CKFinder_Connector_Core_Hooks::run('AfterFileUpload', array(&$this->_currentFolder, &$uploadedFile, &$sFilePath));
    }
	
	//-----漸進式存取圖片 jepg
	public function jpeg_jwork($file_name){
		$im = imagecreatefromjpeg($file_name);
		imageinterlace($im, 1);
		imagejpeg($im, $file_name, 100);
		imagedestroy($im); 
	}
	//-----交錯式存取圖片 png
	public function png_jwork($file_name){
		$im = @imagecreatefrompng($file_name);
		$srcWidth = imagesx($im);
		$srcHeight = imagesy($im);
	
		$newWidth = $srcWidth;
		$newHeight = $srcHeight;
		$newImg = imagecreatetruecolor($newWidth, $newHeight);
		
		$alpha = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
		imagefill($newImg, 0, 0, $alpha);
		
		imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
		imagesavealpha($newImg, true);
		
		imageinterlace($newImg, 1);
		imagepng($newImg, $file_name);
		imagedestroy($newImg); 
	}
	public function gif_jwork($file_name){
		$im = @imagecreatefromgif($file_name);
		$srcWidth = imagesx($im);
		$srcHeight = imagesy($im);
	
		$newWidth = $srcWidth;
		$newHeight = $srcHeight;
		$newImg = imagecreatetruecolor($newWidth, $newHeight);
		
		$alpha = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
		imagefill($newImg, 0, 0, $alpha);
		
		imagecopyresampled($newImg, $im, 0, 0, 0, 0, $newWidth, $newHeight, $srcWidth, $srcHeight);
		imagesavealpha($newImg, true);
		
		imageinterlace($newImg, 1);
		imagegif($newImg, $file_name);
		imagedestroy($newImg);  
	}
}
