<!doctype>
<html>
<head>
    <title>jsPDF</title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="stylesheet" type="text/css" href="/js/jsPDF/examples/css/smoothness/jquery-ui-1.8.17.custom.css">
    <link rel="stylesheet" type="text/css" href="/js/jsPDF/examples/css/main.css">

    <script type="text/javascript" src="/js/jsPDF/examples/js/jquery/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="/js/jsPDF/examples/js/jquery/jquery-ui-1.8.17.custom.min.js"></script>
    <script type="text/javascript" src="/js/jsPDF/dist/jspdf.debug.js"></script>

    <script>
        function demoFromHTML() {
            var pdf = new jsPDF('p', 'pt', 'letter')

            // source can be HTML-formatted string, or a reference
            // to an actual DOM element from which the text will be scraped.
                , source = $('#fromHTMLtestdiv')[0]

            // we support special element handlers. Register them with jQuery-style
            // ID selector for either ID or node name. ("#iAmID", "div", "span" etc.)
            // There is no support for any other type of selectors
            // (class, of compound) at this time.
                , specialElementHandlers = {
                    // element with id of "bypass" - jQuery style selector
                    '#bypassme': function(element, renderer){
                        // true = "handled elsewhere, bypass text extraction"
                        return true
                    }
                }

            margins = {
                top: 80,
                bottom: 60,
                left: 40,
                width: 522
            };
            // all coords and widths are in jsPDF instance's declared units
            // 'inches' in this case
            pdf.fromHTML(
                source // HTML string or DOM elem ref.
                , margins.left // x coord
                , margins.top // y coord
                , {
                    'width': margins.width // max width of content on PDF
                    , 'elementHandlers': specialElementHandlers
                },
                function (dispose) {
                    // dispose: object with X, Y of the last line add to the PDF
                    //          this allow the insertion of new lines after html
                    pdf.save('Test.pdf');
                },
                margins
            )
        }
        $(function() {
            $(".button").button();
        });
    </script>
</head>

<body>

<h1>jsPDF Demos</h1>

<div class="to_pdf">
    <div><p>This (BETA level. API is subject to change!) plugin allows one to scrape formatted text from an HTML fragment into PDF. Font size, styles are copied. The long-running text is split to stated content width.</p></div>
    <div style="border-width: 2px; border-style: dotted; padding: 1em; font-size:120%;line-height: 1.5em;" id="fromHTMLtestdiv">
        <h2 style="font-size:120%">Header Two</h2>
        <strong><em>Double      style span</em></strong>
<span style="font-family:monospace">Monotype span with
carriage return. </span><span style="font-size:300%">a humongous font size span.</span>
        Followed by long parent-less text node. asdf qwer asdf zxcv qsasfd qwer qwasfd zcxv sdf qwer qwe sdf wer qwer asdf zxv.
        <div <span style="font-family:serif">Serif Inner DIV (bad markup, but testing block detection)</div><span style="font-family:sans-serif">  Sans-serif span with extra spaces    </span>
    Followed by text node without any wrapping element. <span>And some long long text span attached at the end to test line wrap. qwer asdf qwer lkjh asdf zxvc safd qwer wwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwwww qewr asdf zxcv.</span>
    <p style="font-size:120%">This is a <em style="font-size:120%">new</em> paragraph.</p>
    This is more wrapping-less text.
    <p id="bypassme" style="font-size:120%">This paragraph will <strong style="font-size:120%">NOT</strong> be on resulting PDF because a special attached element handler will be looking for the ID - 'bypassme' - and should bypass rendering it.</p>
    <p style="font-size:120%;text-align:center">This is <strong style="font-size:120%">another</strong> paragraph.</p>
    <p style="text-align:justify">
        Integer dignissim urna tortor? Cum rhoncus, a lacus ultricies tincidunt, tristique lundium enim urna, magna? Sed, enim penatibus? Lacus pellentesque integer et pulvinar tortor? Dapibus in arcu arcu, vut dolor? Et! Placerat pulvinar cursus, urna ultrices arcu nunc, a ultrices dictumst elementum? Magnis rhoncus pellentesque, egestas enim purus, augue et nascetur sociis enim rhoncus. Adipiscing augue placerat tincidunt pulvinar ridiculus. Porta in sociis arcu et placerat augue sit enim nec hac massa, turpis ridiculus nunc phasellus pulvinar proin sit pulvinar, ultrices aliquet placerat amet? Lorem nunc porttitor etiam risus tempor placerat amet non hac, nunc sed odio augue? Turpis, magnis. Lorem pid, a porttitor tincidunt adipiscing sagittis pellentesque, mattis amet, duis proin, penatibus lectus lorem eros, nisi, tempor phasellus, elit.
    </p>
    <h2>Image Support</h2>
    <p>
        NOTES: the img src must be on the same domain or the external domain should allow Cross-origin.
    </p>
    <img src="http://maps.googleapis.com/maps/api/staticmap?center=Brooklyn+Bridge,New+York,NY&zoom=13&size=400x300&scale=1&maptype=roadmap&markers=color:blue%7Clabel:S%7C40.702147,-74.015794&markers=color:green%7Clabel:G%7C40.711614,-74.012318&markers=color:red%7Ccolor:red%7Clabel:C%7C40.718217,-73.998284&sensor=false" width="400" height="300">
    <!-- ADD_PAGE -->
    <h2>New page added with html comment: ADD_PAGE</h2>
    <h2></h2>
    <p>HTML Table:</p>
    <p>
        NOTES: Must set the COLGROUP tag with "with" on each COL tag as %, inspect the table. BTW the css does not have a good style to render the table on the html :P, feel free to the add the CSS.
    </p>
    <table>
        <colgroup>
            <col width="60%">
            <col width="40%">
        </colgroup>
        <thead>
        <tr>
            <th>
                Heading1
            </th>
            <th>
                Heading2
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                cell 1,1
            </td>
            <td>
                cell 1,2
            </td>
        </tr>
        <tr>
            <td>
                cell 2,1
            </td>
            <td>
                cell 2,2
            </td>
        </tr>
        <tr>
            <td>
                cell 3,1
            </td>
            <td>
                cell 3,2
            </td>
        </tr>
        <tr>
            <td>
                cell 4,1
            </td>
            <td>
                cell 4,2
            </td>
        </tr>
        </tbody>
    </table>
    <h2></h2>
    <h2></h2>
    <p>HTML Lists:</p>
    <div style="margin-left:20px">
        <ul>
            <li>Lorem Ipsum</li>
            <li>Dolor Sit amen</li>
            <li>Lorem Ipsum</li>
            <li>Dolor Sit amen</li>
        </ul>
        <ol>
            <li>Lorem Ipsum</li>
            <li>Dolor Sit amen</li>
            <li>Lorem Ipsum</li>
            <li>Dolor Sit amen</li>
        </ol>
    </div>
</div>
<div><p>
    <button onclick="javascript:demoFromHTML()" class="button">Run Code</button></p>
</div>



</body>
</html>
