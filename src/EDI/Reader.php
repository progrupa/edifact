<?php

namespace EDI;

use Doctrine\Common\Annotations\AnnotationReader;
use EDI\Annotations\Mandatory;
use EDI\Annotations\SegmentPiece;
use EDI\Exception\MandatorySegmentPieceMissing;
use EDI\Exception\MappingNotFoundException;
use EDI\Mapping\MappingLoader;
use EDI\Message\Interchange;
use EDI\Populate\InterchangePopulator;
use EDI\Populate\Populator;

class Reader
{
    /** @var Parser EDI message parser */
    private $parser;
    /** @var  InterchangePopulator */
    private $populator;
    /** @var  MappingLoader */
    private $mappingLoader;
    /** @var  string */
    private $mappingDirectory;
    /** @var array Errors found when reading EDIFACT message */
	private $errors = array();

	public function __construct(Parser $parser, Populator $populator)
	{
        $this->parser = $parser;
        $this->populator = $populator;
	}

	function errors()
	{
		return $this->errors;
	}

	function resetErrors()
	{
		$this->errors = array();
	}

	public function readEdiDataValueReq($filter,$l1,$l2 = false) {    
        return $this->readEdiDataValue($filter, $l1, $l2,true);
    }
    
	/**
	 * read data value from parsed EDI data
	 * 
	 * @param array/string $filter 
	 *  'AGR' - segment code 
	 *  or ['AGR',['1'=>'BB']], where AGR segment code and first element equal 'BB'
	 *  or ['AGR',['1.0'=>'BB']], where AGR segment code and first element zero subelement  equal 'BB'
	 * @param int $l1 first level item number (start by 1)
	 * @param int/false $l2 second level item number (start by 0)
	 * @return string/null
	 */
	public function readEdiDataValue($filter,$l1,$l2 = false,$required = false) {

		//interpret filter arameters
		if (!is_array($filter)) {
			$segment_name = $filter;
			$filter_elements = false;
		} else {
			$segment_name = $filter[0];
			$filter_elements = $filter[1];
		}

		$segment = false;
		$segment_count = 0;

		//search segment, who conform to filter
		foreach ($this->parsedfile as $edi_row) {
			if ($edi_row[0] == $segment_name) {
				if ($filter_elements) {
					$filter_ok = false;
					foreach ($filter_elements as $el_id => $el_value) {
						$f_el_list = explode('.', $el_id);
						if (count($f_el_list) == 1) {
							if (isset($edi_row[$el_id]) && $edi_row[$el_id] == $el_value) {
								$filter_ok = true;
								break;
							}
						} else {
							if (isset($edi_row[$f_el_list[0]]) && isset($edi_row[$f_el_list[0]][$f_el_list[1]]) && $edi_row[$f_el_list[0]][$f_el_list[1]] == $el_value) {
								$filter_ok = true;
								break;
							}
						}
					}
					if ($filter_ok === false) {
						continue;
					}
				}
				$segment = $edi_row;
				$segment_count ++;
			}
		}

		//no found segment
		if (!$segment) {
            if($required){
                $this->errors[] = 'Segment "' . $segment_name . '" no exist';                
            }
			return null;
		}

		//found more one segment - error
		if ($segment_count > 1) {
			
            $this->errors[] = 'Segment "' . $segment_name . '" is ambiguous';
			return null;
		}

		//validate elements
		if (!isset($segment[$l1])) {
            if($required){
                $this->errors[] = 'Segment value "' . $segment_name . '['.$l1.']" no exist';                
            }            
			return null;
		}

		//requestd first level element
		if ($l2 === false) {
			return $segment[$l1];
		}

		//requestd second level element, but not exist
		if (!is_array($segment[$l1]) || !isset($segment[$l1][$l2])) {
            if($required){
                $this->errors[] = 'Segment value "' . $segment_name . '['.$l1.']['.$l2.']" no exist';                
            }             
			return null;
		}

		//second level element
		return $segment[$l1][$l2];
	}

    
    /**
     * read date from DTM segment period qualifier - codelist 2005
     * @param int $PeriodQualifier period qualifier (codelist/2005)
     * @return string YYYY-MM-DD HH:MM:SS
     */
    public function readEdiSegmentDTM($PeriodQualifier){

        $date = $this->readEdiDataValue(['DTM', ['1.0' => $PeriodQualifier]], 1,1);
        $format = $this->readEdiDataValue(['DTM', ['1.0' => $PeriodQualifier]], 1,2);
        if(empty($date)){
            return $date;
        }
        switch ($format) {

            case 203: //CCYYMMDDHHMM
                return preg_replace('#(\d\d\d\d)(\d\d)(\d\d)(\d\d)(\d\d)#','$1-$2-$3 $4:$5:00' , $date);

                break;

            default:
                return $date;
                break;
        }
    }
    
