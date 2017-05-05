<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.


defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/../../config.php');
global $CFG, $DB;

/**
 * Block vpl_brython class definition.
 *
 * This block can be added to a vpl page to support brython for feedback
 *
 * @package    block_vpl_brython
 * @copyright  2016 Guillaume Blin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_vpl_brython extends block_base {

  function init() {
    $this->title = get_string('pluginname', 'block_vpl_brython');
  }

  function applicable_formats() {
    return array('all' => false,'mod-vpl' => true);
  }

  function instance_config_save($data, $nolongerused = false) {
    parent::instance_config_save($data);
  }


  function get_content() {
    global $CFG, $OUTPUT, $DB, $USER;
    $this->content = new stdClass();
    $this->content->items = array();
    $this->content->icons = array();
    $this->content->footer = '';
    $id=$this->page->course->id;
    $p=substr($_SERVER['PHP_SELF'],0,strpos($_SERVER['PHP_SELF'],'mod/vpl')).'blocks/vpl_brython';
    $this->content->text = <<<EOT
<script src="$p/brython.js"></script>
<script type="text/javascript" src="$p/brython_stdlib.js"></script>
<script type="text/javascript">
  function close_modal(){
    document.getElementById("openModal").style.opacity=0;
    document.getElementById("openModal").style.pointerEvents="none";
  }
</script>
<div id='openModal' class='modalDialog'>
        <div>
        	<a href='javascript:close_modal()' title='Close' class='mclose'>X</a>
        	<h2 id="w_caption">Output</h2>
        	<span id='modal_content'>
        	</span>
        	<script type="text/python" id='teacher_script'>
        	</script>
        	<script type="text/python" id='modal_script'>
        	</script>
        </div>
</div>
<script type="text/javascript">
  var target = document.getElementById('vpl_results');
  if(target){ 
	var observer = new MutationObserver(function(mutations) {
	var ct = document.getElementById('ui-accordion-vpl_results-panel-2').textContent;
      	if(ct.indexOf('BRYTHON')>-1){
        	var truc = document.getElementById("openModal");
        	truc.style.opacity=1;
        	truc.style.pointerEvents="auto";
		var bs="", bc="", ts="";
		var lines = ct.split('\\n');
		var go_bs = false, go_bc = false, go_ts = false;
		for(var i = 0;i < lines.length;i++){
			if(lines[i].match(/BRYTHON-SCRIPT-E/)){
                        	go_bs=false;
                	}
			if(lines[i].match(/BRYTHON-CONTENT-E/)){
                        	go_bc=false;
                	}
			if(lines[i].match(/BRYTHON-TSCRIPT-E/)){
                        	go_ts=false;
                	}
			if(go_bs){
				bs = bs + lines[i]+'\\n';
			}
			if(go_bc){
                        	bc = bc + lines[i]+'\\n';
                	}
			if(go_ts){
                        	ts = ts + lines[i]+'\\n';
                	}
			if(lines[i].match(/BRYTHON-SCRIPT-S/)){
                        	go_bs=true;
                	}
			if(lines[i].match(/BRYTHON-CONTENT-S/)){
                        	go_bc=true;
                	}
			if(lines[i].match(/BRYTHON-TSCRIPT-S/)){
                        	go_ts=true;
                	}
		}
        	document.getElementById('modal_script').textContent = bs;
		document.getElementById('modal_content').innerHTML = bc;
		document.getElementById('teacher_script').textContent = ts; 
		brython({debug:1, ipy_id:['teacher_script','modal_script']});
      	}
    });
    var config = { childList: true, characterData: true };
    observer.observe(target, config);
}
</script>
EOT;
    return $this->content;
    }


    function instance_allow_multiple() {
        return true;
    }
}

