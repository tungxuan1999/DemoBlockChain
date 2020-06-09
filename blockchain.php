<?php
require_once("./block.php");

/**
 * A simple blockchain class with proof-of-work (mining).
 */
class BlockChain
{
    protected $url = "https://emergingtechnologies-4e146.firebaseio.com/account/";
    private function pushData($name,$block)
    {
        $curl = curl_init();
        $ar = $block->data;
        if($block->previousHash==null)
        {
            $previousHash = "null";
        }
        else{
            $previousHash = $block->previousHash;
        }
        $data = array(
            CURLOPT_URL => $this->url."account/$name/.json",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "PATCH",
            CURLOPT_POSTFIELDS =>"{\r\n    \"$block->index\": {\r\n        \"nonce\": $block->nonce,\r\n        \"index\": $block->index,\r\n        \"timestamp\": $block->timestamp,\r\n        \"data\": {\r\n            \"amount\": \"".$ar['amount']."\",\r\n            \"result\": \"".$ar['result']."\"\r\n        },\r\n        \"previousHash\": \"$previousHash\"\r\n,\r\n        \"hash\": \"$block->hash\"\r\n    }\r\n}",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        );
        curl_setopt_array($curl, $data);

        $response = curl_exec($curl);

        curl_close($curl);
    }
    /**
     * Instantiates a new Blockchain.
     */
    public function __construct($name)
    {
        $this->chain = [$this->createGenesisBlock($name)];
        $this->difficulty = 4;
    }

    /**
     * Creates the genesis block.
     */
    private function createGenesisBlock($name)
    {
        date_default_timezone_set("Asia/Ho_chi_minh");
        $data = array("amount"=>"0","result"=>"Create Account");
        $time = date("H:i:s d-m-Y");
        $b = new Block(0, strtotime($time), $data);
        $this->pushData($name,$b);
        return $b;
    }

    /**
     * Gets the last block of the chain.
     */
    public function getLastBlock()
    {
        return $this->chain[count($this->chain)-1];
    }

    /**
     * Pushes a new block onto the chain.
     */
    public function push($name,$block)
    {
        $block->previousHash = $this->getLastBlock()->hash;
        $this->mine($block);
        $this->pushData($name,$block);
        array_push($this->chain, $block);
    }

    /**
     * Mines a block.
     */
    public function mine($block)
    {
        while (substr($block->hash, 0, $this->difficulty) !== str_repeat("0", $this->difficulty)) {
            $block->nonce++;
            $block->hash = $block->calculateHash();
        }

        // echo "Block mined: ".$block->hash."\n";
    }

    /**
     * Validates the blockchain's integrity. True if the blockchain is valid, false otherwise.
     */
    public function isValid()
    {
        for ($i = 1; $i < count($this->chain); $i++) {
            $currentBlock = $this->chain[$i];
            $previousBlock = $this->chain[$i-1];

            if ($currentBlock->hash != $currentBlock->calculateHash()) {
                return false;
            }

            if ($currentBlock->previousHash != $previousBlock->hash) {
                return false;
            }
        }

        return true;
    }
}
