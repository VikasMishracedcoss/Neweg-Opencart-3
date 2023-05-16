<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="if (confirm('Are you sure ?')) { $('#form').submit(); }"><i class="fa fa-trash-o"></i></button>
            </div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                                <td class="text-center"><?php if ($sort == 'id') { ?>
                                    <a href="<?php echo $sort_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_id; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_id; ?>"><?php echo $column_id; ?></a>
                                    <?php } ?></td>
                                <td class="text-center"><?php if ($sort == 'method') { ?>
                                    <a href="<?php echo $sort_method; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_method; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_method; ?>"><?php echo $column_method; ?></a>
                                    <?php } ?></td>
                                <td class="text-center"><?php if ($sort == 'type') { ?>
                                    <a href="<?php echo $sort_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_type; ?></a>
                                    <?php } else { ?>
                                    <a href="<?php echo $sort_type; ?>"><?php echo $column_type; ?></a>
                                    <?php } ?></td>
                                <td class="text-center"><?php echo $column_message; ?></td>
                                <td class="text-center"><?php echo $column_response; ?></td>
                                <td class="text-center"><?php echo $column_created_at; ?></td>
                                <td class="text-center"><?php echo $column_action; ?></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($logs)) { ?>
                            <?php foreach ($logs as $log) { ?>
                            <tr>
                                <td class="text-center"><?php if ($log['selected']) { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $log['id']; ?>" checked="checked" />
                                    <?php } else { ?>
                                    <input type="checkbox" name="selected[]" value="<?php echo $log['id']; ?>" />
                                    <?php } ?></td>
                                <td class="text-center"><?php echo $log['id']; ?></td>
                                <td class="text-center"><?php echo $log['method']; ?></td>
                                <td class="text-center"><?php echo $log['type']; ?></td>
                                <td class="text-center"><?php echo $log['message']; ?></td>
                                <td class="text-center">
                                    <a onclick="logInfo('<?php echo $log['id']; ?>')" data-toggle="tooltip" title="view" class="btn btn-info"><i class="fa fa-eye"></i></a>
                                </td>
                                <td class="text-center"><?php echo $log['created_at']; ?></td>
                                <td class="text-center"><?php foreach ($log['action'] as $action) { ?>
                                    <a onclick="if (confirm('Are you sure ?')) { $(this).attr('href', '<?php echo $action["href"]; ?>'); $('#form').submit(); }" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                    <?php } ?></td>
                            </tr>
                            <?php } ?>
                            <?php } else { ?>
                            <tr>
                                <td class="center" colspan="8"><?php echo $text_no_results; ?></td>
                            </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </form>
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0, 0, 0); /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
    }

    .modal-content {
        background-color: #fefefe;
        margin: 6em 0 0 30em;
        padding: 20px;
        border: 1px solid #888;
        width: 60%;
    }
    .data{
        max-height: 300px;
        overflow: auto;
        padding: 10px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <button class="close" onclick="closeModal()">&times;</button>
        <div id="popup_content" class="data"> Loading.......</div>
    </div>
</div>
<script type="text/javascript">
    var modal = document.getElementById('myModal');
    var span = document.getElementsByClassName("close")[0];
    span.onclick = function () {
        modal.style.display = "none";
        $("#popup_content").html('Loading........');
    }
    function closeModal() {
        var modal = document.getElementById('myModal').style.display = 'none';
    }
    function logInfo(id) {
        modal.style.display = "block";
        var url = 'index.php?route=cedebay/log/viewResponse&token=<?php echo $token; ?>';
        $.ajax({
            type: "POST",
            url: url,
            data: {
                'id' : id
            },
            success: function (response) {
                var data = response;
                var res = vkbeautify.json(data);
                $("#popup_content").html('<pre>' + res + '</pre>');
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (window.console) console.log(xhr.responseText);
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);

            },
        });

    }
