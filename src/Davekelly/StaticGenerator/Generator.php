<?php

namespace Davekelly\StaticGenerator;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

//
// Create a static html file version of a given route
// 
class Generator {

    /**
     * Filesystem  
     * @var Illuminate\Filesystem\Filesystem
     */
    protected $fs;

    /**
     * Directory location inside /public to store the files
     * @var string
     */
    protected $fileLocation = '/static';



    public function __construct()
    {
        $this->fs = new Filesystem();
    }


    /**
     * Direct the creation of the static file
     * 
     * @param  String $route 
     * @return [type]        [description]
     */
    public function createStatic($route)
    {

        $client = new Client();
        
        try{
            
            $response = $client->get( url( $route ));

        }catch(RequestException $e){
            echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                echo $e->getResponse() . "\n";
            }
            die();
        }catch(ClientErrorResponseException $e){
            $responseBody = $e->getResponse()->getBody(true);
            dd($responseBody);
        }catch(Exception $e){
            echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                echo $e->getResponse()->getReasonPhrase() . "\n";
            }
            die();
        }
        
        if($response->getStatusCode() == 200){
            return $this->save($response, $route);
        }else{
            dd('Route was not found or is not accessible');
        }
        
    }

    /**
     * Does this route exist?
     * 
     * @param  string $route           [description]
     * @param  collection $routeCollection 
     * @return boolean
     */
    public function checkRouteIsValid($route, $routeCollection)
    {
        foreach($routeCollection as $value){
            if($value->getPath() == $route && 
                    $value->getPath() !== 'generate')
            {
                return true;
            }
        }

        return false;
    }


    /**
     * [save description]
     * @param  [type] $html  [description]
     * @param  [type] $route [description]
     * @return [type]        [description]
     */
    public function save($response, $route)
    {
        
        $staticPath     = $this->getStaticPathLocation();
        $path           = $this->createPathFromRoute($route, $staticPath);
        
        $filename   = $this->getFilename($response);
        $saved      = $this->fs->put($path . '/'. $filename, $response->getBody());
        
        if(is_numeric($saved)){
            return $path . '/' . $filename;
        }

        return $saved;

    }

    /**
     * Where in the /public directory should the files
     * be stored?
     * 
     * @return string $path
     */
    public function getStaticPathLocation()
    {
        $path = public_path() . $this->fileLocation;

        if(!$this->fs->isWritable($path)){
            throw new PathNotWritableException;
        }

        if(!$this->fs->isDirectory( $path )){
            $this->fs->makeDirectory($path);
        }

        return $path;
    }


    /**
     * Check the path for this route exists. If it doesn't,
     * create it 
     * 
     * @param  string $route 
     * @param  string $path  
     * @return string $fullPath
     */
    public function createPathFromRoute($route, $path)
    {
         if($route !== '/'){

            $path = $path . '/'. $route ;

            if(!$this->fs->isDirectory( $path )){
                $this->fs->makeDirectory($path, $mode = 0755, $recursive = true);
            }

        }

        return $path;
    }

    /**
     * Return the correct filename/extension (will be .html
     * if there's no json header set)
     * 
     * @param  Illuminate\Http\Response
     * @return string
     */
    public function getFilename($response)
    {
        $extension = '.html';
        if($response->getHeader('Content-Type') == 'application/json'){
            $extension = '.json';
        }

        return 'index' . $extension;
    }

}