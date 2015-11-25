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


//https://github.com/ensembleVideo/moodle-repository_ensemble/blob/master/ext_chooser/index.php

/**
 * This plugin is used to upload files
 *
 * @since Moodle 2.0
 * @package    repository_plupload
 * @copyright  2015 Pau Ferrer Ocaña
 * @author     Pau Ferrer Ocaña <pferre22@xtec.cat>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
/// Wait as long as it takes for this script to finish
core_php_time_limit::raise();

require_login();

// disable blocks in this page
$PAGE->set_pagelayout('embedded');

// user context
$user_context = context_user::instance($USER->id);

$PAGE->set_context($user_context);
if (!$course = $DB->get_record('course', array('id'=>SITEID))) {
    print_error('invalidcourseid');
}
$PAGE->set_course($course);
$PAGE->requires->jquery();
$PAGE->requires->js('/repository/plupload/js/plupload.full.min.js');

echo $OUTPUT->header();
?>
<h1>Upload Big files</h1>

<div id="filelist">Your browser doesn't have HTML5 support.</div>
<form enctype="multipart/form-data" method="POST">
    <div id="container">
        <a id="pickfiles" href="javascript:;">[Select files]</a>
        <div class="mdl-align">
            <button id="uploadfiles" class="fp-upload-btn btn-primary btn"><?php echo get_string('upload', 'repository');?></button>
        </div>
    </div>
</form>

<pre id="console"></pre>

<script type="text/javascript">
    // jQuery not really required, it's here to overcome an inability to pass configuration options to the fiddle remotely
    $(document).ready(function() {
        // Custom example logic
        function $(id) {
            return document.getElementById(id);
        }

        var uploader = new plupload.Uploader({
            runtimes : 'html5,html4',
            browse_button : 'pickfiles', // you can pass in id...
            container: $('container'), // ... or DOM Element itself
            max_file_size : '10mb',

            // Fake server response here
            // url : '../upload.php',
            url: "/echo/json",

            filters : [],

            init: {
                PostInit: function() {
                    $('filelist').innerHTML = '';

                    $('uploadfiles').onclick = function() {
                        uploader.start();
                        return false;
                    };
                },

                FilesAdded: function(up, files) {
                    plupload.each(files, function(file) {
                        $('filelist').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
                    });
                },

                UploadProgress: function(up, file) {
                    $(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
                },

                Error: function(up, err) {
                    $('console').innerHTML += "\nError #" + err.code + ": " + err.message;
                }
            }
        });

        uploader.init();
    });
</script>
<?php
echo $OUTPUT->footer();
