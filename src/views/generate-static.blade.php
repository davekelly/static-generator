<!doctype html>
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if IE 9]>    <html class="no-js ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title></title>

    <meta name="author" content="Dave Kelly" />
    <meta name="description" content="" />

    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">        

    
</head>
<body class="hfeed">         


    
<div class="page">         
    
    <div id="page-container" class="container">
        
        <div id="main">
            @if(Session::has('flash_notice'))
              <?php
               
                $flash = Session::get('flash_notice'); 
                if(!isset($flash['type'])){
                  $flash['type'] = '';
                }

                ?>
                <div class="row">
                  <div class="col-xs-12" style="margin: 1em 0;">                    
                      <div class="alert alert-{{{ $flash['type'] }}}">>
                          <?php // <a class="close" data-dismiss="alert">×</a>  ?>
                          {{{ $flash['message'] }}}
                      </div>
                  </div>
                </div>
            @endif
            

            <div class="row">
                <div class="col-xs-12">
                    <h1>Generate static file versions</h1>

                    <p class="help-block">
                        Create <code>.html</code> or <code>.json</code> versions of the routes listed below. 
                    </p>
                    <?php if($generated): ?>
                        
                        <div class="alert alert-success">
                            <h4>Static file created.</h4>
                            <p>
                                 You can find it at: <code>/public/static/{{ $generated }}</code>
                            </p>
                        </div>

                    <?php endif; ?>


                   <table class="table table-condensed">
                        <thead>
                            <tr>
                                <th>HTTP Method</th>
                                <th>Route</th>
                                <?php // <th>Corresponding Action</th> ?>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($routeCollection as $value)
                                
                                <?php if($value->getMethods()[0] === 'GET' && 
                                                    $value->getPath() !== 'generate'): ?>

                                    <tr>
                                        <td class="gen-method">{{ $value->getMethods()[0] }}</td>
                                        <td class="gen-path">{{ $value->getPath() }}</td>
                                        <?php /* <td>{{ $value->getActionName() }}</td> */ ?>
                                        <td>
                                            <?php  // $path = ($value->getPath() == '/') ? '/' : '/' . $value->getPath();  ?>

                                                <a href="/generate?route={{ $value->getPath() }}" class="btn btn-primary">
                                                    Generate Static File
                                                </a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div> <!-- #main -->
    </div>

</div>
  
  
</body>
</html>     