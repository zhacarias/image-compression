<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use URL;
use Alchemy\Zippy\Zippy;
use Katzgrau\KLogger\Logger;
use Psr\Log\LogLevel;

class ImageController extends Controller
{
    private $valid_ext = array('jpg', 'jpeg', 'png', 'gif');

    private $base_path;
    
    private $api_key = env('API_KEY');

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->base_path = dirname(dirname(dirname(__DIR__)));
    }
    
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {

            $file_data = $request->file('file');
            $ext = $file_data->getClientOriginalExtension();

            if (in_array($ext, $this->valid_ext)) {
                $filesize = $file_data->getClientSize();
                $maxsize = $file_data->getMaxFilesize();
                if ($filesize <= $maxsize) {
                    $destination = $this->base_path . '\public';
                    $filename =  $file_data->getClientOriginalName();
                    $result = $file_data->move($destination . '\images\tmp', $filename);
                    
                    if (file_exists($result)) {
                        $this->appendLog('Upload file: ' . $filename); // Append Log
                        $compress = $this->compressImage($result, $filename, $destination);
                        return $compress;
                        //return 'Uploaded';
                    }   
                }
                return 'Maximum file exceed';
            }
            return 'Image is not a valid extension';

        }
        return 'Failed: Image file exceed from php.ini config';
    }

    public function appendLog($logstring)
    {
        $logpath = $this->base_path . '\storage\logs';
        $logger = new Logger($logpath, LogLevel::INFO, array (
            'extension' => 'log',
            'filename' => 'audit'
        ));
        $logger->info($logstring);
    }

    public function compressImage($path, $filename, $destination)
    {
        try {
            \Tinify\setKey($this->api_key);
            $source = \Tinify\fromFile($path);
            $source->toFile($destination . '\images\compress\\' . $filename);
        } catch (\Tinify\AccountException $e) {
            return 'Verify your API key and account limit';
        } catch (\Tinify\ClientException $e) {
            return 'Check your source image and request options';
        } catch (\Tinify\ServerException $e) {
            return 'Tinyfy Server Error';
        } catch (\Tinify\ConnectionException $e) {
            return 'Network Connection error occurred';
        } catch (Exception $e) {
            // Something else went wrong, unrelated to the Tinify API.
            return 'Something went wrong, ' . $e.getMessage();
        }
        $this->appendLog('Compress file: ' . $filename); // Append Log
        return 'Successfully Compress';
    }
    
    public function getCompressionQuota()
    {
        try {
            \Tinify\setKey($this->api_key);
            \Tinify\validate();
            $compressionsThisMonth = \Tinify\compressionCount();
            return $compressionsThisMonth;
        } catch (\Tinify\Exception $e) {
            return 'Validation of API key failed';
        }
    }
    
    public function index()
    {
        $quota = $this->getCompressionQuota();
        return view('index', ['quota' => $quota]);
    }

    public function removeTemporaryImage()
    {
        $files = glob($this->base_path . '\public\images\tmp\*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
                $this->appendLog('Delete tmp file: ' . $file); // Append Log
            }
        }
    }

    public function removeCompressImage()
    {
        $files = glob($this->base_path . '\public\images\compress\*'); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
                $this->appendLog('Delete compress file: ' . $file); // Append Log
            }
        }
    }

    public function download()
    {
        $compressF = $this->base_path . '\public\images\compress';
        $zippy = Zippy::load();
        $generatedname = str_random(32) . '.zip';
        $zippy->create('archive\\' . $generatedname, array(
           'compress' => $compressF
        ), true);

        $this->removeTemporaryImage(); // Remove Original Image
        $this->removeCompressImage(); // Remove Compress Image
        $this->appendLog('Downloaded file: ' . $generatedname); // Append Log

        $compressurl = URL::to('/archive') . '/' . $generatedname;
        return $compressurl;
    }

}
