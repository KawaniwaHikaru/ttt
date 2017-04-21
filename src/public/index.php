<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;

// Use Loader() to autoload our model
$loader = new Loader();

$loader->registerNamespaces(
	[
	"TicTacToe" => __DIR__ . "/../models/",
	"GameLogic" => __DIR__ . "/../controllers/",
	]
	);

$loader->register();

$di = new FactoryDefault();

// Set up the database service
$di->set(
	"db",
	function () {
		return new PdoMysql(
			[
			"host"     => "db",
			"username" => "root",
			"password" => "tictactoe",
			"dbname"   => "tictactoe",
			]
			);
	}
	);

// Create and bind the DI to the application
$app = new Micro($di);

$app->get(
	"/api/v1/game",
	function () use ($app) {
		$games = TicTacToe\Games::find();
		echo json_encode($games);
	}
	);


$app->post(
	"/api/v1/game",
	function () use ($app) {

		$game = new TicTacToe\Games();

        // Create a response
		$response = new Response();

		if ( $game->create() == false) {

			$response->setStatusCode(500);
			$response->setContent(json_encode($game->getMessages()));

		} else {

			$game->refresh();

			$response->setContent($game->prettyPrint());
		}


		return $response;
	});


$app->get(
	"/api/v1/game/{id:[0-9]+}",
	function ($id) use ($app) {
		$response = new Response();

		$game = TicTacToe\Games::findFirst("id = $id");	
		if ( $game == false) {
			$response->setStatusCode(404);

		}
		else {

			$response->setContent($game->prettyPrint());
		}

		return $response;

	});

$app->put(
	"/api/v1/game/{id:[0-9]+}",
	function ($id) use ($app) {
		$move = $app->request->getJsonRawBody();

		$response = new Response();
		$game = TicTacToe\Games::findFirst("id = $id");	

		if ( $game == false) {
			$response->setStatusCode(404, "Game does not exist");
		}
		else {
			$response = $game->move($move->type, $move->where);
		}

		return $response;
	});

$app->notFound(
	function () use ($app) {
		$app->response->setStatusCode(404, "Not Found");
		$app->response->sendHeaders();
		echo "This is crazy, but this page was not found!";
	});

$app->handle();