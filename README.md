PHPUi : Open source PHP Library that let you manage CSS and JS code from the server-side. 
=======================================================

Overview
--------
- PHPUi is an Open source library written in PHP and that aims to be compatible with all recents frameworks. 
- It let you manage CSS and JS code from the server-side with some helpful features like inheritance, on the fly code generation etc ...
- PHPUi will be released under the GNU GPL v3 License.


Examples
--------
	<blockquote>
	        $gs = PHPUi::getInstance()->gs(array('columns' => 16));
	        $gs->addChild(new Xhtml\Element('h2', null, true, '16 Column Grid - 960Gs'));
	        $gs->jquery()->click(
	            $gs->jquery()->ajax(
	                array(
	                    'url' => 'ajax.php',
	                    'type' => 'POST',
	                    'data' => array( 'param1' => 'value1', 'param2' => 'value2' ),
	                    'dataType' => 'html',
	                    'success' => 'function(data) { $(".container_16").append(data); }'
	                )
	            )
        	);
	        echo $gs;
	</blockquote>


Documentation, Features and Demos
---------------------------------
- Some basic examples can be found in examples/index.php.
- Outdated development tests for now :)

