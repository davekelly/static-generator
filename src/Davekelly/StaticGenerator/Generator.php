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
        $fs = new Filesystem();

        $path = public_path() . '/static';

        if(!$fs->isDirectory( $path )){
            $fs->makeDirectory($path);
        }

        $filename   = $this->getFilename($response, $route);
        $saved      = $fs->put($path . '/'. $filename, $response->getBody());

        if(is_numeric($saved)){
            return $filename;
        }

        return $saved;

    }

    public function getFilename($response, $route)
    {
        $extension = '.html';
        if($response->getHeader('Content-Type') == 'application/json'){
            $extension = '.json';
        }

        if($route !== '/'){
            $filename = Str::slug( str_replace('/', ' ', $route));

            return $filename . $extension;
        }

        return 'index' . $extension;
    }
}