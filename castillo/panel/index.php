<!doctype html>
<html ng-app="panel">
  <head>
    <script src="/castillo/panel/static/angular.min.js"></script>
    <script src="/castillo/panel/static/ui-bootstrap-tpls-0.12.1.min.js"></script>
    <script src="/castillo/panel/static/panel.js"></script>
    <script src="/castillo/panel/static/lodash.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/castillo/panel/static/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/castillo/panel/static/panel.css">
    <link rel="stylesheet" href="/castillo/panel/static/font-awesome-4.3.0/css/font-awesome.min.css">
  </head>
  <body ng-controller="PanelController">
  <div style="position:fixed; left:0; top:0; right:0; height:48px; background:#dedede">
    <ol class="breadcrumb" style="background:#dedede;line-height:32px;font-size:20px">
      <li ng-repeat="page in currentPage.parents">
        <a href="" ng-click="switchPage(page)"><i ng-if="page == content" class="fa fa-home fa-lg"></i>{{page.name}}</a>
      </li>
      <li>
        <a href=""><i ng-if="currentPage == content" class="fa fa-home fa-lg"></i>{{currentPage.name}}</a>
      </li>
    </ol>
  </div>

  <div style="position:absolute; left:0; width:200px; bottom:0; top:48px; background:#fff;padding:8px">
  
  <div class="list-group">
    <a class="list-group-item disabled"><b>
      PAGES</b>
    </a>
    <a href="" class="list-group-item" ng-if="currentPage.parent" ng-click="switchPage(currentPage.parent)">
      ..
    </a>
    <a ng-repeat="page in currentPage.children" href="#" class="list-group-item" 
      ng-if="page.type == 'directory'"
      ng-click="switchPage(page)" ng-bind="page.name">
    </a>
  </div>

  </div>

  <div style="position:absolute; left:200px; right:0; bottom:0; top:48px; background:#fff;padding:8px">
  
    <div class="panel panel-default">
      <div class="panel-body">
        <div>Properties</div>

        <hr />
        <div>
          <div panel-field field="currentPage.template.content"></div>
        </div>
        <hr />
        <div>
          <div panel-field field="currentPage.template.content.fields.created"></div>
        </div>
        <hr />

        <div>{{currentPage.content}}</div>
        <div>{{currentPage.template.content}}</div>
      </div>
    </div>

  </div>


    
  </body>
</html>


<script id="panelfield.html" type="text/ng-template">
<div>
  <div ng-switch="field.type">

    <div ng-switch-when="text">
    <form class="form-horizontal" role="form">
      <div class="form-group">
        <label for="inputfield" class="control-label">{{field.description}}</label>
        <input type="text" class="form-control" id="inputfield" placeholder="{{field.label}}">
      </div>      
    </form>
    </div>

    <div ng-switch-when="date">

      <p class="input-group">
        <input 
          type="text" 
          class="form-control" 
          datepicker-popup="DD.MM.YYYY" 
          ng-model="dt" 
          is-open="opened"
          ng-required="true"
          close-text="Close" />
        <span class="input-group-btn">
          <button 
            type="button" 
            class="btn btn-default" ng-click="open($event)"><i class="fa fa-calendar"></i></button>
        </span>
      </p>

    </div>

    <div ng-switch-default>
      <div ng-repeat="(key, value) in field.fields">
        <div panel-field field="value"></div>
      </div>
    </div>
  </div>
</div>
</script>