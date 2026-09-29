var app = angular.module("DefineShopStagesApp", []);

var defineShopStagesCtrlr = function($scope, $http){

    $scope.shops = [
        { shop_id: 1, name: "World Class Collision I"},
        { shop_id: 2, name: "World Class Collision II"},
    ];

    $scope.stageHeadings = [
        { id: 1,    name: "Check-In/Pre-Scan"},
        { id: 2,    name: "Disassembly"},
        { id: 3,    name: "Repair Plan"},
        { id: 4,    name: "Waiting Approval"},
        { id: 5,    name: "Waiting for Parts"},
        { id: 6,    name: "Body"},
        { id: 7,    name: "Primer"},
        { id: 8,    name: "Paint"},
        { id: 9,    name: "Delay"},
        { id: 10,   name: "Reassembly"},
        { id: 11,   name: "Sublet"},
        { id: 12,   name: "Final QC"},
        { id: 13,   name: "Ready for Delivery"},
    ];

    $scope.companyStages = [];
    $scope.stageSelected = null;


    $scope.addHeadingToCompany = function(stage){

            // add the heading to the company
        $scope.companyStages.push(stage);

            // remove it from the stage heading list
        $scope.stageHeadings = $scope.stageHeadings.filter(heading => heading.id !== stage.id);
    };


    $scope.removeCompanyHeading = function(stage){

        $scope.stageHeadings.push(stage);

            // remove it from the stage heading list
        $scope.companyStages = $scope.companyStages.filter(heading => heading.id !== stage.id);
    };


    $scope.selectStage = function(stage){

        if (!$scope.stageSelected){
            $scope.stageSelected = stage;
        } else {
            $scope.stageSelected = null;
        }
    };


        // move up the selected stage in the list
    $scope.moveStageUp = function(selectedID){

        const index = $scope.companyStages.findIndex(stage => stage.id === selectedID);
        
        if (index == 0){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.companyStages[index] = $scope.companyStages[index - 1];
            $scope.companyStages[index - 1] = $scope.stageSelected;
        }
    };


        // move down the selected stage in the list
    $scope.moveStageDown = function(selectedID){

        const index = $scope.companyStages.findIndex(stage => stage.id === selectedID);
        
        if (index == ($scope.companyStages.length - 1)){
            
            return;     // if there is only one stage do nothing

        } else {

            $scope.companyStages[index] = $scope.companyStages[index + 1];
            $scope.companyStages[index + 1] = $scope.stageSelected;
        }
    };

}   // defineShopStagesCtrlr()

app.controller("DefineShopStagesCtrlr", defineShopStagesCtrlr);