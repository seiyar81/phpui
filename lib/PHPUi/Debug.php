<?php

namespace PHPUi;

/*
 * From Zend Framework, Zend_Debug
 * <http://www.zend.com>
 */

final class Debug
{
    /**
     * Debug helper function.  This is a wrapper for var_dump() that adds
     * the <pre /> tags, cleans up newlines and indents, and runs
     * htmlentities() before output.
     *
     * @param  mixed  $var   The variable to dump.
     * @param  string $label OPTIONAL Label to prepend to output.
     * @param  bool   $echo  OPTIONAL Echo output if true.
     * @return string
     */
    public static function dump($var, $label=null, $echo=true)
    {
        // format the label
        $label = ($label===null) ? '' : rtrim($label) . ' ';

        // var_dump the variable into a buffer and keep the output
        ob_start();
        var_dump($var);
        $output = ob_get_clean();

        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

        $output = '<pre>'
              . $label
              . $output
              . '</pre>';
                    
        if ($echo) {
            echo($output);
        }
        return $output;
    }
	
}