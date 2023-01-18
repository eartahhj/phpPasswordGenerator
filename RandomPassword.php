<?php
/*
* @author: https://github.com/eartahhj/
* @version: 1.0.0
* @description: Generate a random string based on custom parameters like length and security level.
*/

namespace PasswordGenerator;

class RandomPassword
{
    const MIN_LENGTH=8;
    const MAX_LENGTH=32;
    const SECURITY_LEVEL_LOW=1;
    const SECURITY_LEVEL_MEDIUM=2;
    const SECURITY_LEVEL_HIGH=3;

    protected $specialCharsList=[];
    protected $charsToRemove=[];
    protected $allCharsList=[];
    protected $length;
    protected $securityLevel=1;
    protected $securityLevels=[];
    protected $messages=[];
    protected $chosenChars=[];

    public function __construct()
    {
        $this->securityLevels=[
            self::SECURITY_LEVEL_LOW=>'Low',
            self::SECURITY_LEVEL_MEDIUM=>'Medium',
            self::SECURITY_LEVEL_HIGH=>'High',
        ];
    }

    final public function setSpecialCharsList(array $charsList=[])
    {
        if ($charsList) {
            $this->specialCharsList=$charsList;
        }
    }

    final public function setCharsToRemove(array $charsList=[])
    {
        if ($charsList) {
            $this->charsToRemove=$charsList;
        }
    }

    final public function setAllCharsList(array $charsList=[])
    {
        if ($charsList) {
            $this->allCharsList=$charsList;
        }
    }

    final public function getAllCharsList()
    {
        return $this->allCharsList;
    }

    final public function setLength(int $length=12)
    {
        $this->length=$length;
    }

    final public function getLength()
    {
        return $this->length;
    }

    final public function setSecurityLevel(int $level)
    {
        $this->securityLevel=$level;
    }

    final public function setSecurityLevels(array $levels)
    {
        $this->securityLevels=$levels;
    }

    final public function getSecurityLevels()
    {
        return $this->securityLevels;
    }

    final public function getMessages()
    {
        return $this->messages;
    }

    final public function removeCharsFromList(array $charsToRemove=[])
    {
        if ($charsToRemove) {
            $this->charsToRemove=$charsToRemove;
        }
        foreach ($this->charsToRemove as $char) {
            if ($key=array_search($char, $this->allCharsList)) {
                unset($this->allCharsList[$key]);
            }
        }
    }

    final public function useDefaultSettings()
    {
        $charsList=range('a', 'z');
        if ($this->securityLevel>=self::SECURITY_LEVEL_MEDIUM) {
            $charsList=array_merge($charsList, range('A', 'Z'), range(0, 9));
        }
        if ($this->securityLevel>=self::SECURITY_LEVEL_HIGH) {
            $this->setSpecialCharsList(['!', '~', '_', '&', ')', '(', ']', '[', '@', '*', '?', '$']);
            $charsList=array_merge($charsList, $this->specialCharsList);
        }
        $this->setCharsToRemove(['o','O','0','1','l','I']);
        $this->setAllCharsList($charsList);
        $this->removeCharsFromList();
    }

    final public function returnRandomCharFromList()
    {
        $randomNumber=array_rand($this->allCharsList);
        return $this->allCharsList[$randomNumber];
    }

    final public function generateString()
    {
        if ($this->length<self::MIN_LENGTH) {
            $this->length=self::MIN_LENGTH;
            $this->messages[]='Warning: The required number of characters for the password is lesser that the minimum needed. The password will be at least ' . self::MIN_LENGTH . ' characters long.';
        }
        if ($this->length>self::MAX_LENGTH) {
            $this->length=self::MAX_LENGTH;
            $this->messages[]='Warning: The required number of characters for the password is greater that the maximum allowed. The password will be at most '.self::MAX_LENGTH. ' characters long.';
        }
        if ($this->length>count($this->allCharsList)) {
            $this->length=count($this->allCharsList);
            $this->messages[]='Warning: The required number of characters for the password is greater that the maximum intended. The password will be shorter to avoid duplicated characters.';
        }
        $string=$currentRandomChar=$lastRandomChar='';
        $this->chosenChars=$this->generateRandomCharsList();
        if($this->securityLevel>=self::SECURITY_LEVEL_HIGH and !$this->chosenCharsContainSpecialChar()) {
            $this->forceSpecialCharIntoList();
        }
        foreach($this->chosenChars as $char) {
            $string.=$char;
        }
        return $string;
    }

    final public function renderMessages()
    {
        $html='';
        if (!$this->messages) {
            return $html;
        }
        $html.='<div class="info">';
        foreach ($this->messages as $message) {
            $html.='<p>'.$message."</p>";
        }
        $html.='</div>';
        return $html;
    }

    private function generateRandomCharsList()
    {
        $chosenChars=[];
        $lastRandomChar = null;
        
        for ($i=1; $i<=$this->length; $i++) {
            $currentRandomChar=$this->returnRandomCharFromList();
            while ($currentRandomChar==$lastRandomChar or in_array($currentRandomChar, $chosenChars)) {
                $currentRandomChar=$this->returnRandomCharFromList();
            }
            $lastRandomChar=$currentRandomChar;
            $chosenChars[]=$currentRandomChar;
        }
        return $chosenChars;
    }

    private function forceSpecialCharIntoList()
    {
        $indexCharToChange=array_rand($this->chosenChars, 1);
        $indexSpecialChar=array_rand($this->specialCharsList, 1);
        $this->chosenChars[$indexCharToChange]=$this->specialCharsList[$indexSpecialChar];
        return 1;
    }

    private function chosenCharsContainSpecialChar()
    {
        foreach($this->chosenChars as $char) {
            if(in_array($char, $this->specialCharsList)) {
                return true;
            }
        }
        return false;
    }
}

$password = new RandomPassword();

$requestLength=$requestSecurityLevel='';

if (isset($_REQUEST['send'])) {
    $requestLength=(int)$_REQUEST['length'] ?? RandomPassword::MIN_LENGTH;
    $requestSecurityLevel=(int)$_REQUEST['securitylevel'] ?? RandomPassword::SECURITY_LEVEL_HIGH;
}

if (!$requestLength) {
    $requestLength=RandomPassword::MIN_LENGTH;
}
if (!$requestSecurityLevel) {
    $requestSecurityLevel=RandomPassword::SECURITY_LEVEL_HIGH;
}

$password->setSecurityLevel($requestSecurityLevel);
$password->setLength($requestLength);
$password->useDefaultSettings();
$generatedPassword=$password->generateString();

$json = $_GET['json'] ?? 0;

if ($json) {
    $data['password'] = $generatedPassword;
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit();
}
