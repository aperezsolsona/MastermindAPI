# MastermindAPI
Rest API to simulate a Mastermind game

## Installation

This was developed with PHP 7.2+, Symfony4 using 
- FOSRestBundle and some other bundles to support the construction of the API part.
- Doctrine ORM as persistence layer, tested with MySQL
- Symfony webserver bundle for dev purposes


Use composer to set up the project, then proceed to configure the .env file to create a Database and create schema with needed tables.
 
## Usage

Once installed and with a server running, go to 
>your.webserver/api/doc

for full API documentation and response codes.

### Create game
>your.webserver/api/v1/board **POST**

Creates a new game. 

##### Parameters
- **code**: optional

You can send the POST request as is, without parameters, to get a randomly generated code, or send the code yourself in the request body.

ex. code: ["B", "R", "O", "B"] 

### Place guess
>your.webserver/api/v1/guess **POST**
##### Parameters
- **board_id**: (required)

The board id given when a game was created.
- **pegs**: (required)

Your guess code in JSON format as follows

ex. code: ["B", "R", "O", "B"] 
### Check historic
>/api/v1/board/{id} **GET**

Outputs the list of guesses played against the board represented by {id}

## Design considerations

- This code has been left in verbose mode, i.e. if this was for a real game API, the output would not show the board's code, it would remain secret.
- REST api has been developed only with JSON as available transfer encoding.
- Output and design has been kept as minimal as possible for time optimization.
- No game restrictions have been put in place, such as blocking new guesses to already solved boards, or maximum number of guesses.
- Although only one version has been developed, uri versioning has been put in place.
- Manually setting board code option has been included in order to allow further testing.
