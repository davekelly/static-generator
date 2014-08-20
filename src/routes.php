<?php
use Illuminate\Support\Facades\View;
use Davekelly\StaticGenerator\Generator;


// Display UI for selecting the static files to generate
Route::get('/generate/', function(){
    
    $completed = null;    

    // what routes are available?
    $routeCollection = Route::getRoutes();
    
    // do we have a route to static-ify
    $route     = Input::get('route');
    
    $generate  = new Generator();

    if($route){
        // go. go. go.
        $generate  = new Generator();
        $valid = $generate->checkRouteIsValid($route, $routeCollection);
    
        if($valid){        
            $completed = $generate->createStatic($route);
        }else{
            Session::flash('flash_notice', array(
                    'type' => 'danger', 
                    'message' => 'Sorry, that route was not found'
                )
            );
        }
    }
    
    return View::make('static-generator::generate-static', array(
            'routeCollection'   => $routeCollection,
            'generated'         => $completed,
        )
    );

});