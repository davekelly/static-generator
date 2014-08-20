<?php
use Davekelly\StaticGenerator\Generator;

use GuzzleHttp\Client;
use GuzzleHttp\Subscriber\Mock;
use GuzzleHttp\Message\Response;



class StaticGenerator extends Illuminate\Foundation\Testing\TestCase {

    protected $generator;


    /**
     * Creates the application.
     *
     * @return \Symfony\Component\HttpKernel\HttpKernelInterface
     */
    public function createApplication()
    {
        $unitTesting = true;

        $testEnvironment = 'testing';

        return require __DIR__.'/../../../../bootstrap/start.php';
    }




    public function setUp(){
       parent::SetUp();
       $this->generator = new Generator();
    }


    // 
    // can we tell if valid routes exist
    public function testIsValidRoute()
    {
        
        $vaildRoute = '/';
        $routeCollection = Route::getRoutes();

        $isValidRoute = $this->generator->checkRouteIsValid($vaildRoute, $routeCollection);
        
        $this->assertTrue( $isValidRoute );
    }

    // can we tell if invalid routes exist
    public function testIsInValidRoute()
    {
        
        $invalidRoute = '/oh/hai';
        $routeCollection = Route::getRoutes();

        $isValidRoute = $this->generator->checkRouteIsValid($invalidRoute, $routeCollection);
        
        $this->assertFalse( $isValidRoute );
    }

    // the /generate route can't generate a static version of itself
    // ...because why would it.
    public function testIsInValidGenerateRoute()
    {
        $invalidRoute = 'generate';
        $routeCollection = Route::getRoutes();

        $isValidRoute = $this->generator->checkRouteIsValid($invalidRoute, $routeCollection);
        
        $this->assertFalse( $isValidRoute );
    }


    // can we save a file for the "/" route 
    public function testSaveRouteIndex()
    {
        $client = new Client();

    // Create a mock subscriber and queue two responses.
        $mock = new Mock([
            new Response(200, ['X-Foo' => 'Bar'])
        ]);

        // Add the mock subscriber to the client.
        $client->getEmitter()->attach($mock);
        // The first request is intercepted with the first response.
        $response = $client->get('/');
    
        $savedFileName = $this->generator->save($response, '/');

        $this->assertEquals($savedFileName, 'index.html');

    }  


    // is there a .json extension on filenames for responses with the 
    // application/json content-type header
    public function testJsonFilename()
    {
        $client = new Client();

    // Create a mock subscriber and queue two responses.
        $mock = new Mock([
            new Response(200, ['X-Foo' => 'Bar', 'Content-Type' => 'application/json']),         // Use response object
            
        ]);

        // Add the mock subscriber to the client.
        $client->getEmitter()->attach($mock);
        $response = $client->get('/');

    
        $fileName = $this->generator->getFilename($response, '/');

        $this->assertEquals($fileName, 'index.json');

    }    


    // does the UI load?
    public function testLoadGenerateUi()
    {
        $crawler = $this->client->request('GET', '/generate');

        $this->assertTrue($this->client->getResponse()->isOk());
    }

    // Does it look like the correct view?
    public function testCorrectView()
    {
        $crawler = $this->client->request('GET', '/generate');

        $count = count($crawler->filter('h1:contains("Generate static file versions")'));
        $this->assertGreaterThan(0, $count);
    }

    // we don't want an option to generate '/generate' => 
    // shouldn't be present in the UI table
    public function testUiHasNoGenerateItselfOption()
    {
        $crawler = $this->client->request('GET', '/generate');

        $count = count($crawler->filter('td.gen-path:contains("generate")'));
        $this->assertEquals(0, $count);
    }


}
