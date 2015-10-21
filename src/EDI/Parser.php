<?php

namespace EDI;

class Parser
{
	private $parsedData = array();

	private $errors = array();

	public function __construct($source = null)
	{
		if(is_null($source)) {
            return;
        }

        $this->parse($source);
	}

	//Parse edi array
	public function parse($data)
	{
        if (is_array($data)) { //ARRAY
            if (count($data) == 1) { //containing only one row
                $data = $this->unwrapString(reset($data));
            }
        } elseif (file_exists($data)) {
            $data = $this->loadFile($data); //FILE URL
        } else {
            $data = $this->unwrapString($data);
        }

		$i = 0;
		foreach ($data as $x => &$line) {
            $i++;
			$line = preg_replace('#[\r\n]#', '', $line); //carriage return removal (CR+LF)
			if (preg_match("/[\x01-\x1F\x80-\xFF]/",$line))
				$this->errors[] = "There's a not printable character on line ".($x+1).": ". $line;
			$line = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $line); //basic sanitization, remove non printable chars
			if (strlen($line) == 0 || substr( $line, 0, 3 ) === "UNA")
			{
				unset($data[$x]);
				continue;
			}
			if (strrpos($line,"'") != strlen($line)-1)
				$this->errors[] = 'Segment not ended correctly at line '.$i. "=>". $line;
			$line = $this->splitSegment($line);
		}
		$this->parsedData = array_values($data); //reindex
		return $data;
	}

	//unwrap string splitting rows on terminator (if not escaped)
	public function unwrapString($string)
	{
        $lines = explode("\n", str_replace("\n\r", "\n", $string));
        $segments = array();
        foreach ($lines as $line) {
            $segments = array_merge($segments, preg_split("/(.*?(?<!\?)')/", $line, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE));
        }

        $unwrapped = array();
		foreach($segments as &$segment) {
            $unwrapped[] = trim($segment);
		}
		return $unwrapped;
	}

    //Get errors
    public function errors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    public function getParsedData()
    {
        return $this->parsedData;
    }

	//Segments
	protected function splitSegment($str)
	{
		$str = strrev(preg_replace("/'/", "", strrev($str), 1));//remove ending " ' "
		$matches = preg_split("/(?<!\?)\+/", $str); //split on + if not escaped (negative lookbehind)
		foreach ($matches as &$value)
		{
			if (preg_match("/(?<!\?)'/",$value))
				$this->errors[] = "There's a ' not escaped in the data; string ". $str;
			if (preg_match("/(?<!\?)\?(?!\?)(?!\+)(?!:)(?!')/",$value))
				$this->errors[] = "There's a character not escaped with ? in the data; string ". $value;
			$value = $this->splitData($value); //split on :
		}
		return $matches;
	}

	//Composite data element
	protected function splitData($str)
	{
		$arr = preg_split("/(?<!\?):/", $str); //split on : if not escaped (negative lookbehind)
		if (count($arr) == 1)
			return preg_replace("/\?(?=\?)|\?(?=\+)|\?(?=:)|\?(?=')/", "",$str); //remove ? if not escaped
		foreach ($arr as &$value)
			$value = preg_replace("/\?(?=\?)|\?(?=\+)|\?(?=:)|\?(?=')/", "",$value);
		return $arr;
	}

	protected function loadFile($url)
	{
		$fileContent = file($url);
		if (count($fileContent) == 1) //containing only one row
		{
            $fileContent = $this->unwrapString($fileContent[0]);
		}
		return $fileContent;
	}
}