</script>
<script>
    /**
     * vkBeautify - javascript plugin to pretty-print or minify text in XML, JSON, CSS and SQL formats.
     *
     * Version - 0.99.00.beta
     * Copyright (c) 2012 Vadim Kiryukhin
     * vkiryukhin @ gmail.com
     * http://www.eslinstructor.net/vkbeautify/
     *
     * MIT license:
     *   http://www.opensource.org/licenses/mit-license.php
     *
     *   Pretty print
     *
     *        vkbeautify.xml(text [,indent_pattern]);
     *        vkbeautify.json(text [,indent_pattern]);
     *        vkbeautify.css(text [,indent_pattern]);
     *        vkbeautify.sql(text [,indent_pattern]);
     *
     *        @text - String; text to beatufy;
     *        @indent_pattern - Integer | String;
     *                Integer:  number of white spaces;
     *                String:   character string to visualize indentation ( can also be a set of white spaces )
     *   Minify
     *
     *        vkbeautify.xmlmin(text [,preserve_comments]);
     *        vkbeautify.jsonmin(text);
     *        vkbeautify.cssmin(text [,preserve_comments]);
     *        vkbeautify.sqlmin(text);
     *
     *        @text - String; text to minify;
     *        @preserve_comments - Bool; [optional];
     *                Set this flag to true to prevent removing comments from @text ( minxml and mincss functions only. )
     *
     *   Examples:
     *        vkbeautify.xml(text); // pretty print XML
     *        vkbeautify.json(text, 4 ); // pretty print JSON
     *        vkbeautify.css(text, '. . . .'); // pretty print CSS
     *        vkbeautify.sql(text, '----'); // pretty print SQL
     *
     *        vkbeautify.xmlmin(text, true);// minify XML, preserve comments
     *        vkbeautify.jsonmin(text);// minify JSON
     *        vkbeautify.cssmin(text);// minify CSS, remove comments ( default )
     *        vkbeautify.sqlmin(text);// minify SQL
     *
     */

    (function () {

        function createShiftArr(step)
        {

            var space = '    ';

            if (isNaN(parseInt(step))) {  // argument is string
                space = step;
            } else { // argument is integer
                switch (step) {
                    case 1:
                        space = ' ';
                        break;
                    case 2:
                        space = '  ';
                        break;
                    case 3:
                        space = '   ';
                        break;
                    case 4:
                        space = '    ';
                        break;
                    case 5:
                        space = '     ';
                        break;
                    case 6:
                        space = '      ';
                        break;
                    case 7:
                        space = '       ';
                        break;
                    case 8:
                        space = '        ';
                        break;
                    case 9:
                        space = '         ';
                        break;
                    case 10:
                        space = '          ';
                        break;
                    case 11:
                        space = '           ';
                        break;
                    case 12:
                        space = '            ';
                        break;
                }
            }

            var shift = ['\n']; // array of shifts
            for (ix = 0; ix < 100; ix++) {
                shift.push(shift[ix] + space);
            }
            return shift;
        }

        function vkbeautify()
        {
            this.step = '\t'; // 4 spaces
            this.shift = createShiftArr(this.step);
        };

        vkbeautify.prototype.xml = function (text, step) {

            var ar = text.replace(/>\s{0,}</g, "><")
                    .replace(/</g, "~::~<")
                    .replace(/\s*xmlns\:/g, "~::~xmlns:")
                    .replace(/\s*xmlns\=/g, "~::~xmlns=")
                    .split('~::~'),
                len = ar.length,
                inComment = false,
                deep = 0,
                str = '',
                ix = 0,
                shift = step ? createShiftArr(step) : this.shift;

            for (ix = 0; ix < len; ix++) {
                // start comment or <![CDATA[...]]> or <!DOCTYPE //
                if (ar[ix].search(/<!/) > -1) {
                    str += shift[deep] + ar[ix];
                    inComment = true;
                    // end comment  or <![CDATA[...]]> //
                    if (ar[ix].search(/-->/) > -1 || ar[ix].search(/\]>/) > -1 || ar[ix].search(/!DOCTYPE/) > -1) {
                        inComment = false;
                    }
                } else { // end comment  or <![CDATA[...]]> //
                    if (ar[ix].search(/-->/) > -1 || ar[ix].search(/\]>/) > -1) {
                        str += ar[ix];
                        inComment = false;
                    } else { // <elm></elm> //
                        if (/^<\w/.exec(ar[ix - 1]) && /^<\/\w/.exec(ar[ix]) &&
                            /^<[\w:\-\.\,]+/.exec(ar[ix - 1]) == /^<\/[\w:\-\.\,]+/.exec(ar[ix])[0].replace('/', '')) {
                            str += ar[ix];
                            if (!inComment) {
                                deep--;
                            }
                        } else { // <elm> //
                            if (ar[ix].search(/<\w/) > -1 && ar[ix].search(/<\//) == -1 && ar[ix].search(/\/>/) == -1) {
                                str = !inComment ? str += shift[deep++] + ar[ix] : str += ar[ix];
                            } else { // <elm>...</elm> //
                                if (ar[ix].search(/<\w/) > -1 && ar[ix].search(/<\//) > -1) {
                                    str = !inComment ? str += shift[deep] + ar[ix] : str += ar[ix];
                                } else { // </elm> //
                                    if (ar[ix].search(/<\//) > -1) {
                                        str = !inComment ? str += shift[--deep] + ar[ix] : str += ar[ix];
                                    } else { // <elm/> //
                                        if (ar[ix].search(/\/>/) > -1) {
                                            str = !inComment ? str += shift[deep] + ar[ix] : str += ar[ix];
                                        } else { // <? xml ?> //
                                            if (ar[ix].search(/<\?/) > -1) {
                                                str += shift[deep] + ar[ix];
                                            } else {             // xmlns //
                                                if (ar[ix].search(/xmlns\:/) > -1 || ar[ix].search(/xmlns\=/) > -1) {
                                                    str += shift[deep] + ar[ix];
                                                } else {
                                                    str += ar[ix];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return (str[0] == '\n') ? str.slice(1) : str;
        }

        vkbeautify.prototype.json = function (text, step) {

            var step = step ? step : this.step;

            if (typeof JSON === 'undefined') {
                return text;
            }

            if (typeof text === "string") {
                return JSON.stringify(JSON.parse(text), null, step);
            }
            if (typeof text === "object") {
                return JSON.stringify(text, null, step);
            }

            return text; // text is not string nor object
        }

        vkbeautify.prototype.css = function (text, step) {

            var ar = text.replace(/\s{1,}/g, ' ')
                .replace(/\{/g, "{~::~")
                .replace(/\}/g, "~::~}~::~")
                        .replace(/\;/g, ";~::~")
                        .replace(/\/\*/g, "~::~/*")
                        .replace(/\*\//g, "*/~::~")
                        .replace(/~::~\s{0,}~::~/g, "~::~")
                        .split('~::~'),
                    len = ar.length,
                    deep = 0,
                    str = '',
                    ix = 0,
                    shift = step ? createShiftArr(step) : this.shift;

            for (ix = 0; ix < len; ix++) {
                if (/\{/.exec(ar[ix])) {
                str += shift[deep++] + ar[ix];
            } else if (/\}/.exec(ar[ix])) {
            str += shift[--deep] + ar[ix];
        } else if (/\*\\/.exec(ar[ix])) {
            str += shift[deep] + ar[ix];
        } else {
            str += shift[deep] + ar[ix];
        }
    }
    return str.replace(/^\n{1,}/, '');
    }

    //----------------------------------------------------------------------------

    function isSubquery(str, parenthesisLevel)
    {
        return parenthesisLevel - (str.replace(/\(/g, '').length - str.replace(/\)/g, '').length)
    }

    function split_sql(str, tab)
    {

        return str.replace(/\s{1,}/g, " ")

            .replace(/ AND /ig, "~::~" + tab + tab + "AND ")
            .replace(/ BETWEEN /ig, "~::~" + tab + "BETWEEN ")
            .replace(/ CASE /ig, "~::~" + tab + "CASE ")
            .replace(/ ELSE /ig, "~::~" + tab + "ELSE ")
            .replace(/ END /ig, "~::~" + tab + "END ")
            .replace(/ FROM /ig, "~::~FROM ")
            .replace(/ GROUP\s{1,}BY/ig, "~::~GROUP BY ")
            .replace(/ HAVING /ig, "~::~HAVING ")
            //.replace(/ SET /ig," SET~::~")
            .replace(/ IN /ig, " IN ")

            .replace(/ JOIN /ig, "~::~JOIN ")
            .replace(/ CROSS~::~{1,}JOIN /ig, "~::~CROSS JOIN ")
            .replace(/ INNER~::~{1,}JOIN /ig, "~::~INNER JOIN ")
            .replace(/ LEFT~::~{1,}JOIN /ig, "~::~LEFT JOIN ")
            .replace(/ RIGHT~::~{1,}JOIN /ig, "~::~RIGHT JOIN ")

            .replace(/ ON /ig, "~::~" + tab + "ON ")
            .replace(/ OR /ig, "~::~" + tab + tab + "OR ")
            .replace(/ ORDER\s{1,}BY/ig, "~::~ORDER BY ")
            .replace(/ OVER /ig, "~::~" + tab + "OVER ")

            .replace(/\(\s{0,}SELECT /ig, "~::~(SELECT ")
            .replace(/\)\s{0,}SELECT /ig, ")~::~SELECT ")

            .replace(/ THEN /ig, " THEN~::~" + tab + "")
            .replace(/ UNION /ig, "~::~UNION~::~")
            .replace(/ USING /ig, "~::~USING ")
            .replace(/ WHEN /ig, "~::~" + tab + "WHEN ")
            .replace(/ WHERE /ig, "~::~WHERE ")
            .replace(/ WITH /ig, "~::~WITH ")

            //.replace(/\,\s{0,}\(/ig,",~::~( ")
            //.replace(/\,/ig,",~::~"+tab+tab+"")

            .replace(/ ALL /ig, " ALL ")
            .replace(/ AS /ig, " AS ")
            .replace(/ ASC /ig, " ASC ")
            .replace(/ DESC /ig, " DESC ")
            .replace(/ DISTINCT /ig, " DISTINCT ")
            .replace(/ EXISTS /ig, " EXISTS ")
            .replace(/ NOT /ig, " NOT ")
            .replace(/ NULL /ig, " NULL ")
            .replace(/ LIKE /ig, " LIKE ")
            .replace(/\s{0,}SELECT /ig, "SELECT ")
            .replace(/\s{0,}UPDATE /ig, "UPDATE ")
            .replace(/ SET /ig, " SET ")

            .replace(/~::~{1,}/g, "~::~")
            .split('~::~');
    }

    vkbeautify.prototype.sql = function (text, step) {

        var ar_by_quote = text.replace(/\s{1,}/g, " ")
                .replace(/\'/ig, "~::~\'")
                .split('~::~'),
            len = ar_by_quote.length,
            ar = [],
            deep = 0,
            tab = this.step,//+this.step,
            inComment = true,
            inQuote = false,
            parenthesisLevel = 0,
            str = '',
            ix = 0,
            shift = step ? createShiftArr(step) : this.shift;
        ;

        for (ix = 0; ix < len; ix++) {
            if (ix % 2) {
                ar = ar.concat(ar_by_quote[ix]);
            } else {
                ar = ar.concat(split_sql(ar_by_quote[ix], tab));
            }
        }

        len = ar.length;
        for (ix = 0; ix < len; ix++) {
            parenthesisLevel = isSubquery(ar[ix], parenthesisLevel);

            if (/\s{0,}\s{0,}SELECT\s{0,}/.exec(ar[ix])) {
                ar[ix] = ar[ix].replace(/\,/g, ",\n" + tab + tab + "")
            }

            if (/\s{0,}\s{0,}SET\s{0,}/.exec(ar[ix])) {
                ar[ix] = ar[ix].replace(/\,/g, ",\n" + tab + tab + "")
            }

            if (/\s{0,}\(\s{0,}SELECT\s{0,}/.exec(ar[ix])) {
                deep++;
                str += shift[deep] + ar[ix];
            } else if (/\'/.exec(ar[ix])) {
                if (parenthesisLevel < 1 && deep) {
                    deep--;
                }
                str += ar[ix];
            } else {
                str += shift[deep] + ar[ix];
                if (parenthesisLevel < 1 && deep) {
                    deep--;
                }
            }
            var junk = 0;
        }

        str = str.replace(/^\n{1,}/, '').replace(/\n{1,}/g, "\n");
        return str;
    }


    vkbeautify.prototype.xmlmin = function (text, preserveComments) {

        var str = preserveComments ? text
            : text.replace(/\<![ \r\n\t]*(--([^\-]|[\r\n]|-[^\-])*--[ \r\n\t]*)\>/g, "")
                .replace(/[ \r\n\t]{1,}xmlns/g, ' xmlns');
        return str.replace(/>\s{0,}</g, "><");
    }

    vkbeautify.prototype.jsonmin = function (text) {

        if (typeof JSON === 'undefined') {
            return text;
        }

        return JSON.stringify(JSON.parse(text), null, 0);

    }

    vkbeautify.prototype.cssmin = function (text, preserveComments) {

        var str = preserveComments ? text
            : text.replace(/\/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+\//g, "");

        return str.replace(/\s{1,}/g, ' ')
            .replace(/\{\s{1,}/g, "{")
            .replace(/\}\s{1,}/g, "}")
        .replace(/\;\s{1,}/g, ";")
        .replace(/\/\*\s{1,}/g, "/*")
        .replace(/\*\/\s{1,}/g, "*/");
        }

        vkbeautify.prototype.sqlmin = function (text) {
            return text.replace(/\s{1,}/g, " ").replace(/\s{1,}\(/, "(").replace(/\s{1,}\)/, ")");
        }

        window.vkbeautify = new vkbeautify();

    })();
</script>
<?php echo $footer; ?>
