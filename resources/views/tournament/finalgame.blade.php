<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script>
  var ua = navigator.userAgent.toLowerCase();
  var isiOS = (ua.indexOf('iphone') > -1) || (ua.indexOf('ipad') > -1);
  if(isiOS) {
    var viewport = document.querySelector('meta[name="viewport"]');
    if(viewport) {
      var viewportContent = viewport.getAttribute('content');
      viewport.setAttribute('content', viewportContent + ', user-scalable=no');
    }
  }
  </script>
  <title>Tournament</title>
  <!-- Copyright 1998-2021 by Northwoods Software Corporation. -->

  <!-- Fonts -->
  <link rel="dns-prefetch" href="//fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
  <link href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Noto+Sans+JP" rel="stylesheet">

  <!-- Styles -->
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
  <link href="{{ asset('css/multi-select.css') }}" rel="stylesheet">
  <link href="{{ asset('css/all2.css') }}" rel="stylesheet">

  <script src="https://cdn.ckeditor.com/4.5.6/standard/ckeditor.js"></script>
  <script src="{{ asset('js/app.js') }}" defer></script>
  <script src="{{ asset('js/all.js') }}" defer></script>
  <script src="{{ asset('js/menu.js') }}" defer></script>
  <script src="{{ asset('js/go.js') }}" defer></script>

  <!-- datetimepicker -->
  <link href="{{ asset('css/jquery.datetimepicker.css') }}" rel="stylesheet">
  <script src="{{ asset('js/jquery.js') }}" defer></script>
  <script src="{{ asset('js/jquery.datetimepicker.full.js') }}" defer></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="{{ asset('js/jquery.multi-select.js') }}" defer></script>  <script id="code">
      var winner = "";

      function init() {
        //if (window.goSamples) goSamples();  // init for these samples -- you don't need to call this
        var $ = go.GraphObject.make;  // for conciseness in defining templates

        myDiagram =
          $(go.Diagram, "myDiagramDiv",  // create a Diagram for the DIV HTML element
            {
              "textEditingTool.starting": go.TextEditingTool.SingleClick,
              "textEditingTool.textValidation": isValidScore,
              layout: $(go.TreeLayout, { angle: 180 }),
              "undoManager.isEnabled": false
            });

        // validation function for editing text
        function isValidScore(textblock, oldstr, newstr) {
          if (newstr === "") return true;
          var num = parseInt(newstr, 10);
          chk = !isNaN(num) && num >= 0 && num < {{ ($event->main_score + 1) }};
          if (chk == false) {
              alert('スコアは0~{{ $event->main_score }}を入力してください');
          }

          return chk;
        }

        // define a simple Node template
        myDiagram.nodeTemplate =
          $(go.Node, "Auto",
            { selectable: false },
            $(go.Shape, "RoundedRectangle",
              { fill: 'darkcyan'},
              new go.Binding("stroke", "", function (data) {
                  if (data.player1 == winner || data.player2 == winner){
                    return "orange"
                  }
                  return "darkcyan";
                }),
              new go.Binding("strokeWidth", "", function (data) {
                  if (data.player1 == winner || data.player2 == winner){
                    return 4;
                  }
                  return 1;
                }),
              // Shape.fill is bound to Node.data.color
              new go.Binding("fill", "color")),
            $(go.Panel, "Table",
              $(go.RowColumnDefinition, { column: 0, separatorStroke: "white" }),
              $(go.RowColumnDefinition, { column: 1, separatorStroke: "white", background: "darkcyan" }),
              $(go.RowColumnDefinition, { row: 0, separatorStroke: "white" }),
              $(go.RowColumnDefinition, { row: 1, separatorStroke: "white" }),
              $(go.TextBlock, "",
                {
                  row: 0,
                  wrap: go.TextBlock.None, margin: 5, width: 100,
                  isMultiline: false, textAlign: 'left',
                  font: '8pt  Segoe UI,sans-serif', stroke: 'white'
                },
                new go.Binding("text", "player1").makeTwoWay()),
              $(go.TextBlock, "",
                {
                  row: 1,
                  wrap: go.TextBlock.None, margin: 5, width: 100,
                  isMultiline: false, textAlign: 'left',
                  font: '8pt  Segoe UI,sans-serif', stroke: 'white'
                },
                new go.Binding("text", "player2").makeTwoWay()),
              $(go.TextBlock, "",
                {
                  column: 1, row: 0,
                  wrap: go.TextBlock.None, margin: 2, width: 25,
                  isMultiline: false, editable: false, textAlign: 'center',
                  font: '8pt  Segoe UI,sans-serif', stroke: 'white'
                },
                new go.Binding("text", "score1").makeTwoWay()),
              $(go.TextBlock, "",
                {
                  column: 1, row: 1,
                  wrap: go.TextBlock.None, margin: 2, width: 25,
                  isMultiline: false, editable: false, textAlign: 'center',
                  font: '8pt  Segoe UI,sans-serif', stroke: 'white'
                },
                new go.Binding("text", "score2").makeTwoWay())
            )
          );

        // define the Link template
        myDiagram.linkTemplate =
          $(go.Link,
            {
              routing: go.Link.Orthogonal,
              selectable: false
            },
            $(go.Shape,
              new go.Binding("strokeWidth", "", function (data) {
                  if (data.player1 == winner || data.player2 == winner){
                    return 4;
                  }
                  return 1;
                }),
              new go.Binding("stroke", "", function (data) {
                  if (data.player1 == winner || data.player2 == winner){
                    return "orange"
                  }
                  return "gray";
                }),
            )
          );

        // Generates the original graph from an array of player names
        function createPairs(players) {
          if (players.length % 2 !== 0) players.push('(empty)');
          var startingGroups = players.length / 2;
          var currentLevel = Math.ceil(Math.log(startingGroups) / Math.log(2));
          var levelGroups = [];
          var currentLevel = Math.ceil(Math.log(startingGroups) / Math.log(2));
          for (var i = 0; i < startingGroups; i++) {
            levelGroups.push(currentLevel + '-' + i);
          }
          var totalGroups = [];
          makeLevel(levelGroups, currentLevel, totalGroups, players);
          return totalGroups;
        }

        function makeLevel(levelGroups, currentLevel, totalGroups, players) {
          currentLevel--;
          var len = levelGroups.length;
          var parentKeys = [];
          var parentNumber = 0;
          var p = '';
          for (var i = 0; i < len; i++) {
            if (parentNumber === 0) {
              p = currentLevel + '-' + parentKeys.length;
              parentKeys.push(p);
            }

            if (players !== null) {
              var p1 = players[i * 2];
              var p2 = players[(i * 2) + 1];
              totalGroups.push({
                key: levelGroups[i], parent: p, player1: p1, player2: p2, parentNumber: parentNumber
              });
            } else {
              totalGroups.push({ key: levelGroups[i], parent: p, parentNumber: parentNumber });
            }

            parentNumber++;
            if (parentNumber > 1) parentNumber = 0;
          }

          // after the first created level there are no player names
          if (currentLevel >= 0) makeLevel(parentKeys, currentLevel, totalGroups, null)
        }

        function makeModel(players) {
          var model = new go.TreeModel(createPairs(players));

          model.addChangedListener(function(e) {
            if (e.propertyName !== 'score1' && e.propertyName !== 'score2') return;
            var data = e.object;
            if (isNaN(data.score1) || isNaN(data.score2)) return;

            var playerName = parseInt(data.score1) > parseInt(data.score2) ? data.player1 : data.player2;
            if (parseInt(data.score1) === parseInt(data.score2)) playerName = "";

            // TODO: What happens if score1 and score2 are the same number?

            // both score1 and score2 are numbers,
            // set the name of the higher-score'd player in the advancing (parent) node
            // if the data.parentNumber is 0, then we set player1 on the parent
            // if the data.parentNumber is 1, then we set player2
            var parent = myDiagram.findNodeForKey(data.parent);
            if (parent === null) {
              if (playerName !== "") {
                winner = playerName;
                myDiagram.rebuildParts();
              }
              return;
            }

            myDiagram.model.setDataProperty(parent.data, (data.parentNumber === 0 ? "player1" : "player2"), playerName);
          });

          myDiagram.model = model;

          @foreach($scores as $k => $val)
            var d = model.nodeDataArray[{{$k}}];
            var score = "{{$val[0] ?? ''}}";
            if (score != '') {
              model.setDataProperty(d, "score1", "{{$val[0] ?? ''}}");
              model.setDataProperty(d, "score2", "{{$val[1] ?? ''}}");
            }
          @endforeach
        }

        makeModel([
          @foreach($teams as $val)
            "{{ $val['name'] }}",
          @endforeach
        ]);
      } // end init
    </script>
