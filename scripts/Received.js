 var app = angular
   .module("myModule", [])
     .controller("myController", function($scope, $http, InventoryService){

         $scope.receivedParts = InventoryService.PartsInWarehouse;

         $scope.sortField = "DateReceived";
         $scope.sortDescend = false;

         $scope.ReceivePage = 'PartsReceived.html';

         $scope.sortColumn = function(sortBy){
             $scope.sortField = sortBy;
             $scope.sortDescend = !$scope.sortDescend;
         }

         $scope.AddEntry = function(fromClick){

             if(fromClick == 'new'){
                 lastEntry = $scope.receivedParts.slice(-1);
                 $scope.newEntry = {
                     id : lastEntry.id + 1,
                     RONum: "",
                     Location: "",
                     Customer: "",
                     Vehicle: "",
                     Technician: "",
                     ReceiveDate: new Date().getTime().toString(),
                     Vendor: "",
                     Notes: ""
                 };
                 $scope.ReceivePage = 'NewEntry.html';
             } else {
                 $scope.receivedParts.push($scope.newEntry);
                 AddDelivery($scope.newEntry)
                 $scope.ReceivePage = 'PartsReceived.html';
             }
         }

         function AddDelivery(newDelivery){
             $http.post('./php/Delivery.php', JSON.stringify(newDelivery))
                   .then(function(response) {
                            if (response.data){
                               console.log("New Delivery added successfully!");
                               console.log(response.data);
                               //window.location.href = 'PatientSearch.html';
                               //console.log(response.data);
                            }
                         },
                         function(response) {
                            console.log("Service does not Exists");
                            console.log(response.status);
                            console.log(response.statusText);
                            console.log(response.headers());
                         }
                   );   // .then()
         }

      })
      .directive('numbersOnly', function () {
          return {
              require: 'ngModel',
              link: function (scope, element, attr, ngModelCtrl) {
                  function fromUser(text) {
                      if (text) {
                          var transformedInput = text.replace(/[^0-9]/g, '');

                          if (transformedInput !== text) {
                              ngModelCtrl.$setViewValue(transformedInput);
                              ngModelCtrl.$render();
                          }
                          return transformedInput;
                      }
                      return undefined;
                  }
                  ngModelCtrl.$parsers.push(fromUser);
              }
          };
      });
