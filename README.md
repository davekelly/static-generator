# Static Generator (Laravel 4.2)

Generate static HTML or JSON files from Laravel routes.

This was developed for use with small data visualisation-type applications where it is not required to deploy a full PHP app with a database. Instead, a front-end-only version with data held as JSON can be used. I have needed to use HTML-only versions of Laravel apps often enough that this package made sense.

## Setup & Usage

### Installation

- Add `'Davekelly\StaticGenerator\StaticGeneratorServiceProvider',` to the end of the `providers` array in `/app/config/app.php`
- You will need to create a writable directory at `/public/static` -  this is where the static files are placed

### Usage

Usage is through a front-end interface. The package adds a route to `/generate` where you will find this interface. 

If you generate a static version of a Route that returns a JSON response, the file will have a `.json` extension, otherwise, it will be `.html`.

- Visit `http://your.domain/generate` to see a list of Routes registered in your application
- Click the button next to the Route you'd like to generate a static file for
- If successful, the `.html` or `.json` file will be stored in `/public/static` - you'll be shown a confirmation message with the location & file-name.


## Uses
- [Guzzle](http://guzzlephp.org)
- Front-end bootstrap styling via the [MaxCDN Bootstrap CDN](http://www.bootstrapcdn.com/)

## Future To-Do's / Improvements

- Create an Artisan CLI interface
- Look at filtered routes


## Updates
v0.2 - Creates html nested directories mimicking route structure (e.g. route: `/about/contact` saved to `/static/about/contact/index.html`) - based on idea from [pulkitjalan](https://github.com/pulkitjalan/static-generator/blob/3cb252c846fc00f4b0c862813d8f2f97e7476fa0/src/Davekelly/StaticGenerator/Generator.php)