    public function readUNBDateTimeOfPpreperation(){
        
        //separate date (YYMMDD) and time (HHMM)
        $date = $this->readEdiDataValue('UNB', 4, 0);
        if(!empty($date)){
            $time = $this->readEdiDataValue('UNB', 4, 1);
            $datetime = preg_replace('#(\d\d)(\d\d)(\d\d)(\d\d)(\d\d)#','20$1-$2-$3 $4:$5:00' , $date.$time);
            return $datetime;
        }
        
        //common YYYYMMDDHHMM
        $datetime = $this->readEdiDataValue('UNB', 4);
        $datetime = preg_replace('#(\d\d\d\d)(\d\d)(\d\d)(\d\d)(\d\d)#','$1-$2-$3 $4:$5:00' , $datetime);
        
        return $datetime;
    }

    public function readTDTtransportIdentification($transportStageQualifier){
        
        $transportIdentification = $this->readEdiDataValue(['TDT', ['1' => $transportStageQualifier]], 8, 0);
        if(!empty($transportIdentification)){
            return $transportIdentification;
        }
        
        return $this->readEdiDataValue(['TDT', ['1' => $transportStageQualifier]], 8);        

    }
    
    public function readUNHmessageType(){
        return $this->readEdiDataValue('UNH', 2,0);        
    }

    /**
     * get groups from message
     * @param char $before segment before groups 
     * @param type $start first segment of group
     * @param type $end last segment of group
     * @param type $after segment after groups
     * @return boolean/array
     */
    public function readGroups($before,$start,$end,$after){
        $groups = [];
        $group = [];
        $position = 'before_search';
        foreach ($this->parsedfile as $edi_row) {
            //echo $edi_row[0].' '.$position.PHP_EOL;
            
            //search before group segment
            if ($position == 'before_search' && $edi_row[0] == $before) {
                $position = 'before_is';
                continue;
            }

            if ($position == 'before_search') {
                continue;
            }

            if ($position == 'before_is' && $edi_row[0] == $before) {
                continue;
            }            
            
            //after before search start
            if ($position == 'before_is' && $edi_row[0] == $start) {
                $position = 'group_is';
                $group[] = $edi_row;
                continue;
            }            

            //if after before segment no start segment, search again before segment
            if ($position == 'before_is') {
                $position = 'before_search';
                continue;
            }            

            //get group element
            if ($position == 'group_is' && $edi_row[0] != $end) {
                $group[] = $edi_row;
                continue;
            }            
            
            //found end of group
            if ($position == 'group_is' && $edi_row[0] == $end) {
                $position = 'group_finish';
                $group[] = $edi_row;
                $groups[] = $group; 
                $group = [];
                continue;
            }            
            
            //next group start
            if ($position == 'group_finish' && $edi_row[0] == $start) {
                $group[] = $edi_row;
                $position = 'group_is';
                continue;
            }                 
            
            //finish
            if ($position == 'group_finish' && $edi_row[0] == $after) {
                break;
            }       
            
            $this->errors[] = 'Reading group ' . $before.'/'.$start.'/'.$end.'/'.$after
                    .'. Error on position: ' . $position;
            return false;
            
        }
        
        return $groups;
        
    }

    /**
     * @param $message
     * @return Interchange
     */
    public function transform($message)
    {
        $transformed = $this->parser->parse($message);

        $interchange = $this->populator->populate($transformed);

        return $interchange;
    }

    /**
     * @param $version
     * @return Populator
     * @throws MappingNotFoundException
     */
    protected function updatePopulatorConfiguration($version)
    {
        $dir = realpath($this->mappingDirectory . "/$version");
        if (!file_exists($dir)) {
            throw new MappingNotFoundException(sprintf("Mapping directory '%s' not found", $this->mappingDirectory));
        }

        $segmentsFile = $dir . '/segments.xml';
        if (!file_exists($segmentsFile) || !is_readable($segmentsFile)) {
            throw new MappingNotFoundException(sprintf("segments.xml mapping file not found in directory '%s'",
                $this->mappingDirectory));
        }
        $this->populator->setSegments($this->mappingLoader->loadSegments($segmentsFile));

        $codesFile = $dir . '/codes.xml';
        if (!file_exists($codesFile) || !is_readable($codesFile)) {
            throw new MappingNotFoundException(sprintf("codes.xml mapping file not found in directory '%s'",
                $this->mappingDirectory));
        }
        $this->populator->setCodes($this->mappingLoader->loadCodes($codesFile));
    }
}