</head>
<body onload="init()">
    <div id="app" class="card-body @if ($isMobile) grid-area @endif">
      @include('elements.flash_message')
      <div class="container-fluid @if ($isMobile) top-area @endif">
        @if ($isMobile)
          @include('tournament/nav_sp')
        @else
          @include('tournament/nav')
        @endif
        @if (Auth::user()->role != config('user.role.member') || (!$isMobile && isset($member)))
          <a href="{{ route('game.finalResultlist') }}" class="btn btn-info btn-sm mt-1 ml-4">報告一覧</a>
          <a href="{{ route('game.finalResult') }}" class="btn btn-success btn-sm mt-1">報告</a>
        @endif
      </div>
        <!--
        <div id="sample">
        -->
        @if ($event->passing_order == 1)
          @if ($isMobile)
            <div id="myDiagramDiv" class="bottom-area" style="border: solid 0px black; background: whitesmoke; width:100%;"></div>
          @else
            <div id="myDiagramDiv" style="border: solid 0px black; background: whitesmoke; width:800px; height:{{count($teams) * 40}}px;"></div>
          @endif
        @else
          @if ($isMobile)
            <div id="myDiagramDiv" class="bottom-area" style="border: solid 0px black; background: whitesmoke; width:100%;"></div>
          @else
            <div id="myDiagramDiv" style="border: solid 0px black; background: whitesmoke; width:1000px; height:{{count($teams) * 40}}px;"></div>
          @endif
        @endif
        <!--
        </div>
      -->
        <input type="hidden" id="url" value="{{ config('user.url') }}">
    </div>
</body>
</html>
