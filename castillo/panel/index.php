<!doctype html>
<html ng-app>
  <head>
    <script src="/castillo/panel/static/angular.min.js"></script>
    <script src="/castillo/panel/static/ui-bootstrap-tpls-0.12.1.min.js"></script>
    <script src="/castillo/panel/static/panel.js"></script>
    <link rel="stylesheet" type="text/css" href="/castillo/panel/static/bootstrap.min.css">
  </head>
  <body>
    <div>
      <label>Name:</label>
      <input type="text" ng-model="yourName" placeholder="Enter a name here">
      <hr>
      <h1>Hello {{yourName}}!</h1>


      <p>
        <button class="btn btn-default btn-sm" ng-click="status.open = !status.open">Toggle last panel</button>
        <button class="btn btn-default btn-sm" ng-click="status.isFirstDisabled = ! status.isFirstDisabled">Enable / Disable first panel</button>
      </p>

    </div>
  </body>
</html>