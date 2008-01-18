<?php

class xmlParser{
   var $xml_obj = null;
   var $attrs;  
   var $last_name;
   var $root_node;
   var $depth = 0;
   var $data = false;
   var $offset = 0;
   var $current_ptr = null;
   var $current_path;

   function xmlParser(){
       $this->xml_obj = xml_parser_create();

       xml_set_object($this->xml_obj,$this);
       xml_set_character_data_handler($this->xml_obj, 'dataHandler');
       xml_set_element_handler($this->xml_obj, "startHandler", "endHandler");
   }

   function parse_stream($data) {
	$this->prep();

	echo $data . "\n\n";

           if (!xml_parse($this->xml_obj, $data, TRUE)) {
               die(sprintf("XML error: %s at line %d",
               xml_error_string(xml_get_error_code($this->xml_obj)),
               xml_get_current_line_number($this->xml_obj)));
               xml_parser_free($this->xml_obj);
           }
	return true;
	}
	

   function prep() {

	$this->current_path = array();
	$this->root_node = array();
	$this->current_path[0] = &$this->root_node;
   }

   function parse($path){


       if (!($fp = fopen($path, "r"))) {
           die("Cannot open XML data file: $path");
           return false;
       }

	$this->prep();

       while ($data = fread($fp, 4096)) {
           if (!xml_parse($this->xml_obj, $data, feof($fp))) {
               die(sprintf("XML error: %s at line %d",
               xml_error_string(xml_get_error_code($this->xml_obj)),
               xml_get_current_line_number($this->xml_obj)));
               xml_parser_free($this->xml_obj);
           }
       }

       return true;
   }

   
   
   
   function parse_curl($path){


	$this->prep();

	// create a new cURL resource
	$ch = curl_init();
	
	// set URL and other appropriate options
	curl_setopt($ch, CURLOPT_URL, "$path");
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, FALSE);
	
	// grab URL and pass it to the browser
	$data = curl_exec($ch);	
	// close cURL resource, and free up system resources
	curl_close($ch);

           if (!xml_parse($this->xml_obj, $data, feof($fp))) {
               die(sprintf("XML error: %s at line %d",
               xml_error_string(xml_get_error_code($this->xml_obj)),
               xml_get_current_line_number($this->xml_obj)));
               xml_parser_free($this->xml_obj);
           }

       return true;
   }
   
   
   
   
   
   
   function startHandler($parser, $name, $attribs){

	// debugging
	// echo "start: " . $this->depth .":" . $name ."\n";


	// desired behavior:
	// all child data nodes appear as indexes [0...n] under the root node/their parent node
	// all child nodes appear as names [FOO] under the root node / their parent node


	// current_path[0] should contain the root node
	// current_path[1] should contain the 1st child node on our current tree
	// etc


	// we are starting a new named node
	// if any data comes in, it will create dataset N
	// current pointer should be to the node

	// $current_path[depth-1] points to our parent
	// which we know exists


	// if we are a new name

	// check to see if we are the root node


	if(!isset($this->current_path[$this->depth])) {
		echo "BROKEN!";
	}	

	if(!isset($this->current_path[$this->depth][$name])) {

		// debugging
		// echo "new nodename: $name\n";

		// we create our resource node

		$this->current_path[$this->depth][$name] = array();

		// we create our data node
	
		$local_offset = 0;

	} else {
		// we exist already


		// we find our resource node

		$local_offset = count($this->current_path[$this->depth][$name]);
	
		// debugging
		// echo "existing nodename: $name\n";
	
	}

	// we create our data node

	// debugging
	// echo "offset: $local_offset\n";

	$this->current_path[$this->depth][$name][$local_offset] = array();

	// we set the current pointer

	$this->current_ptr = &$this->current_path[$this->depth][$name][$local_offset]; 

	if(!isset($this->current_ptr)) {
		echo "BROKEN\n";
	}
		
	// we descend to our new child

	$this->depth++;
	
	// we add ourselves to the path

	$this->current_path[$this->depth] = &$this->current_ptr;

	// we add any attributes there might be

      	if(!empty($attribs))
       		  $this->current_ptr['00attrs'] = $attribs;

	// and go
   }

   function dataHandler($parser, $data){
	// what to do about whitespace?
	// remove it of course
	$data = preg_replace("/^\W*/","",$data);

	// we are only interested in real data
       if(empty($data) || $data == "\n") 
		return;


       $this->current_ptr["00data"] .= $data;

	$this->data = true; // add the data to the current pointer

	// debugging
	// echo "data: [$data]\n";
	
   }

   function endHandler($parser, $name){

	$this->depth--; // go up a level

	// debugging
	// echo "end  :" . $this->depth . ":" . $name . "\n";

   }
} /* end class */

?>
