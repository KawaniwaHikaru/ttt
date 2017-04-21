<?php

namespace TicTacToe;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Http\Response;
// use Phalcon\Mvc\Model\Validator\Uniqueness;
// use Phalcon\Mvc\Model\Validator\InclusionIn;

class Games extends Model {

    /**
     *
     * @var integer
     */
    protected $id;

   /**
     *
     * @var string
     */
   protected $board;

   protected $status;
   protected $turn;

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setBoard($b) {
        $this->board = $b;
    }

    public function afterFetch()
    {
        $this->id = (int) $this->id;
    }

    public function prettyPrint() {

        if ($this->status == "DRAW")
            $title = "DRAW";
        else if ($this->status == "WIN_X")
            $title = "X is the Winner";
        else if ($this->status == "WIN_O")
            $title = "O is the Winner";
        else
            $title = sprintf("Game %d, %s's turn ", $this->id, $this->turn);
        

        return sprintf("%s\n%s|%s|%s\n-+-+-\n%s|%s|%s\n-+-+-\n%s|%s|%s\n",
            $title,
            $this->board[0],$this->board[1],$this->board[2],
            $this->board[3],$this->board[4],$this->board[5],
            $this->board[6],$this->board[7],$this->board[8]
            );

    }



    public function move($type, $where) {

        $response = new Response();
        // placing the same type again
        if ($this->status != "PROGRESS") {
            $response->setStatusCode(406, "game not running");
            return $response;
        }

        // outside of the board
        if ($where < 0 || $where > 8) {

            $response->setStatusCode(406, "Position out of bound");
            return $response;

        }

        // placing the same type again
        if ($type != $this->turn) {
            $response->setStatusCode(406, "Not your turn");
            return $response;
        }


        // placing the same type again
        if ($this->board[$where] != " ") {
            $response->setStatusCode(406, "Not Empty Space");
            return $response;
        }

        $this->board[$where] = $type;

        // check if it's a draw
        if ($this->isDraw()) {
            $this->status = "DRAW";
        } else if ($this->isWin('X')) {
            $this->status = "WIN_X";
        } else if ($this->isWin('O')) {
            $this->status = "WIN_O";
        }

        // print_r($this->status);

        // switch turn
        if ($this->turn == "O")
            $this->turn = "X";
        else
            $this->turn = "O";


        $this->save();
        $this->refresh();

        $response->setStatusCode(200);
        $response->setContent($this->prettyPrint());

        return $response;
    }

    private function isDraw() {

        $count = 9;

        // if number of spaces or none xo 
        // is zero, it's a draw
        for ($i = 0; $i < 9; $i++) {
            if ($this->board[$i] != " ") 
                $count --;
        }

        return $count == 0;
    }

    private function isWin($type) {

        $patterns = [
        [0,1,2],
        [0,3,6],
        [0,4,8],
        [1,4,7],
        [2,5,8],
        [3,4,5],
        [6,7,8],
        [2,4,6]
        ];

        // check each of the pattern
        for ($i = 0; $i < sizeof($patterns); $i++) {
            $p1 = $patterns[$i][0];
            $p2 = $patterns[$i][1];
            $p3 = $patterns[$i][2];

            if ($this->board[$p1] == $type &&
                $this->board[$p2] == $type &&
                $this->board[$p3] == $type)

                return true;
        }

        return false;
    }
}