# HSR Map
A simple Hamilton Street Railway (HSR) live transit mobile experience. See it in action [hsrmap.ca](http://hsrmap.ca/).

## Author
[Steve Veerman](http://steve.veerman.ca/)

## About
hsrmap.ca is a mobile web app which displays the location and routes of all HSR buses in near real-time.

## Background

I tend to catch the bus by walking to the nearest stop and waiting. This app will provide the ability to gauge whether there is time to grab a coffee or if you need to stay put because the bus is just around the corner. For more information on the creation of hsrmap, visit this [Software Hamilton article](http://www.softwarehamilton.com/2015/04/17/interview-with-steve-veerman-about-hsrmap/)

## API
See the API_README.md

## Requirements
* PHP with composer

## Setup
* In web root, run: composer install

## Notes
* Live data is not actually live. It is about 45-75 seconds behind, so adjust accordingly.
* If you click on the bus marker, you will see the route drawn.
