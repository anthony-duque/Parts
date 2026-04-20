
<!doctype html>
<html ng-app="myApp">
  <head >
    <script data-require="angular.js@1.6.2" data-semver="1.6.2" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.1/angular.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.6.1/angular-route.js"></script>

    <link rel="stylesheet" href="./styles/bootstrap.css" />
    <script src="scripts/bootstrap.bundle.min.js.js"></script>
 
    <script src="scripts/Route.js"></script>

    <script src='./scripts/Filters.js'></script>
    <script src='./scripts/UtilityFunctions.js'></script>

    <script src="scripts/Order_Materials.js"></script>

    <script src="scripts/Production.js"></script>
    <script src='./scripts/Estimator_View.js'></script>
    <script src='./scripts/Technician_View.js'></script>
    
     <!-- Deliveries Tab Controllers -->
    <script src='./scripts/Deliveries.js'></script>
    <script src='./scripts/DeliveriesByCar.js'></script>
    <script src='./scripts/DeliveriesByVendor.js'></script>

 
    <script src="scripts/Stage.js"></script>
    <script src="scripts/Parts_Search.js"></script>
    <script src="scripts/Return_Forms.js"></script>
    <script src="scripts/Follow_Up.js"></script>
    <script src="scripts/Vendors.js"></script>

  </head>
  <body >

   <nav class="navbar navbar-expand-lg navbar-light bg-primary bg-gradient">

        <button onclick="location.href='#/stage';" class="btn btn-light">
            Stage
        </button>

        &nbsp;
        
        <button onclick="location.href='#/production';" class="btn btn-secondary">
            Production
        </button>

        &nbsp;
        
        <button onclick="location.href='#/deliveries';" class="btn btn-success">
            Deliveries
        </button>

        &nbsp;
        
        <button onclick="location.href='#/parts-search';" class="btn btn-danger">
            Part Search
        </button>

        &nbsp;
        
        <button onclick="location.href='#/materials';" class="btn btn-warning">
            Materials
        </button>

        &nbsp;
        
        <button onclick="location.href='#/follow-up';" class="btn btn-info">
            Follow-up
        </button>

        &nbsp;
        
        <button onclick="location.href='#/return-forms';" class="btn btn-light">
            Return Forms
        </button>

        &nbsp;
        
        <button onclick="location.href='#/vendors';" class="btn btn-dark">
            Vendors
        </button>

    </nav>

<div class="content"  >  
  <ng-view>   </ng-view>
</div>

<!-- div class="footer">footer</div -->